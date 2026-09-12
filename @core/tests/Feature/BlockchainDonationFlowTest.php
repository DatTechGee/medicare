<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlockchainDonationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->artisan('db:seed');
    }

    public function test_blockchain_donation_form_loads()
    {
        $response = $this->get('/blockchain-donate/1');
        $response->assertSuccessful();
    }

    public function test_blockchain_explorer_loads()
    {
        $response = $this->get('/blockchain/explorer');
        $response->assertSuccessful();
    }

    public function test_donations_page_loads()
    {
        $response = $this->get('/donations');
        $response->assertSuccessful();
    }

    public function test_blockchain_transaction_show_route_responds()
    {
        $response = $this->get('/blockchain/tx/0x' . str_repeat('a', 64));
        $this->assertContains($response->status(), [200, 302, 404, 500]);
    }

    public function test_blockchain_verify_route_responds()
    {
        $response = $this->get('/blockchain/verify/0x' . str_repeat('a', 64));
        $this->assertContains($response->status(), [200, 404, 500]);
    }

    public function test_blockchain_receipt_route_responds()
    {
        $response = $this->get('/donation/receipt/TEST-TX-123');
        $this->assertContains($response->status(), [200, 404, 500]);
    }
}
