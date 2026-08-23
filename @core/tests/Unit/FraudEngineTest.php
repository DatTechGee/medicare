<?php

namespace Tests\Unit;

use App\Cause;
use App\Helpers\FraudEngine;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FraudEngineTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function genuine_verified_campaign_scores_low()
    {
        $campaign = Cause::where('wallet_verified', 1)
            ->where('verification_status', 'approved')
            ->first();

        if (!$campaign) {
            $this->markTestSkipped('No verified campaign seeded');
        }

        $result = FraudEngine::analyzeCampaign($campaign);

        $this->assertLessThanOrEqual(20, $result['score'], 'Verified campaign should score <= 20');
        $this->assertEquals('low', $result['risk_level']);
        $this->assertEquals('AUTO_APPROVE', $result['recommendation']);
    }

    /** @test */
    public function unverified_wallet_adds_twenty_points()
    {
        $campaign = Cause::where(function ($q) {
            $q->whereNull('wallet_address')->orWhere('wallet_verified', 0);
        })->first();

        if (!$campaign) {
            $this->markTestSkipped('No unverified campaign seeded');
        }

        /* neutralise any previously persisted score so this test measures only fresh analysis */
        $campaign->fraud_score = 0;

        $result = FraudEngine::analyzeCampaign($campaign);

        $this->assertGreaterThanOrEqual(20, $result['score'], 'Unverified/no wallet must contribute at least +20');
        $this->assertFalse($result['checks']['wallet_verified']);
    }

    /** @test */
    public function risk_levels_follow_score_thresholds()
    {
        $method = new \ReflectionMethod(FraudEngine::class, 'checkWalletVerified');
        $method->setAccessible(true);

        $withWallet = new Cause(['wallet_address' => '0x' . str_repeat('a', 40)]);
        $withWallet->wallet_verified = 1;
        $this->assertTrue($method->invoke(null, $withWallet));

        $without = new Cause([]);
        $this->assertFalse($method->invoke(null, $without));

        $unverified = new Cause(['wallet_address' => '0x' . str_repeat('b', 40)]);
        $unverified->wallet_verified = 0;
        $this->assertFalse($method->invoke(null, $unverified));
    }

    /** @test */
    public function self_donation_is_flagged()
    {
        $campaign = Cause::whereHas('user')->first();
        if (!$campaign || empty($campaign->user->wallet_address)) {
            $this->markTestSkipped('No campaign with a user wallet seeded');
        }

        $result = FraudEngine::checkDonation($campaign, strtolower($campaign->user->wallet_address), 100);

        $this->assertTrue($result['flagged']);
        $this->assertContains('self_donation', $result['flags']);
        $this->assertGreaterThanOrEqual(60, $result['risk_score']);
    }

    /** @test */
    public function clean_donation_passes()
    {
        $campaign = Cause::first();
        if (!$campaign) {
            $this->markTestSkipped('No campaigns seeded');
        }

        $randomWallet = '0x' . substr(hash('sha256', uniqid('', true)), 0, 40);
        $result = FraudEngine::checkDonation($campaign, $randomWallet, 50);

        $this->assertFalse($result['flagged']);
        $this->assertEquals(0, $result['risk_score']);
    }

    /** @test */
    public function dashboard_stats_have_consistent_shape()
    {
        $stats = FraudEngine::getDashboardStats();

        foreach (['total_campaigns', 'high_risk_count', 'medium_risk_count', 'low_risk_count', 'pending_review', 'flagged_campaigns', 'avg_fraud_score'] as $key) {
            $this->assertArrayHasKey($key, $stats);
            $this->assertIsNumeric($stats[$key]);
        }
    }
}
