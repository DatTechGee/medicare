<?php

namespace App\Helpers;

use App\Cause;
use App\CauseLogs;
use App\FraudReport;
use App\Services\HospitalRegistryService;
use App\Verification;
use Carbon\Carbon;

class FraudEngine
{
    /**
     * Full campaign risk analysis — 11 checks across content,
     * identity/wallet and behaviour attack surfaces.
     * Returns score, risk level, per-check booleans and human-readable evidence.
     */
    public static function analyzeCampaign(Cause $campaign): array
    {
        $score = 0;
        $checks = [];
        $evidence = [];

        $definitions = [
            'patient_verified'    => ['weight' => 25],
            'hospital_verified'   => ['weight' => 25],
            'documents_verified'  => ['weight' => 20],
            'no_duplicate_exact'  => ['weight' => 15],
            'amount_normal'       => ['weight' => 15],
            'wallet_verified'     => ['weight' => 20],
            'text_quality'        => ['weight' => 15],
            'no_fuzzy_duplicate'  => ['weight' => 15],
            'goal_reasonable'     => ['weight' => 15],
            'wallet_reputation'   => ['weight' => 10],
            'account_trust'       => ['weight' => 10],
        ];

        foreach ($definitions as $key => $def) {
            $method = self::checkMethod($key);
            if ($method !== null) {
                [$pass, $why] = call_user_func($method, $campaign);
            } else {
                [$pass, $why] = [true, 'Check unavailable'];
            }
            $checks[$key] = $pass;
            if (!$pass) {
                $score += $def['weight'];
            }
            $evidence[$key] = [
                'pass'     => $pass,
                'points'   => $pass ? 0 : $def['weight'],
                'detail'   => $why,
            ];
        }

        // Determine risk level and recommendation
        if ($score <= 20) {
            $riskLevel = 'low';
            $recommendation = 'AUTO_APPROVE';
        } elseif ($score <= 50) {
            $riskLevel = 'medium';
            $recommendation = 'ADMIN_REVIEW';
        } else {
            $riskLevel = 'high';
            $recommendation = 'FLAG_FOR_REVIEW';
        }

        // Save fraud report
        $fraudReport = FraudReport::updateOrCreate(
            ['campaign_id' => $campaign->id],
            [
                'fraud_score'    => $score,
                'risk_level'     => $riskLevel,
                'check_results'  => $checks,
                'evidence'       => $evidence,
                'recommendation' => $recommendation,
                'status'         => 'pending',
            ]
        );

        // Update campaign fraud score
        $campaign->update(['fraud_score' => $score]);

        return [
            'score'           => $score,
            'max_score'       => array_sum(array_column($definitions, 'weight')),
            'risk_level'      => $riskLevel,
            'checks'          => $checks,
            'evidence'        => $evidence,
            'recommendation'  => $recommendation,
            'fraud_report_id' => $fraudReport->id,
        ];
    }

    private static function checkMethod(string $key)
    {
        static $map = null;
        if ($map === null) {
            $map = [
                'patient_verified'    => [self::class, 'checkPatientVerification'],
                'hospital_verified'   => [self::class, 'checkHospitalVerificationV2'],
                'documents_verified'  => [self::class, 'checkDocuments'],
                'no_duplicate_exact'  => [self::class, 'checkDuplicateExact'],
                'amount_normal'       => [self::class, 'checkAmountRisk'],
                'wallet_verified'     => [self::class, 'checkWalletVerified'],
                'text_quality'        => [self::class, 'checkTextQuality'],
                'no_fuzzy_duplicate'  => [self::class, 'checkFuzzyDuplicate'],
                'goal_reasonable'     => [self::class, 'checkGoalReasonable'],
                'wallet_reputation'   => [self::class, 'checkWalletReputation'],
                'account_trust'       => [self::class, 'checkAccountTrust'],
            ];
        }
        return $map[$key] ?? null;
    }

    /* ------------------------------------------------------------------
     | Identity & document checks
     ------------------------------------------------------------------ */

