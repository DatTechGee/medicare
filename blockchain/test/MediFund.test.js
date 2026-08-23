/**
 * MediFund DApp - Smart Contract Test Suite
 *
 * Functional coverage mapped to the research algorithms:
 *   Algorithm 1 - Creating a Campaign and Checking for Fraud ......... MediFundCampaign
 *   Algorithm 2 - Receiving Donations Safely ......................... MediFundDonation + MediFundEscrow
 *   Algorithm 3 - Releasing the Money ................................ MediFundEscrow (milestone/refund)
 *   Hospital / Patient / Document verification ....................... MediFundVerification
 *   Adversarial conditions (Section 3.3 data collection):
 *     - donations to fraud-flagged campaigns must be impossible
 *     - unauthorised releases/refunds must revert
 *     - over-release, double-refund, empty-milestone must revert
 *     - re-entrancy against the payout flow must not duplicate payouts
 */

const { loadFixture } = require("@nomicfoundation/hardhat-toolbox/network-helpers");
const { expect } = require("chai");
const hre = require("hardhat");
const { ethers } = hre;

const ONE_ETH = ethers.parseEther("1");

describe("MediFund - full system", function () {
  async function deployFixture() {
    const [admin, patient, patient2, donor1, donor2, donor3, other] =
      await ethers.getSigners();

    const Escrow = await ethers.getContractFactory("MediFundEscrow", admin);
    const escrow = await Escrow.deploy();
    await escrow.waitForDeployment();

    const Campaign = await ethers.getContractFactory("MediFundCampaign", admin);
    const campaign = await Campaign.deploy();
    await campaign.waitForDeployment();

    const Donation = await ethers.getContractFactory("MediFundDonation", admin);
    const donation = await Donation.deploy(await escrow.getAddress());
    await donation.waitForDeployment();

    const Verification = await ethers.getContractFactory("MediFundVerification", admin);
    const verification = await Verification.deploy();
    await verification.waitForDeployment();

    // wire the structural fraud gate (donation <-> campaign registry)
    await donation.setCampaignContract(await campaign.getAddress());

    return { admin, patient, patient2, donor1, donor2, donor3, other,
             escrow, campaign, donation, verification };
  }

  /** Helper: register one campaign directly on the registry. */
  async function createCampaign(campaign, beneficiary, overrides = {}) {
    const args = [
      overrides.title ?? "Emergency Open-Heart Surgery",
      overrides.goal ?? ethers.parseEther("10"),
      beneficiary,
      overrides.patientName ?? "Rahim Uddin",
      overrides.hospitalName ?? "NSUK Teaching Hospital",
      overrides.medicalDetails ?? "Cardiac surgery - verified invoice #A-102",
      overrides.deadline ?? 0n,
      "fraudScore" in overrides ? overrides.fraudScore : 10n,
    ];
    return campaign.createCampaign(...args);
  }

  // ------------------------------------------------------------------
  // ALGORITHM 1 - campaign creation gated by the fraud engine's score
  // ------------------------------------------------------------------
  describe("Algorithm 1: campaign creation & fraud gate", function () {
    it("deploys with deployer as owner", async function () {
      const { campaign, admin } = await loadFixture(deployFixture);
      expect(await campaign.owner()).to.equal(admin.address);
      expect(await campaign.FRAUD_GATE_THRESHOLD()).to.equal(50n);
    });

    it("stores a low-risk campaign as Active and emits CampaignCreated", async function () {
      const { campaign, patient } = await loadFixture(deployFixture);
      await expect(createCampaign(campaign, patient))
        .to.emit(campaign, "CampaignCreated")
        .withArgs(1n, patient.address, ethers.parseEther("10"));

      const c = await campaign.getCampaign(1n);
      expect(c.id).to.equal(1n);
      expect(c.title).to.equal("Emergency Open-Heart Surgery");
      expect(c.goalAmount).to.equal(ethers.parseEther("10"));
      expect(c.fraudScore).to.equal(10n);
      expect(c.status).to.equal(0n); // Active
      expect(c.hospitalVerified).to.equal(false);
    });

    it("stores a high-risk campaign as Flagged at creation (score >= 50)", async function () {
      const { campaign, patient } = await loadFixture(deployFixture);
      await expect(createCampaign(campaign, patient, { fraudScore: 87n }))
        .to.emit(campaign, "FraudFlagged")
        .withArgs(1n, 87n);

      const c = await campaign.getCampaign(1n);
      expect(c.status).to.equal(3n); // Flagged
      expect(c.fraudScore).to.equal(87n);
    });

    it("a campaign created exactly at the threshold is blocked", async function () {
      const { campaign, patient } = await loadFixture(deployFixture);
      await createCampaign(campaign, patient, { fraudScore: 50n });
      expect(await campaign.isDonatable(1n)).to.equal(false);
    });

    it("rejects invalid campaign inputs", async function () {
      const { campaign, patient } = await loadFixture(deployFixture);
      await expect(createCampaign(campaign, patient, { goal: 0n }))
        .to.be.revertedWith("Goal must be > 0");
      await expect(createCampaign(campaign, ethers.ZeroAddress))
        .to.be.revertedWith("Invalid beneficiary");
      await expect(createCampaign(campaign, patient, { fraudScore: 101n }))
        .to.be.revertedWith("Invalid fraud score");
    });

    it("owner can flag an existing live campaign with a new score", async function () {
      const { campaign, patient } = await loadFixture(deployFixture);
      await createCampaign(campaign, patient);
      await expect(campaign.flagCampaign(1n, 95n))
        .to.emit(campaign, "FraudFlagged")
        .withArgs(1n, 95n);
      const c = await campaign.getCampaign(1n);
      expect(c.status).to.equal(3n); // Flagged
      expect(await campaign.isDonatable(1n)).to.equal(false);
    });

    it("owner can record hospital/patient verification and change status", async function () {
      const { campaign, patient } = await loadFixture(deployFixture);
      await createCampaign(campaign, patient);

      await expect(campaign.verifyHospital(1n, true)).to.emit(campaign, "HospitalVerified").withArgs(1n, true);
      await expect(campaign.verifyPatient(1n, true)).to.emit(campaign, "PatientVerified").withArgs(1n, true);

      const c = await campaign.getCampaign(1n);
      expect(c.hospitalVerified).to.equal(true);
      expect(c.patientVerified).to.equal(true);

      await campaign.updateCampaignStatus(1n, 1n); // Completed
      expect((await campaign.getCampaign(1n)).status).to.equal(1n);
      expect(await campaign.isDonatable(1n)).to.equal(false);
    });

    it("non-owners cannot flag, verify, update or create through admin paths", async function () {
      const { campaign, other, patient } = await loadFixture(deployFixture);
      await createCampaign(campaign, patient);

      await expect(campaign.connect(other).flagCampaign(1n, 99n)).to.be.reverted;
      await expect(campaign.connect(other).verifyHospital(1n, true)).to.be.reverted;
      await expect(campaign.connect(other).verifyPatient(1n, true)).to.be.reverted;
      await expect(
        campaign.connect(other).updateCampaignStatus(1n, 2n)
      ).to.be.reverted;

      // invalid ids are rejected even for the owner
      await expect(campaign.flagCampaign(999n, 99n)).to.be.revertedWith("Invalid campaign");

      // sanity: beneficiary stored correctly
      const c = await campaign.getCampaign(1n);
      expect(c.beneficiary).to.equal(patient.address);
    });
  });

  // ------------------------------------------------------------------
  // ALGORITHM 2 - donations held in escrow, recorded on-chain
  // ------------------------------------------------------------------
  describe("Algorithm 2: safe donation custody", function () {
    it("records a donation and forwards the ETH into escrow", async function () {
      const { campaign, donation, escrow, donor1 } = await loadFixture(deployFixture);
      await createCampaign(campaign, donor1);

      const amount = ethers.parseEther("2.5");
      await expect(donation.connect(donor1).donate(1n, { value: amount }))
        .to.emit(donation, "DonationMade")
        .withArgs(1n, 1n, donor1.address, amount);

      const d = await donation.getDonation(1n);
      expect(d.donor).to.equal(donor1.address);
      expect(d.amount).to.equal(amount);
      expect(d.refunded).to.equal(false);

      expect(await donation.getDonationCount(1n)).to.equal(1n);
      expect(await escrow.campaignEscrowBalance(1n)).to.equal(amount);
      expect(await escrow.totalHeld()).to.equal(amount);
      expect(await ethers.provider.getBalance(await escrow.getAddress())).to.equal(amount);
    });

    it("accumulates multiple donors' contributions under the same campaign", async function () {
      const { campaign, donation, escrow, donor1, donor2, donor3 } = await loadFixture(deployFixture);
      await createCampaign(campaign, donor1);

      await donation.connect(donor1).donate(1n, { value: ONE_ETH });
      await donation.connect(donor2).donate(1n, { value: ethers.parseEther("0.5") });
      await donation.connect(donor3).donate(1n, { value: ethers.parseEther("0.25") });

      expect(await donation.getDonationCount(1n)).to.equal(3n);
      expect(await escrow.campaignEscrowBalance(1n)).to.equal(ethers.parseEther("1.75"));
      expect(await escrow.totalHeld()).to.equal(ethers.parseEther("1.75"));
    });

    it("REJECTS donations to a fraud-flagged campaign (structural coupling)", async function () {
      const { campaign, donation, donor1 } = await loadFixture(deployFixture);

      await createCampaign(campaign, donor1, { fraudScore: 80n }); // flagged at creation
      await expect(
        donation.connect(donor1).donate(1n, { value: ONE_ETH })
      ).to.be.revertedWith("Campaign not open for donations");

      await createCampaign(campaign, donor1); // id 2, clean
      await campaign.flagCampaign(2n, 60n);          // flagged post-creation
      await expect(
        donation.connect(donor1).donate(2n, { value: ONE_ETH })
      ).to.be.revertedWith("Campaign not open for donations");
    });

    it("REJECTS donations when the registry is not wired (adversarial setup)", async function () {
      const { escrow, donor1 } = await loadFixture(deployFixture);
      const Donation2 = await ethers.getContractFactory("MediFundDonation", donor1);
      const rogue = await Donation2.deploy(await escrow.getAddress());
      await rogue.waitForDeployment();
      await expect(rogue.donate(1n, { value: ONE_ETH })).to.be.revertedWith("Registry not set");
    });

    it("rejects zero-value donations and unknown campaigns", async function () {
      const { campaign, donation, donor1 } = await loadFixture(deployFixture);
      await createCampaign(campaign, donor1);

      await expect(donation.connect(donor1).donate(1n, { value: 0n }))
        .to.be.revertedWith("Donation must be > 0");
      await expect(donation.connect(donor1).donate(42n, { value: ONE_ETH }))
        .to.be.revertedWith("Campaign not open for donations");
    });

    it("escrow holdFunds validates inputs directly", async function () {
      const { escrow, donor1 } = await loadFixture(deployFixture);
      await expect(escrow.holdFunds(1n, donor1.address, { value: 0n }))
        .to.be.revertedWith("Amount must be > 0");
      await expect(escrow.holdFunds(1n, ethers.ZeroAddress, { value: ONE_ETH }))
        .to.be.revertedWith("Invalid donor");
    });
  });

  // ------------------------------------------------------------------
  // ALGORITHM 3 - conditional release of escrowed funds
  // ------------------------------------------------------------------
  describe("Algorithm 3: milestone-based disbursement", function () {
    async function fundedFixture() {
      const base = await loadFixture(deployFixture);
      await createCampaign(base.campaign, base.patient); // id 1 -> patient
      await base.donation.connect(base.donor1).donate(1n, { value: ethers.parseEther("5") });
      await base.donation.connect(base.donor2).donate(1n, { value: ethers.parseEther("5") });
      await base.escrow.setCampaignBeneficiary(1n, base.patient.address);
      return base;
    }

    it("releases an exact milestone amount to the verified patient wallet", async function () {
      const { escrow, patient } = await loadFixture(fundedFixture);

      const before = await ethers.provider.getBalance(patient.address);
      const milestone = ethers.parseEther("3");

      await expect(escrow.milestoneRelease(1n, milestone, "Hospital invoice #A-102 paid"))
        .to.emit(escrow, "MilestoneRelease")
        .withArgs(1n, milestone, "Hospital invoice #A-102 paid");

      // entries were [5 ETH donor1, 5 ETH donor2]: 3 ETH comes off the oldest
      const after = await ethers.provider.getBalance(patient.address);
      expect(after - before).to.equal(milestone);

      expect(await escrow.campaignEscrowBalance(1n)).to.equal(ethers.parseEther("7"));
      expect(await escrow.totalHeld()).to.equal(ethers.parseEther("7"));
      expect(await ethers.provider.getBalance(await escrow.getAddress())).to.equal(ethers.parseEther("7"));

      const e1 = await escrow.getEscrowEntry(1n);
      const e2 = await escrow.getEscrowEntry(2n);
      expect(e1.amount).to.equal(ethers.parseEther("2")); // partial remainder kept exact
      expect(e1.released).to.equal(false);
      expect(e2.amount).to.equal(ethers.parseEther("5")); // untouched
    });

    it("consumes held entries oldest-first across several releases", async function () {
      const { escrow } = await loadFixture(fundedFixture);

      await escrow.releaseFunds(1n, ethers.parseEther("6"), "Phase 1 treatment");
      let e1 = await escrow.getEscrowEntry(1n);
      let e2 = await escrow.getEscrowEntry(2n);
      expect(e1.released).to.equal(true);
      expect(e1.amount).to.equal(0n);
      expect(e2.amount).to.equal(ethers.parseEther("4")); // 6 = 5 + 1 from entry 2

      await escrow.releaseFunds(1n, ethers.parseEther("4"), "Phase 2 treatment");
      e2 = await escrow.getEscrowEntry(2n);
      expect(e2.released).to.equal(true);
      expect(await escrow.campaignEscrowBalance(1n)).to.equal(0n);
      expect(await escrow.totalHeld()).to.equal(0n);
    });

    it("refunds a donor exactly, and refuses double refunds / released entries", async function () {
      const { escrow, donor1 } = await loadFixture(fundedFixture);

      const before = await ethers.provider.getBalance(donor1.address);
      await escrow.refundDonor(1n); // admin pays gas; donor receives the exact held amount
      const after = await ethers.provider.getBalance(donor1.address);

      expect(after - before).to.equal(ethers.parseEther("5"));
      expect((await escrow.getEscrowEntry(1n)).refunded).to.equal(true);

      await expect(escrow.refundDonor(1n)).to.be.revertedWith("Not eligible for refund");

      // consume entry 2 entirely, then refunding it must fail
      await escrow.releaseFunds(1n, ethers.parseEther("5"), "Final discharge");
      await expect(escrow.refundDonor(2n)).to.be.revertedWith("Not eligible for refund");
    });

    it("refund remains exact after a PARTIAL release (remainder accounting)", async function () {
      const { escrow, donor2 } = await loadFixture(fundedFixture);

      // entry1=5, entry2=5. Release 7 -> entry1 emptied, entry2 keeps 3.
      await escrow.releaseFunds(1n, ethers.parseEther("7"), "Surgery deposit");
      expect(await escrow.campaignEscrowBalance(1n)).to.equal(ethers.parseEther("3"));

      const before = await ethers.provider.getBalance(donor2.address);
      await escrow.refundDonor(2n);
      const after = await ethers.provider.getBalance(donor2.address);
      expect(after - before).to.equal(ethers.parseEther("3")); // exact remainder, not 5
      expect(await escrow.totalHeld()).to.equal(0n);
    });

    it("ADVERSARIAL: unauthorised or malformed releases always revert", async function () {
      const { escrow, other } = await loadFixture(fundedFixture);

      await expect(escrow.connect(other).milestoneRelease(1n, ONE_ETH, "hijack"))
        .to.be.reverted;
      await expect(escrow.connect(other).releaseFunds(1n, ONE_ETH, "hijack"))
        .to.be.reverted;
      await expect(escrow.connect(other).refundDonor(1n)).to.be.reverted;
      await expect(escrow.connect(other).setCampaignBeneficiary(1n, other.address))
        .to.be.reverted;

      // owner-side malformed requests
      await expect(escrow.milestoneRelease(9n, ONE_ETH, "unknown campaign"))
        .to.be.revertedWith("Insufficient escrow balance");
      await expect(escrow.releaseFunds(1n, 0n, "nothing")).to.be.revertedWith("Amount must be > 0");
      await expect(escrow.releaseFunds(1n, ethers.parseEther("50"), "over-release"))
        .to.be.revertedWith("Insufficient escrow balance");
      await expect(escrow.milestoneRelease(1n, ONE_ETH, ""))
        .to.be.revertedWith("Milestone required");
    });

    it("ADVERSARIAL: re-entrant beneficiary cannot double-drain the escrow", async function () {
      const { campaign, donation, escrow, donor1, patient } = await loadFixture(deployFixture);

      // malicious beneficiary that tries to re-enter milestoneRelease on payout
      const Attack = await ethers.getContractFactory("ReentrancyAttacker", donor1);
      const attacker = await Attack.deploy(await escrow.getAddress(), 2n);
      await attacker.waitForDeployment();

      await createCampaign(campaign, patient); // id 1 (clean, unused here)
      await createCampaign(campaign, await attacker.getAddress(), { title: "Attack" }); // id 2
      await donation.connect(donor1).donate(2n, { value: ethers.parseEther("4") });
      await escrow.setCampaignBeneficiary(2n, await attacker.getAddress());

      await escrow.milestoneRelease(2n, ethers.parseEther("4"), "legit payout");

      expect(await attacker.attacks()).to.equal(1n);      // re-entry was attempted
      expect(await attacker.breached()).to.equal(false);  // ...and failed
      expect(await escrow.campaignEscrowBalance(2n)).to.equal(0n);
      expect(await escrow.totalHeld()).to.equal(0n);
    });

    it("keeps global accounting conserved across mixed flows", async function () {
      const { escrow } = await loadFixture(fundedFixture);

      await escrow.releaseFunds(1n, ethers.parseEther("6"), "Phase 1"); // pays out 6 (entry1=5 fully + 1 from entry2)
      await escrow.refundDonor(2n);                                     // refunds entry2's remaining 4

      const balance = await ethers.provider.getBalance(await escrow.getAddress());
      expect(await escrow.totalHeld()).to.equal(0n);
      expect(balance).to.equal(0n); // 10 ETH in -> 6 paid + 4 refunded, nothing lost or duplicated
    });
  });

  // ------------------------------------------------------------------
  // VERIFICATION CONTRACT - hospital / patient / document checks
  // ------------------------------------------------------------------
  describe("MediFundVerification", function () {
    it("tracks pending verifications and flips them to Verified", async function () {
      const { verification, admin } = await loadFixture(deployFixture);

      const pId = await verification.createVerification.staticCall(1n, 0n, "Patient ID checked");
      await verification.createVerification(1n, 0n, "Patient ID checked");
      const hId = await verification.createVerification.staticCall(1n, 1n, "Hospital letter checked");
      await verification.createVerification(1n, 1n, "Hospital letter checked");
      const dId = await verification.createVerification.staticCall(1n, 2n, "Invoice hash checked");
      await verification.createVerification(1n, 2n, "Invoice hash checked");

      expect(pId).to.equal(1n);
      expect(hId).to.equal(2n);
      expect(dId).to.equal(3n);

      let v = await verification.getVerification(1n);
      expect(v.status).to.equal(0n); // Pending
      expect(v.verifier).to.equal(admin.address);

      await expect(verification.updateVerification(1n, 1n, "ok"))
        .to.emit(verification, "VerificationUpdated").withArgs(1n, 1n, admin.address);
      await verification.updateVerification(2n, 1n, "ok");
      await verification.updateVerification(3n, 1n, "ok");

      v = await verification.getVerification(1n);
      expect(v.status).to.equal(1n); // Verified
      expect(await verification.isCampaignFullyVerified(1n)).to.equal(true);
    });

    it("isCampaignFullyVerified stays false until ALL three types pass", async function () {
      const { verification } = await loadFixture(deployFixture);
      await verification.createVerification(1n, 0n, "patient");
      await verification.createVerification(1n, 1n, "hospital");

      await verification.updateVerification(1n, 1n, "ok");
      expect(await verification.isCampaignFullyVerified(1n)).to.equal(false);

      await verification.updateVerification(2n, 2n, "bad letter"); // hospital REJECTED
      expect(await verification.isCampaignFullyVerified(1n)).to.equal(false);
    });

    it("rejects invalid verification updates", async function () {
      const { verification } = await loadFixture(deployFixture);
      await expect(verification.updateVerification(77n, 1n, "x"))
        .to.be.revertedWith("Invalid verification");
    });
  });
});
