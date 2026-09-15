<?php

namespace App\Http\Controllers\Admin;

use App\Cause;
use App\Http\Controllers\Controller;
use App\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    private const BASE_PATH = 'backend.';

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $type = in_array($request->input('type'), ['patient', 'hospital', 'document', 'amount']) ? $request->input('type') : null;

        $query = Verification::with('campaign')->orderBy('id', 'desc');
        if ($type) {
            $query->where('type', $type);
        }
        $verifications = $query->get();

        $pendingCount  = Verification::where('status', 'pending')->count();
        $verifiedCount = Verification::where('status', 'verified')->count();
        $rejectedCount = Verification::where('status', 'rejected')->count();

        /* pending campaigns that need review, regardless of whether checklist rows exist yet */
        $pendingCampaigns = Cause::with(['verifications', 'user'])
            ->where('status', 'pending')
            ->orderBy('id', 'desc')
            ->get();

        foreach ($pendingCampaigns as $p) {
            $p->verification_rows = $p->verifications->count();
        }

        return view(self::BASE_PATH . 'verifications-index', [
            'verifications'   => $verifications,
            'pendingCount'    => $pendingCount,
            'verifiedCount'   => $verifiedCount,
            'rejectedCount'   => $rejectedCount,
            'pendingCampaigns' => $pendingCampaigns,
            'activeType'      => $type,
        ]);
    }

    public function view($id)
    {
        $verification = Verification::with([
            'campaign',
            'campaign.user',
            'campaign.category',
            'campaign.verifications',
            'campaign.gift',
            'campaign.fraud_reports',
        ])->findOrFail($id);

        $campaign = $verification->campaign;

        $relatedVerifications = $campaign
            ? $campaign->verifications()->orderBy('id')->get()
            : collect();

        $medicalDocs = collect();
        if (!empty($campaign->medical_document)) {
            $ids = array_filter(explode('|', $campaign->medical_document));
            $medicalDocs = \App\MediaUpload::whereIn('id', $ids)->get();
        }

        $galleryMedia = collect();
        if (!empty($campaign->image_gallery)) {
            $ids = array_filter(explode('|', $campaign->image_gallery));
            $galleryMedia = \App\MediaUpload::whereIn('id', $ids)->get();
        }

        return view(self::BASE_PATH . 'verifications-view', [
            'verification'       => $verification,
            'campaign'           => $campaign,
            'relatedVerifications' => $relatedVerifications,
            'medicalDocs'        => $medicalDocs,
            'galleryMedia'       => $galleryMedia,
        ]);
    }

    public function sync($campaignId)
    {
        $campaign = Cause::find($campaignId);
        if (!$campaign) {
            return redirect()->back()->with(['msg' => __('Campaign not found'), 'type' => 'danger']);
        }

        $created = Verification::ensureForCampaign($campaign->id);

        return redirect()->back()->with([
            'msg' => __('Verification checklist synced') . (count($created) ? ' (+' . count($created) . ' created)' : ' (already up to date)'),
            'type' => 'success',
        ]);
    }

    public function verify($id)
    {
        $verification = Verification::findOrFail($id);
        $verification->update([
            'status'      => 'verified',
            'verified_by' => Auth::guard('admin')->user()->name,
            'notes'       => request('notes') ?? $verification->notes,
        ]);

        // Check if all verifications for this campaign are complete
        $campaign = Cause::find($verification->campaign_id);
        if ($campaign) {
            $pendingCount = Verification::where('campaign_id', $campaign->id)
                ->where('status', 'pending')
                ->count();

            $verifiedCount = Verification::where('campaign_id', $campaign->id)
                ->where('status', 'verified')
                ->count();

            if ($pendingCount === 0 && $verifiedCount > 0) {
                $campaign->update(['verification_status' => 'approved']);
            } else {
                $campaign->update(['verification_status' => 'pending']);
            }
        }

        return redirect()->back()->with(['msg' => __('Verification Approved'), 'type' => 'success']);
    }

    public function reject($id)
    {
        $verification = Verification::findOrFail($id);
        $verification->update([
            'status'      => 'rejected',
            'verified_by' => Auth::guard('admin')->user()->name,
            'notes'       => request('notes') ?? $verification->notes,
        ]);

        $campaign = Cause::find($verification->campaign_id);
        if ($campaign) {
            $campaign->update(['verification_status' => 'rejected']);
        }

        return redirect()->back()->with(['msg' => __('Verification Rejected'), 'type' => 'danger']);
    }

    public function verifyAll($campaignId)
    {
        $updated = Verification::where('campaign_id', $campaignId)
            ->where('status', 'pending')
            ->update([
                'status'      => 'verified',
                'verified_by' => Auth::guard('admin')->user()->name,
            ]);

        $campaign = Cause::find($campaignId);
        if ($campaign) {
            $pendingCount  = Verification::where('campaign_id', $campaignId)->where('status', 'pending')->count();
            $verifiedCount = Verification::where('campaign_id', $campaignId)->where('status', 'verified')->count();
            if ($pendingCount === 0 && $verifiedCount > 0) {
                $campaign->update(['verification_status' => 'approved']);
            }
        }

        return redirect()->back()->with(['msg' => __('All pending verification items approved'), 'type' => 'success']);
    }

    public function rejectAll($campaignId)
    {
        Verification::where('campaign_id', $campaignId)
            ->where('status', 'pending')
            ->update([
                'status'      => 'rejected',
                'verified_by' => Auth::guard('admin')->user()->name,
            ]);

        $campaign = Cause::find($campaignId);
        if ($campaign) {
            $campaign->update(['verification_status' => 'rejected']);
        }

        return redirect()->back()->with(['msg' => __('Pending verification items rejected'), 'type' => 'danger']);
    }

    public function bulkAction(Request $request)
    {
        $verifications = Verification::whereIn('id', $request->ids);
        foreach ($verifications as $verification) {
            $verification->delete();
        }
        return response()->json(['status' => 'ok']);
    }
}
