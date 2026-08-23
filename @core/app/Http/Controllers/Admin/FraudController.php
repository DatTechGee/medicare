<?php

namespace App\Http\Controllers\Admin;

use App\Cause;
use App\FraudReport;
use App\Helpers\FraudEngine;
use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use App\Helpers\DataTableHelpers\General;

class FraudController extends Controller
{
    private const BASE_PATH = 'backend.';

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function dashboard()
    {
        $stats = FraudEngine::getOverallStats();
        $recentReports = FraudReport::with('campaign')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        $flaggedCampaigns = FraudReport::where('status', 'flagged')
            ->with('campaign')
            ->orderBy('fraud_score', 'desc')
            ->take(5)
            ->get();

        /* ---- analytics for charts ---- */

        // 1. fraud-score distribution buckets
        $buckets = ['0-20' => 0, '21-40' => 0, '41-60' => 0, '61-80' => 0, '81-100' => 0];
        foreach (FraudReport::select('fraud_score')->get() as $r) {
            $s = (int) $r->fraud_score;
            if ($s <= 20) $buckets['0-20']++;
            elseif ($s <= 40) $buckets['21-40']++;
            elseif ($s <= 60) $buckets['41-60']++;
            elseif ($s <= 80) $buckets['61-80']++;
            else $buckets['81-100']++;
        }

        // 2. flag trend — reports per day, last 14 days
        $trend = [];
        $trendLabels = [];
        for ($i = 13; $i >= 0; $i--) {
            $day = now()->subDays($i)->format('Y-m-d');
            $trendLabels[] = now()->subDays($i)->format('M j');
            $trend[] = FraudReport::whereDate('created_at', $day)->count();
        }

        // 3. escrow held vs released vs refunded (USD)
        $escrow = [
            'held' => (float) \App\Escrow::where('status', 'held')->sum('amount'),
            'released' => (float) \App\Escrow::where('status', 'released')->sum('amount'),
            'refunded' => (float) \App\Escrow::where('status', 'refunded')->sum('amount'),
            'disputed' => (float) \App\Escrow::where('disputed', 1)->where('status', 'held')->sum('amount'),
        ];

        // 4. duplicate clusters — campaigns whose fuzzy-duplicate check failed
        $clusters = [];
        $dupReports = FraudReport::whereNotNull('evidence')->get();
        foreach ($dupReports as $rep) {
            $ev = $rep->evidence ?? [];
            if (!empty($ev['no_fuzzy_duplicate']) && !($ev['no_fuzzy_duplicate']['pass'] ?? true)) {
                $clusters[] = [
                    'campaign_id' => $rep->campaign_id,
                    'title' => optional($rep->campaign)->title ?? "campaign#{$rep->campaign_id}",
                    'detail' => $ev['no_fuzzy_duplicate']['detail'] ?? '',
                ];
            }
        }

        // 5. payment-integrity flags
        $paymentFlags = FraudReport::where('check_results->type', 'payment_integrity')->count();

        // 6. recent audit trail
        $auditLogs = \App\Helpers\AuditLogger::recent(12);

        return view(self::BASE_PATH . 'fraud-dashboard', [
            'stats' => $stats,
            'recentReports' => $recentReports,
            'flaggedCampaigns' => $flaggedCampaigns,
            'buckets' => $buckets,
            'trendLabels' => $trendLabels,
            'trendData' => $trend,
            'escrow' => $escrow,
            'clusters' => $clusters,
            'paymentFlags' => $paymentFlags,
            'auditLogs' => $auditLogs,
        ]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = FraudReport::with('campaign')->orderBy('id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return General::bulkCheckbox($row->id);
                })
                ->addColumn('campaign_title', function ($row) {
                    return optional($row->campaign)->title ?? '-';
                })
                ->addColumn('score_badge', function ($row) {
                    $color = $row->score_color;
                    return '<span class="badge badge-' . $color . '">' . $row->fraud_score . '/100</span>';
                })
                ->addColumn('risk_badge', function ($row) {
                    return $row->risk_badge;
                })
                ->addColumn('status_badge', function ($row) {
                    $colors = ['pending' => 'warning', 'reviewed' => 'info', 'cleared' => 'success', 'flagged' => 'danger'];
                    $color = $colors[$row->status] ?? 'secondary';
                    return '<span class="badge badge-' . $color . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $action = '';
                    $action .= '<a href="' . route('admin.fraud.view', $row->id) . '" class="btn btn-sm btn-info" title="View"><i class="fa fa-eye"></i></a> ';
                    return $action;
                })
                ->rawColumns(['action', 'checkbox', 'score_badge', 'risk_badge', 'status_badge'])
                ->make(true);
        }

        return view(self::BASE_PATH . 'fraud-reports');
    }

    public function view($id)
    {
        $fraudReport = FraudReport::with(['campaign', 'reviewer'])->findOrFail($id);
        $verifications = Verification::where('campaign_id', $fraudReport->campaign_id)->get();

        return view(self::BASE_PATH . 'fraud-report-view', [
            'fraudReport' => $fraudReport,
            'verifications' => $verifications,
        ]);
    }

    public function review(Request $request, $id)
    {
        $this->validate($request, [
            'status' => 'required|in:cleared,flagged',
            'admin_notes' => 'nullable|string',
        ]);

        $fraudReport = FraudReport::findOrFail($id);
        $fraudReport->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::id(),
        ]);

        // Update campaign status based on review
        $campaign = Cause::find($fraudReport->campaign_id);
        if ($campaign) {
            if ($request->status === 'cleared') {
                $campaign->update(['verification_status' => 'verified']);
            } elseif ($request->status === 'flagged') {
                $campaign->update(['status' => 'draft']);
            }
        }

        return redirect()->back()->with(['msg' => __('Fraud Report Reviewed'), 'type' => 'success']);
    }

    public function bulkAction(Request $request)
    {
        $reports = FraudReport::whereIn('id', $request->ids);
        foreach ($reports as $report) {
            $report->delete();
        }
        return response()->json(['status' => 'ok']);
    }

    /** Full accountability trail */
    public function auditLogs(Request $request)
    {
        $query = \App\AuditLog::orderByDesc('id');

        if ($action = $request->get('action')) {
            $query->where('action', $action);
        }

        $logs = $query->limit(200)->get();
        $actions = \App\AuditLog::select('action')->distinct()->pluck('action');

        return view(self::BASE_PATH . 'audit-logs', [
            'logs' => $logs,
            'actions' => $actions,
            'currentAction' => $request->get('action'),
        ]);
    }
}
