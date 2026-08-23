const hre = require("hardhat");
const fs = require("fs");

async function main() {
  const dep = JSON.parse(fs.readFileSync(__dirname + "/../deployments.json", "utf8"));
  const [owner, donor] = await hre.ethers.getSigners();
  const donation = await hre.ethers.getContractAt("MediFundDonation", dep.contracts.MediFundDonation);
  const mfc = await hre.ethers.getContractAt("MediFundCampaign", dep.contracts.MediFundCampaign);
  const id = 6; /* seeded as suspicious (fraud_score 49) */

  console.log("=== FRAUD DETECTION DEMO (campaign #" + id + ") ===");
  let c = await mfc.campaigns(id);
  console.log("before flag: fraudScore=" + c.fraudScore + " status=" + c.status + " donatable=" + await mfc.isDonatable(id));

  /* Admin flags the campaign with score >= 50 */
  const ftx = await mfc.connect(owner).flagCampaign(id, 85);
  const rcpt = await ftx.wait();
  const evt = rcpt.logs.map(l => { try { return mfc.interface.parseLog(l); } catch (e) { return null; } }).filter(Boolean).find(e => e && e.name === "FraudFlagged");
  c = await mfc.campaigns(id);
  console.log("admin flags score=85 -> FraudFlagged event:", evt ? `id=${evt.args[0]} score=${evt.args[1]}` : "(none)");
  console.log("after flag : fraudScore=" + c.fraudScore + " status=" + c.status + " donatable=" + await mfc.isDonatable(id));

  /* Donor now tries to donate -> must revert */
  try {
    const tx = await donation.connect(donor).donate(id, { value: hre.ethers.parseEther("0.01") });
    await tx.wait();
    console.log("donation attempt: UNEXPECTEDLY SUCCEEDED");
  } catch (e) {
    console.log("donation attempt blocked by contract:", (e.reason || e.shortMessage || String(e)).slice(0, 90));
  }

  /* Admin clears it again so the demo site stays usable */
  await mfc.connect(owner).flagCampaign(id, 49);
  console.log("admin restores score=49 -> donatable=" + await mfc.isDonatable(id));
}

main().catch((e) => { console.error(e); process.exitCode = 1; });