    private static function checkPatientVerification(Cause $campaign): array
    {
        if (empty($campaign->patient_name)) {
            return [false, 'No patient name declared on the campaign'];
        }

        $verified = Verification::where('campaign_id', $campaign->id)
            ->where('type', 'patient')
            ->where('status', 'verified')
            ->exists();

        return $verified
            ? [true, 'Patient identity verified against submitted documents']
            : [false, "Patient '{$campaign->patient_name}' has no verified identity record"];
    }

    private static function checkHospitalVerificationV2(Cause $campaign): array
    {
        $registry = HospitalRegistryService::verify($campaign->hospital_name);

        $rowVerified = Verification::where('campaign_id', $campaign->id)
            ->where('type', 'hospital')
            ->where('status', 'verified')
            ->exists();

        if (!empty($campaign->hospital_name) && $registry['registered'] && $rowVerified) {
            return [true, "Registered in national registry ({$registry['reg_no']}, {$registry['tier']}) + admin verified"];
        }
        if ($rowVerified) {
            return [true, 'Admin verified — but hospital NOT found in national registry'];
        }
        if (!empty($campaign->hospital_name) && $registry['registered']) {
            return [true, "Found in national registry as {$registry['matched_name']} ({$registry['reg_no']}) — pending admin confirmation"];
        }
        if (empty($campaign->hospital_name)) {
            return [false, 'No treating hospital declared'];
        }

        return [false, "'{$campaign->hospital_name}' not found in national hospital registry and unverified by admin"];
    }

    private static function checkDocuments(Cause $campaign): array
    {
        $verified = Verification::where('campaign_id', $campaign->id)
            ->where('type', 'document')
            ->where('status', 'verified')
            ->exists();

        return $verified
            ? [true, 'Medical documents uploaded and verified']
            : [false, 'Medical documents missing or unverified'];
    }

    /* ------------------------------------------------------------------
     | Content checks
     ------------------------------------------------------------------ */

    private static function checkDuplicateExact(Cause $campaign): array
    {
        $createdAt = $campaign->created_at instanceof Carbon ? $campaign->created_at : Carbon::parse($campaign->created_at);

        /* only punish copycats: a matching campaign that existed BEFORE this one */
        $duplicate = Cause::where('id', '!=', $campaign->id)
            ->where('created_at', '<', $createdAt)
            ->where('created_at', '>=', Carbon::now()->subDays(90))
            ->where(function ($query) use ($campaign) {
                if (!empty($campaign->patient_name)) {
                    $query->orWhere('patient_name', $campaign->patient_name);
                }
                if (!empty($campaign->hospital_name)) {
                    $query->orWhere('hospital_name', $campaign->hospital_name);
                }
            })
            ->orderBy('created_at')
            ->first();

        if (!$duplicate) {
            return [true, 'No earlier campaign claims the same patient/hospital — original fundraiser'];
        }

        return [false, "Copycat risk: campaign #{$duplicate->id} '{$duplicate->title}' claimed the same patient/hospital " . $duplicate->created_at->diffForHumans()];
    }

