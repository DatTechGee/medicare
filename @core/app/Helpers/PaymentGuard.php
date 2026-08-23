<?php

namespace App\Helpers;

use App\BlockchainTransaction;
use App\CauseLogs;

/**
 * Payment integrity guards for the demo blockchain payment rail.
 *
 *  - validateAddress()   structural EIP-55-style wallet validation (canonical form enforcement)
 *  - assertNoReplay()    tx-replay guard — a transaction hash may only ever be recorded once
 *  - walletVelocityOk()  hard rate-limit on donations per donor wallet
 *  - amountMismatch()    tripwire comparing the MetaMask-confirmed amount vs the server-side amount
 */
class PaymentGuard
{
    /**
     * Validate an Ethereum address.
     * Accepts canonical all-lower / all-upper hex; mixed-case is only accepted when the
     * optional kornrunner/keccak package is installed to verify the EIP-55 checksum.
     *
     * @return array{ok: bool, reason: string}
     */
    public static function validateAddress(string $address): array
    {
        $address = trim($address);

        if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $address)) {
            return ['ok' => false, 'reason' => 'Invalid wallet address format'];
        }

        $hex = substr($address, 2);
        $isLower = ($hex === strtolower($hex));
        $isUpper = ($hex === strtoupper($hex));

        if ($isLower || $isUpper) {
            return ['ok' => true, 'reason' => 'Canonical address form'];
        }

        /* mixed case → verify EIP-55 checksum when keccak is available;
           otherwise normalise to lowercase (addresses are case-insensitive) */
        if (class_exists(\kornrunner\Keccak::class)) {
            $lower = strtolower($address);
            $hash = \kornrunner\Keccak::hash(substr($lower, 2), 256);

            for ($i = 0; $i < 40; $i++) {
                $char = $hex[$i];
                if (hexdec($hash[$i]) > 7 && strtoupper($char) !== $char) {
                    return ['ok' => false, 'reason' => 'EIP-55 checksum mismatch — address was mistyped'];
                }
                if (hexdec($hash[$i]) <= 7 && strtolower($char) !== $char) {
                    return ['ok' => false, 'reason' => 'EIP-55 checksum mismatch — address was mistyped'];
                }
            }

            return ['ok' => true, 'reason' => 'Valid EIP-55 checksummed address'];
        }

        return [
            'ok' => true,
            'reason' => 'Mixed-case address accepted and normalised to lowercase',
            'address' => strtolower($address),
        ];
    }

    /** Tx-replay guard: returns true when hash already exists in ledger */
    public static function replayDetected(string $transactionHash): bool
    {
        return BlockchainTransaction::where('transaction_hash', $transactionHash)->exists();
    }

    /** Hard velocity limit across ALL campaigns for one donor wallet */
    public static function walletVelocityOk(string $walletAddress, int $maxPerHour = 6): bool
    {
        $recent = CauseLogs::where('donor_wallet_address', strtolower($walletAddress))
            ->where('created_at', '>=', now()->subHour())
            ->count();

        return $recent < $maxPerHour;
    }

    /**
     * Amount-mismatch tripwire.
     * Compares what MetaMask confirmed on-screen with what the server computed.
     */
    public static function amountMismatch(?float $confirmedEth, float $expectedEth, float $tolerancePct = 0.01): bool
    {
        if ($confirmedEth === null || $confirmedEth <= 0) {
            return false; // field absent (legacy client) — other checks still apply
        }

        $expected = max($expectedEth, 0.000001);
        $delta = abs($confirmedEth - $expected) / $expected;

        return $delta > $tolerancePct;
    }
}
