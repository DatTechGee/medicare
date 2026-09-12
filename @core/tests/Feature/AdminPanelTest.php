<?php

namespace Tests\Feature;

use App\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->artisan('db:seed');
    }

    public function test_admin_dashboard_requires_auth()
    {
        $response = $this->get('/admin-home');
        $response->assertStatus(302);
    }

    public function test_admin_can_access_dashboard_when_logged_in()
    {
        $admin = Admin::where('username', 'medifund_admin')->first();
        if (!$admin) {
            $this->markTestSkipped('No admin user seeded');
        }

        $this->actingAs($admin, 'admin');

        $response = $this->get('/admin-home');
        $response->assertStatus(200);
    }

    public function test_admin_donations_page_loads()
    {
        $admin = Admin::where('username', 'medifund_admin')->first();
        if (!$admin) {
            $this->markTestSkipped('No admin user seeded');
        }

        $this->actingAs($admin, 'admin');

        $response = $this->get('/admin-home/donations');
        $response->assertStatus(200);
    }

    public function test_admin_blockchain_settings_page_loads()
    {
        $admin = Admin::where('username', 'medifund_admin')->first();
        if (!$admin) {
            $this->markTestSkipped('No admin user seeded');
        }

        $this->actingAs($admin, 'admin');

        $response = $this->get('/admin-home/blockchain/settings');
        $response->assertStatus(200);
    }

    public function test_admin_fraud_dashboard_loads()
    {
        $admin = Admin::where('username', 'medifund_admin')->first();
        if (!$admin) {
            $this->markTestSkipped('No admin user seeded');
        }

        $this->actingAs($admin, 'admin');

        $response = $this->get('/admin-home/fraud/dashboard');
        $response->assertStatus(200);
    }

    public function test_admin_verifications_page_loads()
    {
        $admin = Admin::where('username', 'medifund_admin')->first();
        if (!$admin) {
            $this->markTestSkipped('No admin user seeded');
        }

        $this->actingAs($admin, 'admin');

        $response = $this->get('/admin-home/verifications');
        $response->assertStatus(200);
    }
}
