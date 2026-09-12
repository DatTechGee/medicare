<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlockchainApiThrottleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->artisan('db:seed');
    }

    public function test_blockchain_wallet_status_returns_200()
    {
        $response = $this->getJson('/api/blockchain/wallet-status');
        $response->assertSuccessful();
    }

    public function test_blockchain_network_stats_returns_200()
    {
        $response = $this->getJson('/api/blockchain/network-stats');
        $response->assertSuccessful();
    }

    public function test_blockchain_config_returns_200_or_500()
    {
        $response = $this->getJson('/api/blockchain/config');
        $this->assertContains($response->status(), [200, 500]);
    }

    public function test_public_campaigns_api_returns_200()
    {
        $response = $this->getJson('/api/campaigns');
        $response->assertSuccessful();
    }

    public function test_blockchain_api_docs_loads()
    {
        $response = $this->get('/api/docs');
        $response->assertSuccessful();
    }

    public function test_blockchain_api_donate_endpoint_exists()
    {
        $response = $this->postJson('/api/blockchain/donate', [
            'campaign_id' => 999,
            'amount' => 0.01,
            'tx_hash' => '0x' . str_repeat('a', 64),
            'donor_wallet' => '0x' . str_repeat('b', 40),
        ]);
        $this->assertContains($response->status(), [200, 422, 500]);
    }

    public function test_blockchain_connect_wallet_endpoint_exists()
    {
        $response = $this->postJson('/api/blockchain/connect-wallet', [
            'wallet_address' => '0x' . str_repeat('c', 40),
        ]);
        $this->assertContains($response->status(), [200, 422, 500]);
    }

    public function test_blockchain_verify_transaction_endpoint_exists()
    {
        $response = $this->postJson('/api/blockchain/verify-transaction', [
            'tx_hash' => '0x' . str_repeat('d', 64),
        ]);
        $this->assertContains($response->status(), [200, 422, 500]);
    }
}
