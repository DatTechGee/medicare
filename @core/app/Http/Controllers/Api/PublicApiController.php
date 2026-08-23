<?php

namespace App\Http\Controllers\Api;

use App\Cause;
use App\Escrow;
use App\Http\Controllers\Controller;
use App\Services\DemoBlockchainService;
use Illuminate\Http\JsonResponse;

/**
 * Public read-only JSON API — transparency endpoints for researchers,
 * integrators and the docs playground at /api/docs.
 */
class PublicApiController extends Controller
{
    public function campaigns(): JsonResponse
    {
        $campaigns = Cause::where('status', 'publish')
            ->orderByDesc('id')
            ->limit(100)
            ->get(['id', 'slug', 'title', 'excerpt', 'amount', 'raised', 'fraud_score',
                   'wallet_verified', 'wallet_address', 'deadline', 'emmergency', 'created_at']);

        return response()->json([
            'ok' => true,
            'count' => $campaigns->count(),
            'data' => $campaigns->map(function ($c) {
                $score = min(100, (int) $c->fraud_score);
                return [
                    'id' => $c->id,
                    'title' => $c->title,
                    'slug' => $c->slug,
                    'excerpt' => \Str::limit($c->excerpt, 140),
                    'url' => route('frontend.donations.single', $c->slug),
                    'goal_amount' => (float) $c->amount,
                    'raised' => (float) $c->raised,
                    'percent_funded' => round($c->progress_percentage, 2),
                    'fraud_score' => $score,
                    'risk_level' => $score <= 20 ? 'low' : ($score <= 50 ? 'medium' : 'high'),
                    'wallet_verified' => (bool) $c->wallet_verified,
                    'document_sealed' => !empty($c->document_hash),
                    'urgent' => (bool) $c->emmergency,
                    'deadline' => optional($c->deadline)->toDateString(),
                    'created_at' => optional($c->created_at)->toIso8601String(),
                ];
            }),
        ]);
    }

    public function campaign($id): JsonResponse
    {
        $c = Cause::where('status', 'publish')->find($id);

        if (!$c) {
            return response()->json(['ok' => false, 'error' => 'campaign not found'], 404);
        }

        $escrow = [
            'held_usd' => (float) Escrow::where('campaign_id', $c->id)->where('status', 'held')->sum('amount'),
            'released_usd' => (float) Escrow::where('campaign_id', $c->id)->where('status', 'released')->sum('amount'),
            'refunded_usd' => (float) Escrow::where('campaign_id', $c->id)->where('status', 'refunded')->sum('amount'),
            'disputed_rows' => Escrow::where('campaign_id', $c->id)->where('status', 'held')->where('disputed', 1)->count(),
        ];

        return response()->json([
            'ok' => true,
            'data' => [
                'id' => $c->id,
                'title' => $c->title,
                'url' => route('frontend.donations.single', $c->slug),
                'goal_amount' => (float) $c->amount,
                'raised' => (float) $c->raised,
                'percent_funded' => round($c->progress_percentage, 2),
                'fraud_score' => min(100, (int) $c->fraud_score),
                'risk_level' => min(100, (int) $c->fraud_score) <= 20 ? 'low' : (min(100, (int) $c->fraud_score) <= 50 ? 'medium' : 'high'),
                'verification_status' => $c->verification_status,
                'wallet_verified' => (bool) $c->wallet_verified,
                'beneficiary_wallet' => $c->wallet_address,
                'document_sha256_seal' => $c->document_hash,
                'escrow' => $escrow,
            ],
        ]);
    }

    public function transaction(string $hash): JsonResponse
    {
        if (!preg_match('/^0x[a-fA-F0-9]{64}$/', $hash)) {
            return response()->json(['ok' => false, 'error' => 'malformed transaction hash'], 422);
        }

        $result = DemoBlockchainService::verifyTransaction($hash);

        if (!$result['verified']) {
            return response()->json(['ok' => false, 'error' => $result['message']], 404);
        }

        return response()->json(['ok' => true, 'data' => $result]);
    }
}
