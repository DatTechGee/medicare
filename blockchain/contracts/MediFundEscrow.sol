// SPDX-License-Identifier: MIT
pragma solidity ^0.8.19;

import "@openzeppelin/contracts/access/Ownable.sol";
import "@openzeppelin/contracts/utils/ReentrancyGuard.sol";

/**
 * @title MediFundEscrow
 * @notice Custodial escrow for donated funds (Algorithm 2: hold donations;
 * Algorithm 3: release only against verified milestone proof, or refund).
 */
contract MediFundEscrow is Ownable, ReentrancyGuard {

    struct EscrowEntry {
        uint256 id;
        uint256 campaignId;
        address donor;
        uint256 amount;
        uint256 heldAt;
        bool released;
        bool refunded;
        string releaseReason;
    }

    uint256 public escrowCounter;
    mapping(uint256 => EscrowEntry) public escrowEntries;
    mapping(uint256 => uint256[]) public campaignEscrowIds;
    mapping(uint256 => uint256) public campaignEscrowBalance;

    /// @notice Per-campaign payout account (the patient's verified wallet).
    mapping(uint256 => address payable) public campaignBeneficiary;

    uint256 public totalHeld;

    event FundsHeld(uint256 indexed escrowId, uint256 indexed campaignId, address donor, uint256 amount);
    event FundsReleased(uint256 indexed escrowId, uint256 indexed campaignId, uint256 amount, string reason);
    event FundsRefunded(uint256 indexed escrowId, uint256 indexed campaignId, address donor, uint256 amount);
    event MilestoneRelease(uint256 indexed campaignId, uint256 amount, string milestone);
    event BeneficiarySet(uint256 indexed campaignId, address beneficiary);

    constructor() Ownable(msg.sender) {}

    /**
     * @notice Hold donated funds in escrow. Called by the donation contract
     * (or directly by a donor) as part of Algorithm 2, Step 3-4.
     */
    function holdFunds(uint256 _campaignId, address _donor) external payable nonReentrant {
        require(msg.value > 0, "Amount must be > 0");
        require(_donor != address(0), "Invalid donor");

        escrowCounter++;
        escrowEntries[escrowCounter] = EscrowEntry({
            id: escrowCounter,
            campaignId: _campaignId,
            donor: _donor,
            amount: msg.value,
            heldAt: block.timestamp,
            released: false,
            refunded: false,
            releaseReason: ""
        });

        campaignEscrowIds[_campaignId].push(escrowCounter);
        campaignEscrowBalance[_campaignId] += msg.value;
        totalHeld += msg.value;

        emit FundsHeld(escrowCounter, _campaignId, _donor, msg.value);
    }

    /**
     * @notice Bind the payout wallet for a campaign (admin-verified patient wallet).
     */
    function setCampaignBeneficiary(uint256 _campaignId, address payable _beneficiary) external onlyOwner {
        require(_beneficiary != address(0), "Invalid beneficiary");
        campaignBeneficiary[_campaignId] = _beneficiary;
        emit BeneficiarySet(_campaignId, _beneficiary);
    }

    /**
     * @notice Release `_amount` of held funds to the campaign beneficiary.
     * Oldest entries are consumed first; partially consumed entries keep their
     * remainder so later refunds stay exact.
     */
    function releaseFunds(uint256 _campaignId, uint256 _amount, string memory _reason)
        public
        onlyOwner
        nonReentrant
    {
        require(_amount > 0, "Amount must be > 0");
        require(campaignEscrowBalance[_campaignId] >= _amount, "Insufficient escrow balance");

        _consumeHeld(_campaignId, _amount, _reason);

        campaignEscrowBalance[_campaignId] -= _amount;
        totalHeld -= _amount;

        address payable to = campaignBeneficiary[_campaignId];
        if (to != address(0)) {
            (bool success, ) = to.call{value: _amount}("");
            require(success, "Transfer failed");
        }
    }

    /**
     * @notice Algorithm 3, Step 4: release funds against a verified milestone/proof.
     * @dev Delegates to releaseFunds(), whose own nonReentrant guard protects
     *      the whole flow (a second guard here would revert every call).
     */
    function milestoneRelease(uint256 _campaignId, uint256 _amount, string memory _milestone)
        external
        onlyOwner
    {
        require(bytes(_milestone).length > 0, "Milestone required");
        releaseFunds(_campaignId, _amount, _milestone);
        emit MilestoneRelease(_campaignId, _amount, _milestone);
    }

    /**
     * @notice Refund one untouched escrow entry back to its donor.
     */
    function refundDonor(uint256 _escrowId) external onlyOwner nonReentrant {
        EscrowEntry storage entry = escrowEntries[_escrowId];
        require(!entry.released && !entry.refunded, "Not eligible for refund");
        require(entry.amount > 0, "Nothing to refund");

        entry.refunded = true;
        campaignEscrowBalance[entry.campaignId] -= entry.amount;
        totalHeld -= entry.amount;

        (bool success, ) = entry.donor.call{value: entry.amount}("");
        require(success, "Refund failed");

        emit FundsRefunded(_escrowId, entry.campaignId, entry.donor, entry.amount);
    }

    function getCampaignEscrowBalance(uint256 _campaignId) external view returns (uint256) {
        return campaignEscrowBalance[_campaignId];
    }

    function getEscrowEntry(uint256 _escrowId) external view returns (EscrowEntry memory) {
        return escrowEntries[_escrowId];
    }

    function getCampaignEscrowIds(uint256 _campaignId) external view returns (uint256[] memory) {
        return campaignEscrowIds[_campaignId];
    }

    receive() external payable {}

    /**
     * @dev FIFO consumption of held entries. Emits one FundsReleased per entry.
     *      entry.amount always reflects the currently-held remainder, so a
     *      later refund of a partially consumed entry is exact.
     */
    function _consumeHeld(uint256 _campaignId, uint256 _amount, string memory _reason) private {
        uint256[] storage escrowIds = campaignEscrowIds[_campaignId];
        uint256 remaining = _amount;

        for (uint256 i = 0; i < escrowIds.length && remaining > 0; i++) {
            EscrowEntry storage entry = escrowEntries[escrowIds[i]];
            if (!entry.released && !entry.refunded && entry.amount > 0) {
                if (entry.amount <= remaining) {
                    remaining -= entry.amount;
                    uint256 consumed = entry.amount;
                    entry.amount = 0;
                    entry.released = true;
                    entry.releaseReason = _reason;
                    emit FundsReleased(entry.id, _campaignId, consumed, _reason);
                } else {
                    entry.amount -= remaining;
                    emit FundsReleased(entry.id, _campaignId, remaining, _reason);
                    remaining = 0;
                }
            }
        }
    }
}
