const fs = require("fs");
const ethers = require("C:/xampp/htdocs/fundorex/blockchain/node_modules/ethers");

(async () => {
  const cfg = JSON.parse(fs.readFileSync("C:/xampp/htdocs/fundorex/@core/public/assets/blockchain/medifund-contracts.json", "utf8"));
  const provider = new ethers.JsonRpcProvider(cfg.rpcUrl, { chainId: cfg.chainId, name: cfg.chainName }, { staticNetwork: true, batchMaxCount: 1 });

  const campaign = new ethers.Contract(cfg.contracts.MediFundCampaign.address, cfg.contracts.MediFundCampaign.abi, provider);
  const donation = new ethers.Contract(cfg.contracts.MediFundDonation.address, cfg.contracts.MediFundDonation.abi, provider);
  const escrow = new ethers.Contract(cfg.contracts.MediFundEscrow.address, cfg.contracts.MediFundEscrow.abi, provider);

  console.log("isDonatable(1):", await campaign.isDonatable(1));
  console.log("escrowBalance(1):", (await escrow.campaignEscrowBalance(1)).toString());
  console.log("getDonationCount(1):", (await donation.getDonationCount(1)).toString());
  console.log("getCampaignDonations(1): ok");
  const ids = await donation.getCampaignDonations(1);
  for (const id of ids.slice(0, 5)) {
    const d = await donation.getDonation(id);
    console.log("  donation", id.toString(), "amount:", d.amount ? d.amount.toString() : "?");
  }
  console.log("ALL CALLS OK — no unrecognized-selector");
})().catch(e => { console.error("FAIL:", e.shortMessage || e.message); process.exit(1); });

