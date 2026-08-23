<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class PatientWalletController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $query = User::whereNotNull('wallet_address')->orderByDesc('wallet_connected_at');

        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('wallet_verified', false);
            } elseif ($request->status === 'verified') {
                $query->where('wallet_verified', true);
            }
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('wallet_address', 'like', "%{$s}%");
            });
        }

        $patients = $query->paginate(12)->withQueryString();
        $pendingCount = User::whereNotNull('wallet_address')->where('wallet_verified', false)->count();

        return view('backend.patient-wallets', compact('patients', 'pendingCount'));
    }

    public function verify(Request $request, $id)
    {
        $patient = User::findOrFail($id);

        if (empty($patient->wallet_address)) {
            return back()->with(['msg' => __('This patient has no connected wallet.'), 'type' => 'warning']);
        }

        $patient->update([
            'wallet_verified' => true,
            'wallet_verified_at' => now(),
            'wallet_verified_by' => auth()->guard('admin')->id(),
        ]);

        \App\Helpers\AuditLogger::record('patient_wallet_verify', 'User', $patient->id, [
            'wallet' => $patient->wallet_address,
        ]);

        return back()->with(['msg' => __('Wallet verified — patient can now receive campaign payouts.'), 'type' => 'success']);
    }

    public function reject(Request $request, $id)
    {
        $patient = User::findOrFail($id);

        $patient->update([
            'wallet_verified' => false,
            'wallet_verified_at' => null,
            'wallet_verified_by' => null,
        ]);

        \App\Helpers\AuditLogger::record('patient_wallet_reject', 'User', $patient->id, [
            'wallet' => $patient->wallet_address,
        ]);

        return back()->with(['msg' => __('Wallet verification revoked. Payouts to this wallet are blocked.'), 'type' => 'success']);
    }
}
