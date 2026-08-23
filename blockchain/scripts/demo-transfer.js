const hre = require("hardhat");
const fs = require("fs");

async function main() {
  const dep = JSON.parse(fs.readFileSync(__dirname + "/../deployments.json", "utf8"));
  const [owner, donor] = await hre.ethers.getSigners();
  const campaignId = 1;

  const donation = await hre.ethers.getContractAt("MediFundDonation", dep.contracts.MediFundDonation);
  const escrow = await hre.ethers.getContractAt("MediFundEscrow", dep.contracts.MediFundEscrow);
  const mfc = await hre.ethers.getContractAt("MediFundCampaign", dep.contracts.MediFundCampaign);

  const benAddr = (await mfc.getCampaign(campaignId)).beneficiary;
  console.log("=== DEMO TRANSFER: donor -> escrow -> beneficiary ===");

  /* 1. DONOR donates 0.05 ETH */
  const amt = hre.ethers.parseEther("0.05");
  const dtx = await donation.connect(donor).donate(campaignId, { value: amt });
  await dtx.wait();
  console.log("1) DONOR " + donor.address);
  console.log("   donated 0.05 ETH to campaign #" + campaignId);

  /* 2. Registry recorded it */
  const c = await mfc.getCampaign(campaignId);
  console.log("2) ON-CHAIN raisedAmount:", hre.ethers.formatEther(c.raisedAmount), "ETH",
    "| donors:", (await mfc.getDonors(campaignId)).length,
    "| this donor total:", hre.ethers.formatEther(await mfc.getDonorAmount(campaignId, donor.address)), "ETH");

  /* 3. ESCROW holds it */
  console.log("3) ESCROW holds:", hre.ethers.formatEther(await escrow.getCampaignEscrowBalance(campaignId)), "ETH");

  /* 4. PATIENT payout */
  const before = await hre.ethers.provider.getBalance(benAddr);
  const rtx = await escrow.releaseFunds(campaignId, amt, "Patient payout - verified treatment invoice");
  await rtx.wait();
  const after = await hre.ethers.provider.getBalance(benAddr);
  console.log("4) BENEFICIARY " + benAddr);
  console.log("   received:", hre.ethers.formatEther(after - before), "ETH | escrow left:",
    hre.ethers.formatEther(await escrow.getCampaignEscrowBalance(campaignId)), "ETH");
  console.log("=== FULL CYCLE OK ===");
}

main().catch((e) => { console.error(e.shortMessage || e.message); process.exitCode = 1; });