    private static function checkTextQuality(Cause $campaign): array
    {
        $title = (string) $campaign->title;
        $body = strip_tags((string) $campaign->cause_content);
        $excerpt = (string) $campaign->excerpt;

        $problems = [];

        /* spam / urgency keyword scan */
        $spamWords = ['urgent!!!', 'urgent', 'asap', 'million', 'billion', 'fast money',
            'donate now', 'quick cash', 'immediately needed', 'life saving treatment needed immediately',
            'every dollar counts', 'send money fast'];
        $found = [];
        foreach ($spamWords as $word) {
            if (stripos($title . ' ' . $excerpt . ' ' . substr($body, 0, 400), $word) !== false && !in_array($word, $found)) {
                $found[] = '"' . $word . '"';
            }
        }
        if (count($found) >= 2 || (count($found) === 1 && in_array('"million"', $found))) {
            $problems[] = 'urgency/spam keywords: ' . implode(', ', array_slice($found, 0, 4));
        }

        /* ALL-CAPS ratio on words of 3+ letters */
        preg_match_all('/\b[A-Za-z]{3,}\b/', $title . ' ' . $excerpt, $words);
        $words = $words[0] ?? [];
        if (count($words) >= 4) {
            $capsCount = count(preg_grep('/^[A-Z]{3,}$/', $words));
            if ($capsCount / max(1, count($words)) > 0.35) {
                $problems[] = round(($capsCount / count($words)) * 100) . '% of words are ALL-CAPS';
            }
        }

        /* gibberish tokens (5+ chars without vowels) */
        preg_match_all('/\b[b-df-hj-np-tv-z]{5,}\b/i', $body, $gib);
        $gibberish = array_unique(array_map('strtolower', $gib[0] ?? []));
        if (count($gibberish) >= 2) {
            $problems[] = 'gibberish tokens like "' . implode('", "', array_slice($gibberish, 0, 2)) . '"';
        }

        /* story too short */
        if (mb_strlen(trim($body)) < 150 && mb_strlen(trim($excerpt)) < 60) {
            $problems[] = 'campaign story is too short (' . mb_strlen(trim($body)) . ' chars)';
        }

        if (empty($problems)) {
            return [true, 'Story text reads naturally — no urgency spam, caps abuse or gibberish detected'];
        }

        return [false, implode('; ', $problems)];
    }

    private static function checkFuzzyDuplicate(Cause $campaign): array
    {
        $myTitle = self::normalizeText($campaign->title);

        $others = Cause::where('id', '!=', $campaign->id)
            ->where('created_at', '>=', Carbon::now()->subDays(120))
            ->get(['id', 'title', 'slug']);

        $bestScore = 0;
        $bestMatch = null;

        foreach ($others as $other) {
            similar_text($myTitle, self::normalizeText($other->title), $percent);
            if ($percent > $bestScore) {
                $bestScore = $percent;
                $bestMatch = $other;
            }
        }

        if ($bestMatch && $bestScore > 75) {
            return [false, round($bestScore) . "% title similarity with campaign #{$bestMatch->id} '{$bestMatch->title}' — possible duplicate fundraiser"];
        }

        return [true, $bestMatch ? ("Most similar existing title is only " . round($bestScore) . "% alike") : 'No comparable titles found'];
    }

    /** Benchmark goal caps (USD) per treatment category keyword */
    private static function checkGoalReasonable(Cause $campaign): array
    {
        $benchmarks = [
            'chemotherapy' => 80000,  'cancer' => 120000,    'oncology' => 120000,
            'transplant'   => 150000, 'dialysis' => 60000,   'nicu' => 80000,
            'premature'    => 80000,  'open-heart' => 100000,'heart surgery' => 100000,
            'cardiac'      => 100000, 'brain tumor' => 90000,'spinal cord' => 70000,
            'accident'     => 45000,  'fracture' => 25000,   'treatment' => 60000,
            'surgery'      => 70000,
        ];

        $haystack = strtolower($campaign->title . ' ' . $campaign->excerpt);
        $cap = 50000; // generic default cap
        $matched = 'generic medical treatment';

        foreach ($benchmarks as $keyword => $benchmarkCap) {
            if (stripos($haystack, str_replace('-', ' ', $keyword)) !== false) {
                $cap = $benchmarkCap;
                $matched = $keyword;
                break;
            }
        }

        $goal = (float) $campaign->amount;

        if ($goal > $cap * 3) {
            return [false, number_format($goal) . " USD goal is >3x the {$cap} USD benchmark for '{$matched}' campaigns"];
        }
        if ($goal > $cap * 1.5) {
            return [false, number_format($goal) . " USD goal exceeds the ~{$cap} USD typical range for '{$matched}' campaigns"];
        }

        return [true, number_format($goal) . " USD goal fits the '{$matched}' benchmark range (<= " . number_format((int) ($cap * 1.5)) . ')'];
    }

    /* ------------------------------------------------------------------
     | Wallet & account checks
     ------------------------------------------------------------------ */

