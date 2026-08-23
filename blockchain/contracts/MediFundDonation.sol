// SPDX-License-Identifier: MIT
pragma solidity ^0.8.19;

import "@openzeppelin/contracts/utils/ReentrancyGuard.sol";

interface IMediFundCampaign {
    function isDonatable(uint256 _campaignId) external view returns (bool);
    function recordDonation(uint256 _campaignId, address _donor, uint256 _amount) external;
}

/**
 * @title MediFundDonation
 * @notice Accepts donor contributions (Algorithm 2). Donations are only
 * accepted for campaigns that passed the fraud gate in the campaign registry;
 * flagged campaigns are structurally unable to receive funds.
 */
contract MediFundDonation is ReentrancyGuard {

    struct Donation {
        uint256 id;
        uint256 campaignId;
        address donor;
        uint256 amount;
        uint256 timestamp;
        bool refunded;
    }

    uint256 public donationCounter;
    mapping(uint256 => Donation) public donations;
    mapping(uint256 => uint256[]) public campaignDonationIds;

    address public escrowContract;
    address public campaignContract;
    address public owner;

    event DonationMade(uint256 indexed donationId, uint256 indexed campaignId, address indexed donor, uint256 amount);
    event DonationRefunded(uint256 indexed donationId, address indexed donor, uint256 amount);
    event CampaignRegistrySet(address indexed registry);

    constructor(address _escrowContract) {
        require(_escrowContract != address(0), "Invalid escrow");
        escrowContract = _escrowContract;
        owner = msg.sender;
    }

    /**
     * @notice Bind the fraud-gated campaign registry. Only the deployer may set it.
     */
    function setCampaignContract(address _campaignContract) external {
        require(msg.sender == owner, "Only owner");
        require(_campaignContract != address(0), "Invalid registry");
        campaignContract = _campaignContract;
        emit CampaignRegistrySet(_campaignContract);
    }

    /**
     * @notice Algorithm 2: record a donation and forward it to escrow custody.
     * Reverts for flagged/cancelled/completed campaigns (fraud gate coupling).
     */
    function donate(uint256 _campaignId) external payable nonReentrant {
        require(msg.value > 0, "Donation must be > 0");
        require(msg.sender != address(0), "Invalid donor");
        require(campaignContract != address(0), "Registry not set");
        require(IMediFundCampaign(campaignContract).isDonatable(_campaignId), "Campaign not open for donations");

        donationCounter++;
        donations[donationCounter] = Donation({
            id: donationCounter,
            campaignId: _campaignId,
            donor: msg.sender,
            amount: msg.value,
            timestamp: block.timestamp,
            refunded: false
        });

        campaignDonationIds[_campaignId].push(donationCounter);

        emit DonationMade(donationCounter, _campaignId, msg.sender, msg.value);

        // Record totals in the campaign registry (raisedAmount + donor ledger)
        IMediFundCampaign(campaignContract).recordDonation(_campaignId, msg.sender, msg.value);

        // Forward to escrow custody (Algorithm 2, Step 3)
        (bool success, ) = escrowContract.call{value: msg.value}(
            abi.encodeWithSignature("holdFunds(uint256,address)", _campaignId, msg.sender)
        );
        require(success, "Escrow transfer failed");
    }

    function getDonation(uint256 _donationId) external view returns (Donation memory) {
        require(_donationId > 0 && _donationId <= donationCounter, "Invalid donation");
        return donations[_donationId];
    }

    function getCampaignDonations(uint256 _campaignId) external view returns (uint256[] memory) {
        return campaignDonationIds[_campaignId];
    }

    function getDonationCount(uint256 _campaignId) external view returns (uint256) {
        return campaignDonationIds[_campaignId].length;
    }

    receive() external payable {}
}
