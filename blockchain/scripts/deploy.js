const hre = require("hardhat");

async function main() {
  console.log("Deploying MediFund Smart Contracts...\n");

  // 1. Deploy Campaign Contract
  const Campaign = await hre.ethers.getContractFactory("MediFundCampaign");
  const campaign = await Campaign.deploy();
  await campaign.waitForDeployment();
  const campaignAddr = await campaign.getAddress();
  console.log("MediFundCampaign deployed to:", campaignAddr);

  // 2. Deploy Escrow Contract
  const Escrow = await hre.ethers.getContractFactory("MediFundEscrow");
  const escrow = await Escrow.deploy();
  await escrow.waitForDeployment();
  const escrowAddr = await escrow.getAddress();
  console.log("MediFundEscrow deployed to:", escrowAddr);

  // 3. Deploy Donation Contract (with escrow address)
  const Donation = await hre.ethers.getContractFactory("MediFundDonation");
  const donation = await Donation.deploy(escrowAddr);
  await donation.waitForDeployment();
  const donationAddr = await donation.getAddress();
  console.log("MediFundDonation deployed to:", donationAddr);

  // 4. Deploy Verification Contract
  const Verification = await hre.ethers.getContractFactory("MediFundVerification");
  const verification = await Verification.deploy();
  await verification.waitForDeployment();
  const verificationAddr = await verification.getAddress();
  console.log("MediFundVerification deployed to:", verificationAddr);

  // 5. Wire the fraud gate: donations only accepted for campaigns the
  //    fraud engine cleared (structural coupling, Section 2.2.3 of the thesis).
  const tx = await donation.setCampaignContract(campaignAddr);
  await tx.wait();
  console.log("Fraud gate wired: MediFundDonation now checks MediFundCampaign.isDonatable()");

  // 6. Authorize the donation contract to write raisedAmount + donor ledger
  const tx2 = await campaign.setDonationSource(donationAddr);
  await tx2.wait();
  console.log("Donation source wired: MediFundCampaign.recordDonation() authorized");

  // Save deployment addresses
  const fs = require("fs");
  const deployments = {
    network: hre.network.name,
    chainId: hre.network.config.chainId,
    deployedAt: new Date().toISOString(),
    contracts: {
      MediFundCampaign: campaignAddr,
      MediFundDonation: donationAddr,
      MediFundEscrow: escrowAddr,
      MediFundVerification: verificationAddr
    }
  };

  fs.writeFileSync(
    "./deployments.json",
    JSON.stringify(deployments, null, 2)
  );
  console.log("\nDeployment addresses saved to deployments.json");

  console.log("\n--- Deployment Summary ---");
  console.log(JSON.stringify(deployments, null, 2));
}

main()
  .then(() => process.exit(0))
  .catch((error) => {
    console.error(error);
    process.exit(1);
  });