    private static function checkWalletVerified(Cause $campaign): array
    {
        if (empty($campaign->wallet_address)) {
            return [false, 'No receiving wallet address declared at all'];
        }
        if (!$campaign->wallet_verified) {
            return [false, 'Receiving wallet not yet verified by an admin (ownership unproven)'];
        }
        return [true, 'Beneficiary wallet ownership verified by admin'];
    }

    private static function checkWalletReputation(Cause $campaign): array
    {
        $wallet = strtolower(trim((string) $campaign->wallet_address));
        if ($wallet === '') {
            return [true, 'No receiving wallet to evaluate (covered by wallet verification check)'];
        }

        /* same wallet used by other campaigns */
        $otherCampaigns = Cause::where('id', '!=', $campaign->id)
            ->whereRaw('LOWER(wallet_address) = ?', [$wallet])
            ->get(['id', 'title', 'fraud_score']);

        $flaggedTwins = $otherCampaigns->where('fraud_score', '>', 50);
        if ($flaggedTwins->isNotEmpty()) {
            $twin = $flaggedTwins->first();
            return [false, "This receiving wallet is already linked to FLAGGED campaign #{$twin->id} '{$twin->title}' (score {$twin->fraud_score})"];
        }

        if ($otherCampaigns->count() > 0) {
            return [true, "Wallet shared with {$otherCampaigns->count()} other low-risk campaign(s) — no negative history"];
        }

        return [true, 'Wallet has no negative history and is unique to this campaign'];
    }

    private static function checkAccountTrust(Cause $campaign): array
    {
        $userId = $campaign->user_id;

        if (empty($userId)) {
            return [true, 'Campaign created directly by MediFund staff'];
        }

        $user = \App\User::find($userId);
        if (!$user) {
            return [true, 'Owner account unavailable for behavioural checks'];
        }

        $problems = [];

        $createdAt = $campaign->created_at instanceof Carbon ? $campaign->created_at : Carbon::parse($campaign->created_at);
        $registeredAt = $user->created_at instanceof Carbon ? $user->created_at : Carbon::parse($user->created_at);

        if ($registeredAt->diffInHours($createdAt) < 24) {
            $problems[] = 'owner account was less than 24h old when the campaign was created';
        }

        $recent = Cause::where('user_id', $userId)
            ->where('id', '!=', $campaign->id)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();
        if ($recent >= 2) {
            $problems[] = "owner launched {$recent} other campaign(s) within the last 7 days";
        }

        $rejectedHistory = Cause::where('user_id', $userId)
            ->where('id', '!=', $campaign->id)
            ->where(function ($q) {
                $q->where('verification_status', 'rejected')->orWhere('fraud_score', '>', 50);
            })->count();
        if ($rejectedHistory > 0) {
            $problems[] = "owner has {$rejectedHistory} previously rejected/high-risk campaign(s)";
        }

        if (empty($problems)) {
            $ageDays = $registeredAt->diffInDays(Carbon::now());
            return [true, "Trusted owner account (age {$ageDays}d, clean history, normal submission pace)"];
        }

        return [false, implode('; ', $problems)];
    }

    /* ------------------------------------------------------------------
     | Legacy single-purpose checks kept for API compatibility
     ------------------------------------------------------------------ */

    private static function checkAmountRisk(Cause $campaign): array
    {
        $amount = (float) $campaign->amount;

        if ($amount > 10000000) {
            return [false, number_format($amount) . ' USD target is implausibly high'];
        }

        if ($amount >= 999999 && $amount <= 1000001) {
            return [false, 'Round one-million-style target is a classic scam pattern'];
        }

        $avgAmount = Cause::where('categories_id', $campaign->categories_id)
            ->where('id', '!=', $campaign->id)
            ->avg('amount');

        if (!empty($avgAmount) && $amount > $avgAmount * 10) {
            return [false, 'Target is more than 10x the average of similar campaigns'];
        }

        return [true, 'Target amount looks proportionate'];
    }

