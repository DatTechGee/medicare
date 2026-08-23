<?php

namespace App\Services;

use App\BlockchainTransaction;
use App\Cause;
use App\CauseLogs;
use App\Escrow;
use Illuminate\Support\Str;

class DemoBlockchainService
{
    const NETWORK = 'Demo Ethereum';
    const CURRENCY = 'ETH';
    const EXPLORER_URL = 'https://etherscan.io/tx/';
    const BLOCK_EXPLORER_URL = 'https://etherscan.io/block/';

    public static function generateWalletAddress(): string
    {
        $hex = '0123456789abcdef';
        $address = '0x';
        for ($i = 0; $i < 40; $i++) {
            $address .= $hex[random_int(0, 15)];
        }
        return $address;
    }

    public static function generateTransactionHash(): string
    {
        /* replay-proof generation: loop until a never-before-seen hash is produced */
        do {
            $hex = '0123456789abcdef';
            $hash = '0x';
            for ($i = 0; $i < 64; $i++) {
                $hash .= $hex[random_int(0, 15)];
            }
        } while (\App\Helpers\PaymentGuard::replayDetected($hash));

        return $hash;
    }

    public static function generateBlockNumber(): int
    {
        return random_int(18000000, 19999999);
    }

    public static function generateGasFee(): string
    {
        return number_format(random_int(10, 250) / 1000, 8) . ' ETH';
    }

    public static function processDonation(
        CauseLogs $causeLog,
        string $walletAddress,
        float $amount
    ): BlockchainTransaction {
        $transactionHash = self::generateTransactionHash();
        $blockNumber = self::generateBlockNumber();
        $gasFee = self::generateGasFee();

        $blockchainTx = BlockchainTransaction::create([
            'cause_log_id' => $causeLog->id,
            'campaign_id' => $causeLog->cause_id,
            'wallet_address' => $walletAddress,
            'transaction_hash' => $transactionHash,
            'amount' => $amount,
            'currency' => self::CURRENCY,
            'network' => self::NETWORK,
            'transaction_type' => 'donation',
            'status' => 'confirmed',
            'block_number' => $blockNumber,
            'gas_fee' => $gasFee,
            'confirmed_at' => now(),
        ]);

        // Update cause log with blockchain details
        // (preserve payment-integrity flags such as mismatch_flagged)
        $causeLog->update([
            'donor_wallet_address' => $walletAddress,
            'blockchain_transaction_hash' => $transactionHash,
            'payment_type' => 'crypto',
            'donation_status' => $causeLog->donation_status ?: 'confirmed',
            'status' => 'complete',
            'transaction_id' => $transactionHash,
        ]);

        // Hold donation in escrow (Algorithm 2)
        // Escrow tracks the NET amount payable to the patient (donation minus platform fee).
        // Callers may pass an ETH-converted amount for $tx — escrow must use real donated totals.
        $netAmount = round(max((float) $causeLog->amount - (float) ($causeLog->admin_charge ?? 0), 0), 2);
        Escrow::create([
            'campaign_id' => $causeLog->cause_id,
            'cause_log_id' => $causeLog->id,
            'amount' => $netAmount,
            'donor_wallet_address' => $walletAddress,
            'blockchain_tx_hash' => $transactionHash,
            'status' => 'held',
        ]);

        return $blockchainTx;
    }

    public static function processWithdrawal(
        Cause $campaign,
        float $amount,
        string $walletAddress
    ): BlockchainTransaction {
        return self::recordTransfer($campaign, $amount, $walletAddress, 'withdrawal');
    }

    public static function processRefund(
        Cause $campaign,
        float $amount,
        string $donorWalletAddress
    ): BlockchainTransaction {
        return self::recordTransfer($campaign, $amount, $donorWalletAddress, 'refund');
    }

    private static function recordTransfer(
        Cause $campaign,
        float $amount,
        string $walletAddress,
        string $type
    ): BlockchainTransaction {
        $transactionHash = self::generateTransactionHash();
        $blockNumber = self::generateBlockNumber();
        $gasFee = self::generateGasFee();

        /* cause_log_id has an FK constraint — attach to this campaign's latest donation log */
        $causeLogId = \App\CauseLogs::where('cause_id', $campaign->id)->orderByDesc('id')->value('id')
            ?? \App\CauseLogs::orderByDesc('id')->value('id') ?? 0;

        return BlockchainTransaction::create([
            'cause_log_id' => $causeLogId,
            'campaign_id' => $campaign->id,
            'wallet_address' => $walletAddress,
            'transaction_hash' => $transactionHash,
            'amount' => $amount,
            'currency' => self::CURRENCY,
            'network' => self::NETWORK,
            'transaction_type' => $type,
            'status' => 'confirmed',
            'block_number' => $blockNumber,
            'gas_fee' => $gasFee,
            'confirmed_at' => now(),
        ]);
    }

    public static function verifyTransaction(string $transactionHash): array
    {
        $tx = BlockchainTransaction::where('transaction_hash', $transactionHash)->first();

        if (!$tx) {
            return [
                'verified' => false,
                'message' => 'Transaction not found',
            ];
        }

        return [
            'verified' => true,
            'transaction_hash' => $tx->transaction_hash,
            'block_number' => $tx->block_number,
            'from' => $tx->wallet_address,
            'amount' => $tx->formatted_amount,
            'network' => $tx->network,
            'status' => $tx->status,
            'confirmed_at' => $tx->confirmed_at ? $tx->confirmed_at->toDateTimeString() : null,
            'gas_fee' => $tx->gas_fee,
            'explorer_url' => self::EXPLORER_URL . $tx->transaction_hash,
        ];
    }

    public static function getExplorerUrl(string $hash): string
    {
        return self::EXPLORER_URL . $hash;
    }

    public static function getBlockExplorerUrl(int $blockNumber): string
    {
        return self::BLOCK_EXPLORER_URL . $blockNumber;
    }
}
