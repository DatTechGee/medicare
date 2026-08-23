const hre = require("hardhat");

async function main() {
  const fs = require("fs");
  const d = JSON.parse(fs.readFileSync(__dirname + "/../deployments.json", "utf8"));
  const c = await hre.ethers.getContractAt("MediFundCampaign", d.contracts.MediFundCampaign);

  console.log(
    "counter=",
    (await c.campaignCounter()).toString(),
    "donatable#1=",
    await c.isDonatable(1),
    "donatable#6=",
    await c.isDonatable(6)
  );
}

main().catch((e) => { console.error(e); process.exitCode = 1; });