    /** Kept for backwards compatibility with older callers/tests */
    private static function checkDuplicate(Cause $campaign): bool
    {
        [$pass] = self::checkDuplicateExact($campaign);
        return $pass;
    }

    public static function checkDonation(Cause $campaign, string $donorWallet, $amount = 0): array
    {
        $flags = [];
        $riskScore = 0;

        // 1. Self-donation: donor wallet matches campaign owner's wallet
        $ownerWallet = strtolower(optional($campaign->user)->wallet_address ?? '');
        if (!empty($ownerWallet) && $ownerWallet === strtolower($donorWallet)) {
            $flags[] = 'self_donation';
            $riskScore += 60;
        }

        // 2. Velocity: same wallet donating many times in short window
        $recentCount = CauseLogs::where('donor_wallet_address', $donorWallet)
            ->where('cause_id', $campaign->id)
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->count();
        if ($recentCount >= 5) {
            $flags[] = 'high_frequency_donations';
            $riskScore += 25;
        }

        // 3. Round-number pattern: repeated identical amounts
        if ($recentCount >= 3) {
            $identical = CauseLogs::where('donor_wallet_address', $donorWallet)
                ->where('cause_id', $campaign->id)
                ->where('amount', $amount)
                ->where('created_at', '>=', Carbon::now()->subHour())
                ->count();
            if ($identical >= 3) {
                $flags[] = 'repeated_identical_amounts';
                $riskScore += 15;
            }
        }

        if (empty($flags)) {
            return ['flagged' => false, 'flags' => [], 'risk_score' => 0];
        }

        FraudReport::create([
            'campaign_id' => $campaign->id,
            'fraud_score' => min($riskScore, 100),
            'risk_level' => $riskScore >= 50 ? 'high' : ($riskScore >= 25 ? 'medium' : 'low'),
            'check_results' => [
                'type' => 'donation_check',
                'donor_wallet' => $donorWallet,
                'amount' => $amount,
                'flags' => $flags,
            ],
            'recommendation' => $riskScore >= 50 ? 'FLAG_FOR_REVIEW' : 'ADMIN_REVIEW',
            'status' => 'flagged',
        ]);

        return ['flagged' => true, 'flags' => $flags, 'risk_score' => min($riskScore, 100)];
    }

    public static function getOverallStats(): array
    {
        return [
            'total_campaigns' => Cause::count(),
            'flagged_campaigns' => FraudReport::where('status', 'flagged')->count(),
            'pending_review' => FraudReport::where('status', 'pending')->count(),
            'cleared_campaigns' => FraudReport::where('status', 'cleared')->count(),
            'avg_fraud_score' => round(FraudReport::avg('fraud_score') ?? 0, 1),
            'high_risk_count' => FraudReport::where('risk_level', 'high')->count(),
            'medium_risk_count' => FraudReport::where('risk_level', 'medium')->count(),
            'low_risk_count' => FraudReport::where('risk_level', 'low')->count(),
        ];
    }

    public static function getDashboardStats(): array
    {
        $totalCampaigns = Cause::count();
        $highRisk = FraudReport::where('risk_level', 'high')->count();
        $mediumRisk = FraudReport::where('risk_level', 'medium')->count();
        $lowRisk = FraudReport::where('risk_level', 'low')->count();
        $pendingReview = FraudReport::where('status', 'pending')->count();

        return [
            'total_campaigns' => $totalCampaigns,
            'high_risk_count' => $highRisk,
            'medium_risk_count' => $mediumRisk,
            'low_risk_count' => $lowRisk,
            'pending_review' => $pendingReview,
            'flagged_campaigns' => FraudReport::where('status', 'flagged')->count(),
            'avg_fraud_score' => round(FraudReport::avg('fraud_score') ?? 0, 1),
        ];
    }

    private static function normalizeText(?string $text): string
    {
        $text = strtolower(strip_tags((string) $text));
        $text = preg_replace('/[^a-z0-9 ]+/', ' ', $text);

        return trim(preg_replace('/\s+/', ' ', $text ?? ''));
    }
}
