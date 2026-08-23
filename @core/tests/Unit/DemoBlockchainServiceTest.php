<?php

namespace Tests\Unit;

use App\Cause;
use App\Services\DemoBlockchainService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DemoBlockchainServiceTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function generated_wallet_addresses_are_valid()
    {
        for ($i = 0; $i < 20; $i++) {
            $addr = DemoBlockchainService::generateWalletAddress();
            $this->assertMatchesRegularExpression('/^0x[a-f0-9]{40}$/', $addr);
            $this->assertSame(strtolower($addr), $addr);
        }
    }

    /** @test */
    public function generated_transaction_hashes_are_valid()
    {
        for ($i = 0; $i < 20; $i++) {
            $hash = DemoBlockchainService::generateTransactionHash();
            $this->assertMatchesRegularExpression('/^0x[a-f0-9]{64}$/', $hash, 'Tx hash must be 32 bytes hex');
        }
    }

    /** @test */
    public function block_numbers_stay_in_demo_range()
    {
        for ($i = 0; $i < 50; $i++) {
            $block = DemoBlockchainService::generateBlockNumber();
            $this->assertGreaterThanOrEqual(18000000, $block);
            $this->assertLessThanOrEqual(19999999, $block);
        }
    }

    /** @test */
    public function gas_fees_are_plausible()
    {
        $fee = (float) DemoBlockchainService::generateGasFee();
        $this->assertGreaterThan(0, $fee);
        $this->assertLessThan(1, $fee, 'Demo gas fee should stay below 1 ETH');
    }

    /** @test */
    public function verify_transaction_returns_not_found_for_unknown_hash()
    {
        $result = DemoBlockchainService::verifyTransaction('0x' . str_repeat('a', 64));
        $this->assertFalse($result['verified']);
        $this->assertEquals('Transaction not found', $result['message']);
    }

    /** @test */
    public function verify_transaction_returns_details_for_known_hash()
    {
        $tx = DB::table('blockchain_transactions')->where('transaction_type', 'donation')->first();
        if (!$tx) {
            $this->markTestSkipped('No seeded blockchain transactions');
        }

        $result = DemoBlockchainService::verifyTransaction($tx->transaction_hash);
        $this->assertTrue($result['verified']);
        $this->assertEquals($tx->block_number, $result['block_number']);
        $this->assertEquals($tx->network, $result['network']);
        $this->assertEquals($tx->status, $result['status']);
        $this->assertStringContainsString($tx->transaction_hash, $result['explorer_url']);
    }

    /** @test */
    public function withdrawal_attaches_to_a_valid_cause_log_fk()
    {
        /* regression: cause_log_id used to be hardcoded 0 which violated the FK constraint */
        $cause = Cause::whereHas('cause_logs')->first();
        if (!$cause) {
            $this->markTestSkipped('No campaign with donation logs');
        }

        $tx = DemoBlockchainService::processWithdrawal($cause, 0.05, strtolower($cause->wallet_address ?: DemoBlockchainService::generateWalletAddress()));

        $this->assertInstanceOf(\App\BlockchainTransaction::class, $tx);
        $this->assertEquals('withdrawal', $tx->transaction_type);
        $this->assertDatabaseHas('blockchain_transactions', [
            'id' => $tx->id,
            'transaction_type' => 'withdrawal',
        ]);
        $this->assertTrue(DB::table('cause_logs')->where('id', $tx->cause_log_id)->exists(), 'Withdrawal must reference a real cause_log row');

        $tx->delete();
    }
}
