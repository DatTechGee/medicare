/**
 * Syncs campaigns from the MediFund Laravel app into the deployed
 * MediFundCampaign contract so on-chain donations never revert with
 * "Campaign not open for donations".
 *
 * Usage:
 *   1. Start the app:        php artisan serve          (in @core)
 *   2. Start local chain:    npx hardhat node           (chainId 31337)
 *   3. Run this sync:        npx hardhat run scripts/sync-campaigns.js --network localhost
 *
 * The script asks the Laravel API for each campaign id (1,2,3,...) and
 * registers any that are missing on-chain. Chain campaign ids match DB ids.
 */
const hre = require("hardhat");

const APP_URL = process.env.MEDIFUND_APP_URL || "http://127.0.0.1:8000";
const BENEFICIARY =
  process.env.MEDIFUND_BENEFICIARY || "0x80354450F4c300F178de2Ab718AbA6D2818CE102";
/** USD per ETH used by the app for display conversions. */
const USD_PER_ETH = parseFloat(process.env.MEDIFUND_USD_PER_ETH || "3450");
/** On-chain fraud gate threshold (must match MediFundCampaign.sol). */
const FRAUD_GATE_THRESHOLD = 50;
const THIRTY_DAYS = 30 * 24 * 60 * 60;

async function main() {
  const { ethers } = hre;
  const deployments = JSON.parse(
    require("fs").readFileSync(__dirname + "/../deployments.json", "utf8")
  );
  const campaignAddr = deployments.contracts.MediFundCampaign;

  const campaign = await ethers.getContractAt("MediFundCampaign", campaignAddr);
  const escrow = await ethers.getContractAt(
    "MediFundEscrow",
    deployments.contracts.MediFundEscrow
  );
  const [deployer] = await ethers.getSigners();
  console.log("Syncer account:", deployer.address);
  console.log("MediFundCampaign:", campaignAddr);
  console.log("MediFundEscrow:  ", deployments.contracts.MediFundEscrow);
  console.log("App API:", APP_URL, "\n");

  let registered = 0;
  let skipped = 0;
  let missing = 0;

  for (let id = 1; id <= 100; id++) {
    // Fetch campaign data from the Laravel API; stop at first missing id.
    let api;
    try {
      const res = await fetch(`${APP_URL}/api/blockchain/campaign/${id}`);
      if (!res.ok) break;
      api = await res.json();
    } catch (e) {
      console.log(`Could not reach app API (${e.message}). Is php artisan serve running?`);
      break;
    }

    let onChain;
    try {
      await campaign.getCampaign(id);
      onChain = true;
    } catch (e) {
      onChain = false;
    }

    if (onChain) {
      skipped++;
      /* backfill escrow beneficiary if an older sync missed it */
      try {
        const cur = await escrow.campaignBeneficiary(id);
        if (cur === ethers.ZeroAddress) {
          const etx = await escrow.setCampaignBeneficiary(id, BENEFICIARY);
          await etx.wait();
          console.log(`Backfilled escrow beneficiary for #${id}`);
        }
      } catch (e) {}
      continue;
    }

    const goalUsd = parseFloat(api.campaign.amount) || 100;
    const goalWei = ethers.parseEther((goalUsd / USD_PER_ETH).toFixed(6));
    const fraudScore = Math.min(49, Math.max(0, parseInt(api.campaign.fraud_score) || 0));
    const deadline = Math.floor(Date.now() / 1000) + THIRTY_DAYS;

    const tx = await campaign.createCampaign(
      String(api.campaign.title).slice(0, 64),
      goalWei,
      BENEFICIARY,
      String(api.campaign.patient_name || "Patient").slice(0, 64),
      String(api.campaign.hospital_name || "Hospital").slice(0, 64),
      "Synced from MediFund app",
      deadline,
      fraudScore
    );
    await tx.wait();

    /* The escrow contract keeps its own beneficiary mapping; without this,
       releaseFunds() would pay nobody (silent skip on address(0)). */
    const etx = await escrow.setCampaignBeneficiary(id, BENEFICIARY);
    await etx.wait();

    registered++;
    console.log(`Registered campaign #${id}: "${api.campaign.title}" (goal ${goalUsd} USD, fraudScore ${fraudScore}) tx=${tx.hash}`);
    await new Promise((r) => setTimeout(r, 150)); // gentle pace for the local node
    void missing;
  }

  console.log(`\nDone. registered=${registered} alreadyOnChain=${skipped}`);
}

main().catch((error) => {
  console.error(error);
  process.exitCode = 1;
});
