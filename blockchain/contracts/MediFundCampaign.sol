// SPDX-License-Identifier: MIT
pragma solidity ^0.8.19;

import "@openzeppelin/contracts/access/Ownable.sol";
import "@openzeppelin/contracts/utils/ReentrancyGuard.sol";

/**
 * @title MediFundCampaign
 * @notice On-chain registry of medical crowdfunding campaigns.
 * Implements Algorithm 1 (fraud-gated campaign creation):
 * a campaign whose fraud score meets/exceeds FRAUD_GATE_THRESHOLD is
 * stored as Flagged and is therefore never eligible to receive funds.
 */
contract MediFundCampaign is Ownable, ReentrancyGuard {

    enum CampaignStatus { Active, Completed, Cancelled, Flagged }

    struct Campaign {
        uint256 id;
        string title;
        uint256 goalAmount;
        uint256 raisedAmount;
        address payable beneficiary;
        string patientName;
        string hospitalName;
        string medicalDetails;
        CampaignStatus status;
        uint256 createdAt;
        uint256 deadline;
        uint256 fraudScore;
        bool hospitalVerified;
        bool patientVerified;
    }

    /// @notice Fraud scores >= this value block on-chain listing (Algorithm 1, Step 6).
    uint256 public constant FRAUD_GATE_THRESHOLD = 50;

    uint256 public campaignCounter;
    mapping(uint256 => Campaign) public campaigns;
    mapping(uint256 => address[]) public campaignDonors;
    mapping(uint256 => mapping(address => uint256)) public donorAmounts;

    /// @notice Donation contract authorized to write donation totals.
    address public donationSource;

    event CampaignCreated(uint256 indexed campaignId, address indexed beneficiary, uint256 goalAmount);
    event CampaignUpdated(uint256 indexed campaignId, string status);
    event HospitalVerified(uint256 indexed campaignId, bool verified);
    event PatientVerified(uint256 indexed campaignId, bool verified);
    event FraudFlagged(uint256 indexed campaignId, uint256 score);
    event DonationRecorded(uint256 indexed campaignId, address indexed donor, uint256 amount, uint256 raisedAmount);

    constructor() Ownable(msg.sender) {}

    /**
     * @notice Bind the donation contract allowed to record contributions.
     */
    function setDonationSource(address _donationContract) external onlyOwner {
        require(_donationContract != address(0), "Invalid donation contract");
        donationSource = _donationContract;
    }

    /**
     * @notice Called by MediFundDonation on every accepted contribution.
     * Updates raisedAmount plus the per-campaign donor ledger.
     */
    function recordDonation(uint256 _campaignId, address _donor, uint256 _amount) external {
        require(msg.sender == donationSource, "Unauthorized recorder");
        require(_campaignId > 0 && _campaignId <= campaignCounter, "Invalid campaign");
        require(_amount > 0, "Invalid amount");

        campaigns[_campaignId].raisedAmount += _amount;
        if (donorAmounts[_campaignId][_donor] == 0) {
            campaignDonors[_campaignId].push(_donor);
        }
        donorAmounts[_campaignId][_donor] += _amount;

        emit DonationRecorded(_campaignId, _donor, _amount, campaigns[_campaignId].raisedAmount);
    }

    /**
     * @notice Create a campaign after the off-chain fraud engine has scored it.
     * @param _fraudScore 0-100 score produced by the fraud detection module.
     *        Scores at/above the gate threshold store the campaign as Flagged,
     *        keeping it structurally unable to receive donations.
     */
    function createCampaign(
        string memory _title,
        uint256 _goalAmount,
        address payable _beneficiary,
        string memory _patientName,
        string memory _hospitalName,
        string memory _medicalDetails,
        uint256 _deadline,
        uint256 _fraudScore
    ) external returns (uint256) {
        require(_goalAmount > 0, "Goal must be > 0");
        require(_beneficiary != address(0), "Invalid beneficiary");
        require(_fraudScore <= 100, "Invalid fraud score");

        campaignCounter++;
        campaigns[campaignCounter] = Campaign({
            id: campaignCounter,
            title: _title,
            goalAmount: _goalAmount,
            raisedAmount: 0,
            beneficiary: _beneficiary,
            patientName: _patientName,
            hospitalName: _hospitalName,
            medicalDetails: _medicalDetails,
            status: _fraudScore >= FRAUD_GATE_THRESHOLD ? CampaignStatus.Flagged : CampaignStatus.Active,
            createdAt: block.timestamp,
            deadline: _deadline,
            fraudScore: _fraudScore,
            hospitalVerified: false,
            patientVerified: false
        });

        if (_fraudScore >= FRAUD_GATE_THRESHOLD) {
            emit FraudFlagged(campaignCounter, _fraudScore);
        } else {
            emit CampaignCreated(campaignCounter, _beneficiary, _goalAmount);
        }
        return campaignCounter;
    }

    function updateCampaignStatus(uint256 _campaignId, CampaignStatus _status) external onlyOwner {
        require(_campaignId > 0 && _campaignId <= campaignCounter, "Invalid campaign");
        campaigns[_campaignId].status = _status;
        emit CampaignUpdated(_campaignId, _status == CampaignStatus.Active ? "Active" : "Updated");
    }

    function verifyHospital(uint256 _campaignId, bool _verified) external onlyOwner {
        require(_campaignId > 0 && _campaignId <= campaignCounter, "Invalid campaign");
        campaigns[_campaignId].hospitalVerified = _verified;
        emit HospitalVerified(_campaignId, _verified);
    }

    function verifyPatient(uint256 _campaignId, bool _verified) external onlyOwner {
        require(_campaignId > 0 && _campaignId <= campaignCounter, "Invalid campaign");
        campaigns[_campaignId].patientVerified = _verified;
        emit PatientVerified(_campaignId, _verified);
    }

    function flagCampaign(uint256 _campaignId, uint256 _score) external onlyOwner {
        require(_campaignId > 0 && _campaignId <= campaignCounter, "Invalid campaign");
        campaigns[_campaignId].fraudScore = _score;
        campaigns[_campaignId].status = CampaignStatus.Flagged;
        emit FraudFlagged(_campaignId, _score);
    }

    /// @notice True when a campaign is listed for fundraising (fraud gate passed).
    function isDonatable(uint256 _campaignId) public view returns (bool) {
        if (_campaignId == 0 || _campaignId > campaignCounter) return false;
        return campaigns[_campaignId].status == CampaignStatus.Active;
    }

    function getCampaign(uint256 _campaignId) external view returns (Campaign memory) {
        require(_campaignId > 0 && _campaignId <= campaignCounter, "Invalid campaign");
        return campaigns[_campaignId];
    }

    function getDonors(uint256 _campaignId) external view returns (address[] memory) {
        require(_campaignId > 0 && _campaignId <= campaignCounter, "Invalid campaign");
        return campaignDonors[_campaignId];
    }

    function getDonorAmount(uint256 _campaignId, address _donor) external view returns (uint256) {
        return donorAmounts[_campaignId][_donor];
    }

    receive() external payable {}
}
