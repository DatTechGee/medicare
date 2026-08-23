// SPDX-License-Identifier: MIT
pragma solidity ^0.8.19;

import "@openzeppelin/contracts/access/Ownable.sol";

contract MediFundVerification is Ownable {

    enum VerificationType { Patient, Hospital, Document }
    enum VerificationStatus { Pending, Verified, Rejected }

    struct Verification {
        uint256 id;
        uint256 campaignId;
        VerificationType vType;
        VerificationStatus status;
        address verifier;
        string notes;
        uint256 timestamp;
    }

    uint256 public verificationCounter;
    mapping(uint256 => Verification) public verifications;
    mapping(uint256 => uint256[]) public campaignVerifications;
    mapping(uint256 => mapping(uint8 => bool)) public campaignVerified;

    event VerificationCreated(uint256 indexed verificationId, uint256 indexed campaignId, VerificationType vType);
    event VerificationUpdated(uint256 indexed verificationId, VerificationStatus status, address verifier);

    constructor() Ownable(msg.sender) {}

    function createVerification(
        uint256 _campaignId,
        VerificationType _vType,
        string memory _notes
    ) external onlyOwner returns (uint256) {
        verificationCounter++;
        verifications[verificationCounter] = Verification({
            id: verificationCounter,
            campaignId: _campaignId,
            vType: _vType,
            status: VerificationStatus.Pending,
            verifier: msg.sender,
            notes: _notes,
            timestamp: block.timestamp
        });

        campaignVerifications[_campaignId].push(verificationCounter);
        emit VerificationCreated(verificationCounter, _campaignId, _vType);
        return verificationCounter;
    }

    function updateVerification(
        uint256 _verificationId,
        VerificationStatus _status,
        string memory _notes
    ) external onlyOwner {
        require(_verificationId > 0 && _verificationId <= verificationCounter, "Invalid verification");

        Verification storage v = verifications[_verificationId];
        v.status = _status;
        v.notes = _notes;
        v.verifier = msg.sender;
        v.timestamp = block.timestamp;

        if (_status == VerificationStatus.Verified) {
            campaignVerified[v.campaignId][uint8(v.vType)] = true;
        }

        emit VerificationUpdated(_verificationId, _status, msg.sender);
    }

    function isCampaignFullyVerified(uint256 _campaignId) external view returns (bool) {
        return campaignVerified[_campaignId][uint8(VerificationType.Patient)]
            && campaignVerified[_campaignId][uint8(VerificationType.Hospital)]
            && campaignVerified[_campaignId][uint8(VerificationType.Document)];
    }

    function getVerification(uint256 _verificationId) external view returns (Verification memory) {
        return verifications[_verificationId];
    }

    function getCampaignVerifications(uint256 _campaignId) external view returns (uint256[] memory) {
        return campaignVerifications[_campaignId];
    }
}
