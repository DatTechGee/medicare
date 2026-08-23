<?php

namespace App\Http\Controllers\Admin;

use App\Cause;
use App\Http\Controllers\Controller;
use App\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use App\Helpers\DataTableHelpers\General;

class VerificationController extends Controller
{
    private const BASE_PATH = 'backend.';

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Verification::with('campaign')->orderBy('id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return General::bulkCheckbox($row->id);
                })
                ->addColumn('campaign_title', function ($row) {
                    return optional($row->campaign)->title ?? '-';
                })
                ->addColumn('type_badge', function ($row) {
                    $colors = ['patient' => 'primary', 'hospital' => 'info', 'document' => 'warning', 'amount' => 'secondary'];
                    $color = $colors[$row->type] ?? 'secondary';
                    return '<span class="badge badge-' . $color . '">' . ucfirst($row->type) . '</span>';
                })
                ->addColumn('status_badge', function ($row) {
                    return '<span class="badge badge-' . $row->status_color . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $action = '';
                    $action .= '<a href="' . route('admin.verification.view', $row->id) . '" class="btn btn-sm btn-info" title="View"><i class="fa fa-eye"></i></a> ';
                    if ($row->status === 'pending') {
                        $action .= '<button class="btn btn-sm btn-success verify-btn" data-id="' . $row->id . '" title="Verify"><i class="fa fa-check"></i></button> ';
                        $action .= '<button class="btn btn-sm btn-danger reject-btn" data-id="' . $row->id . '" title="Reject"><i class="fa fa-times"></i></button> ';
                    }
                    return $action;
                })
                ->rawColumns(['action', 'checkbox', 'campaign_title', 'type_badge', 'status_badge'])
                ->make(true);
        }

        $pendingCount = Verification::where('status', 'pending')->count();

        return view(self::BASE_PATH . 'verifications-index', [
            'pendingCount' => $pendingCount,
        ]);
    }

    public function view($id)
    {
        $verification = Verification::with('campaign')->findOrFail($id);
        return view(self::BASE_PATH . 'verifications-view', [
            'verification' => $verification,
        ]);
    }

    public function verify($id)
    {
        $verification = Verification::findOrFail($id);
        $verification->update([
            'status' => 'verified',
            'verified_by' => Auth::guard('admin')->user()->name,
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
                $campaign->update(['verification_status' => 'verified']);
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
            'status' => 'rejected',
            'verified_by' => Auth::guard('admin')->user()->name,
        ]);

        $campaign = Cause::find($verification->campaign_id);
        if ($campaign) {
            $campaign->update(['verification_status' => 'rejected']);
        }

        return redirect()->back()->with(['msg' => __('Verification Rejected'), 'type' => 'danger']);
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
