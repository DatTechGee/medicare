<?php

namespace App\Http\Controllers\User;

use App\Cause;
use App\Helpers\AuditLogger;
use App\Http\Controllers\Controller;
use App\Services\HospitalRegistryService;
use App\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HospitalDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    private function hospitalUser()
    {
        $user = Auth::guard('web')->user();
        abort_if(!$user || !$user->isHospital(), 403, __('Hospital account required to access this page.'));

        return $user;
    }

    public function index()
    {
        $user = $this->hospitalUser();

        $assigned = Cause::with(['user', 'verifications'])
            ->whereNotNull('hospital_name')
            ->whereIn('status', ['pending', 'publish'])
            ->get()
            ->filter(function ($c) use ($user) {
                return HospitalRegistryService::matches($c->hospital_name, $user->hospital_name);
            })
            ->values();

        $pending = collect();
        $done = collect();

        foreach ($assigned as $c) {
            $row = $c->verifications->firstWhere('type', 'hospital');
            $c->hospital_vrow = $row;

            if ($row && in_array($row->status, ['verified', 'rejected'], true)) {
                $done->push($c);
            } else {
                $pending->push($c);
            }
        }

        $stats = [
            'assigned' => $assigned->count(),
            'pending' => $pending->count(),
            'completed' => $done->count(),
            'awaitingApproval' => $assigned->where('status', 'pending')->count(),
        ];

        return view('frontend.user.dashboard.hospital-dashboard', [
            'pendingCampaigns' => $pending,
            'doneCampaigns' => $done,
            'stats' => $stats,
            'hospitalName' => $user->hospital_name ?: __('Unassigned Hospital Account'),
        ]);
    }

    public function verify(Request $request, $id)
    {
        $user = $this->hospitalUser();
        $campaign = Cause::findOrFail($id);

        abort_if(!HospitalRegistryService::matches($campaign->hospital_name, $user->hospital_name), 403, __('This campaign is not assigned to your hospital.'));

        Verification::ensureForCampaign($campaign->id);

        $record = Verification::where('campaign_id', $campaign->id)
            ->where('type', 'hospital')
            ->first();

        $record->update([
            'status' => 'verified',
            'verified_by' => 'Hospital: ' . ($user->hospital_name ?: $user->name),
            'notes' => $request->notes ?: __('Authenticity of the patient claim confirmed by the treating hospital.'),
        ]);

        AuditLogger::record(
            'hospital_verified',
            'Cause',
            $campaign->id,
            ['hospital' => $user->hospital_name, 'verification' => 'hospital', 'status' => 'verified']
        );

        $this->recomputeCampaignStatus($campaign);

        return redirect()->back()->with([
            'msg' => __('Hospital confirmation recorded — the patient claim was confirmed by the treating hospital.'),
            'type' => 'success',
        ]);
    }

    public function reject(Request $request, $id)
    {
        $user = $this->hospitalUser();
        $campaign = Cause::findOrFail($id);

        abort_if(!HospitalRegistryService::matches($campaign->hospital_name, $user->hospital_name), 403, __('This campaign is not assigned to your hospital.'));

        Verification::ensureForCampaign($campaign->id);

        $record = Verification::where('campaign_id', $campaign->id)
            ->where('type', 'hospital')
            ->first();

        $record->update([
            'status' => 'rejected',
            'verified_by' => 'Hospital: ' . ($user->hospital_name ?: $user->name),
            'notes' => $request->notes ?: __('The hospital could not confirm the patient claim as stated.'),
        ]);

        AuditLogger::record(
            'hospital_rejected',
            'Cause',
            $campaign->id,
            ['hospital' => $user->hospital_name, 'verification' => 'hospital', 'status' => 'rejected']
        );

        $campaign->update(['verification_status' => 'rejected']);

        return redirect()->back()->with([
            'msg' => __('Hospital claim recorded as not confirmed — the campaign is flagged for review.'),
            'type' => 'danger',
        ]);
    }

    private function recomputeCampaignStatus(Cause $campaign)
    {
        $pendingCount = Verification::where('campaign_id', $campaign->id)
            ->where('status', 'pending')
            ->count();

        $verifiedCount = Verification::where('campaign_id', $campaign->id)
            ->where('status', 'verified')
            ->count();

        $campaign->update([
            'verification_status' => ($pendingCount === 0 && $verifiedCount > 0) ? 'approved' : 'pending',
        ]);
    }
}