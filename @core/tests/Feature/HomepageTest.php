<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->artisan('db:seed');
    }

    public function test_homepage_loads()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_about_page_loads()
    {
        $response = $this->get('/about');
        $response->assertStatus(200);
    }

    public function test_faq_page_loads()
    {
        $response = $this->get('/faq');
        $response->assertStatus(200);
    }

    public function test_team_page_loads()
    {
        $response = $this->get('/team');
        $response->assertStatus(200);
    }

    public function test_blog_page_loads()
    {
        $response = $this->get('/blog');
        $response->assertStatus(200);
    }

    public function test_donations_page_loads()
    {
        $response = $this->get('/donations');
        $response->assertStatus(200);
    }

    public function test_contact_page_loads()
    {
        $response = $this->get('/contact');
        $response->assertStatus(200);
    }

    public function test_login_page_loads()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_register_page_loads()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_admin_login_page_loads()
    {
        $response = $this->get('/login/admin');
        $response->assertStatus(200);
    }
}
