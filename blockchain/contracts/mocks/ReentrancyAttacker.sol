// SPDX-License-Identifier: MIT
pragma solidity ^0.8.19;

import "../MediFundEscrow.sol";

/**
 * @dev Test helper: a beneficiary wallet that attempts to re-enter
 *      MediFundEscrow.milestoneRelease while receiving a legitimate payout.
 *      Used to prove the escrow's checks-effects-interactions pattern and
 *      ReentrancyGuard prevent duplicated payouts (adversarial testing,
 *      Section 3.3 of the research methodology).
 */
contract ReentrancyAttacker {
    MediFundEscrow public immutable escrow;
    uint256 public immutable campaignId;
    uint256 public attacks;
    bool public breached;

    constructor(MediFundEscrow _escrow, uint256 _campaignId) {
        escrow = _escrow;
        campaignId = _campaignId;
    }

    receive() external payable {
        attacks++;
        // Attempt to drain more funds while the outer payout is still in flight.
        try escrow.milestoneRelease(campaignId, 1 wei, "re-entrant theft") {
            breached = true; // would mean the guard failed
        } catch {
            // expected: nonReentrant guard / insufficient balance revert
        }
    }
}
