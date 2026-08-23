<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class WalletAuthController extends Controller
{
    public function showWalletLogin()
    {
        return view('frontend.user.wallet-login');
    }

    public function nonce(Request $request)
    {
        $request->validate([
            'address' => 'required|string|size:42|regex:/^0x[a-fA-F0-9]{40}$/',
        ]);

        $address = strtolower($request->input('address'));
        $nonce = strtoupper(Str::random(16));
        $issuedAt = now()->timestamp;

        Cache::put('wallet_nonce_' . $address, ['nonce' => $nonce, 'ts' => $issuedAt], now()->addMinutes(10));

        $message = "MediFund - Medical Crowdfunding DApp\n\n"
            . "Sign this message to prove you own this wallet and login.\n\n"
            . "Address: " . $address . "\n"
            . "Nonce: " . $nonce . "\n"
            . "Issued: " . date('Y-m-d H:i:s', $issuedAt);

        return response()->json([
            'success' => true,
            'message' => $message,
            'nonce' => $nonce,
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'address' => 'required|string|size:42|regex:/^0x[a-fA-F0-9]{40}$/',
            'signature' => 'required|string|min:132|max:134|regex:/^0x[a-fA-F0-9]+$/',
        ]);

        $address = strtolower($request->input('address'));
        $signature = $request->input('signature');

        $cached = Cache::get('wallet_nonce_' . $address);
        if (!$cached) {
            return response()->json([
                'success' => false,
                'message' => __('Nonce expired. Please try again.'),
            ], 422);
        }

        if (!$this->validateSignatureFormat($signature)) {
            return response()->json([
                'success' => false,
                'message' => __('Invalid signature format.'),
            ], 422);
        }

        Cache::forget('wallet_nonce_' . $address);

        $user = User::where('wallet_address', $address)->first();

        if (!$user) {
            $shortAddr = substr($address, 0, 6) . substr($address, -4);
            $baseUsername = 'patient_' . strtolower(substr($address, 2, 8));
            $username = $baseUsername;
            $attempt = 1;
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . '_' . $attempt++;
            }

            $user = User::create([
                'name' => 'Donor ' . $shortAddr,
                'email' => $username . '@wallet.medifund.local',
                'username' => $username,
                'password' => bcrypt(Str::random(32)),
                'email_verified' => 1,
                'role' => 'donor',
                'campaign_permission' => '',
                'wallet_address' => $address,
                'wallet_connected_at' => now(),
            ]);
        } else {
            $user->update(['wallet_connected_at' => now()]);
        }

        Auth::guard('web')->login($user, true);

        session(['wallet_address' => $address]);
        session(['wallet_connected' => true]);

        return response()->json([
            'success' => true,
            'message' => __('Wallet authenticated successfully'),
            'redirect' => route('user.home'),
            'user' => [
                'name' => $user->name,
                'wallet' => $shortAddr ?? substr($address, 0, 6) . '...' . substr($address, -4),
                'is_new' => $user->wasRecentlyCreated,
            ],
        ]);
    }

    private function validateSignatureFormat(string $signature): bool
    {
        $hex = substr($signature, 2);
        if (strlen($hex) !== 130) {
            return false;
        }
        $r = substr($hex, 0, 64);
        $s = substr($hex, 64, 64);
        $v = substr($hex, 128, 2);

        if (!ctype_xdigit($r) || !ctype_xdigit($s) || !in_array(strtolower($v), ['1b', '1c', '00', '01'])) {
            return false;
        }
        if (preg_match('/^0+$/', $r) || preg_match('/^0+$/', $s)) {
            return false;
        }
        return true;
    }
}
