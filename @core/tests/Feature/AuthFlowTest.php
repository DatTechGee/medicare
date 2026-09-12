<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->artisan('db:seed');
    }

    public function test_user_can_view_login_form()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_user_can_view_register_form()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_admin_can_view_login_form()
    {
        $response = $this->get('/login/admin');
        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_is_redirected_from_dashboard()
    {
        $response = $this->get('/user-home');
        $response->assertStatus(302);
    }

    public function test_unauthenticated_admin_is_redirected()
    {
        $response = $this->get('/admin-home');
        $response->assertStatus(302);
    }
}
