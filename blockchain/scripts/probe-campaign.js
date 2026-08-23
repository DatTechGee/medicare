const hre = require("hardhat");
const fs = require("fs");

async function main() {
  const dep = JSON.parse(fs.readFileSync(__dirname + "/../deployments.json", "utf8"));
  console.log("campaign addr:", dep.contracts.MediFundCampaign);
  const code = await hre.ethers.provider.getCode(dep.contracts.MediFundCampaign);
  console.log("code size:", code.length);
  const mfc = await hre.ethers.getContractAt("MediFundCampaign", dep.contracts.MediFundCampaign);
  console.log("has donationSource:", await mfc.donationSource());
  try {
    const c = await mfc.campaigns(1);
    console.log("campaigns(1) title:", c.title, "raised:", c.raisedAmount ? c.raisedAmount.toString() : "?");
  } catch (e) {
    console.log("campaigns(1) FAILED:", e.shortMessage || e.message);
  }
}

main().catch((e) => { console.error(e); process.exitCode = 1; });
