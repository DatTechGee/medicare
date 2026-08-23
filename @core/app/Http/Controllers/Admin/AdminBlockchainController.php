<?php

namespace App\Http\Controllers\Admin;

use App\BlockchainTransaction;
use App\Cause;
use App\CauseLogs;
use App\Http\Controllers\Controller;
use App\Services\DemoBlockchainService;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Helpers\DataTableHelpers\General;

class AdminBlockchainController extends Controller
{
    private const BASE_PATH = 'backend.';

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = BlockchainTransaction::with(['campaign', 'causeLog'])
                ->orderBy('id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return General::bulkCheckbox($row->id);
                })
                ->addColumn('campaign', function ($row) {
                    return optional($row->campaign)->title ?? '-';
                })
                ->addColumn('wallet_short', function ($row) {
                    return $row->short_wallet;
                })
                ->addColumn('hash_short', function ($row) {
                    return $row->short_hash;
                })
                ->addColumn('formatted_amount', function ($row) {
                    return $row->formatted_amount;
                })
                ->addColumn('type_badge', function ($row) {
                    $colors = ['donation' => 'success', 'withdrawal' => 'warning', 'release' => 'info'];
                    $color = $colors[$row->transaction_type] ?? 'secondary';
                    return '<span class="badge badge-' . $color . '">' . ucfirst($row->transaction_type) . '</span>';
                })
                ->addColumn('status_badge', function ($row) {
                    $colors = ['pending' => 'warning', 'confirmed' => 'success', 'failed' => 'danger'];
                    $color = $colors[$row->status] ?? 'secondary';
                    return '<span class="badge badge-' . $color . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $action = '';
                    $action .= '<a href="' . route('admin.blockchain.view', $row->id) . '" class="btn btn-sm btn-info" title="View"><i class="fa fa-eye"></i></a> ';
                    return $action;
                })
                ->rawColumns(['action', 'checkbox', 'wallet_short', 'hash_short', 'type_badge', 'status_badge'])
                ->make(true);
        }

        $totalTransactions = BlockchainTransaction::count();
        $totalVolume = BlockchainTransaction::where('status', 'confirmed')->sum('amount');
        $pendingTx = BlockchainTransaction::where('status', 'pending')->count();

        return view(self::BASE_PATH . 'blockchain-transactions', [
            'totalTransactions' => $totalTransactions,
            'totalVolume' => $totalVolume,
            'pendingTx' => $pendingTx,
        ]);
    }

    public function view($id)
    {
        $transaction = BlockchainTransaction::with(['campaign', 'causeLog'])->findOrFail($id);

        return view(self::BASE_PATH . 'blockchain-transaction-view', [
            'transaction' => $transaction,
        ]);
    }

    public function settings()
    {
        return view(self::BASE_PATH . 'blockchain-settings');
    }

    public function updateSettings(Request $request)
    {
        $fields = [
            'blockchain_network_name',
            'blockchain_currency',
            'blockchain_explorer_url',
            'blockchain_demo_mode',
            'blockchain_transfer_mode',
            'blockchain_contract_address',
            'blockchain_escrow_contract_address',
            'blockchain_chain_id',
            'blockchain_rpc_url',
            'blockchain_min_donation',
            'blockchain_max_donation',
            'blockchain_wallet_login_enabled',
            'site_receiving_wallet',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                update_static_option($field, $request->input($field));
            }
        }

        return redirect()->back()->with(['msg' => __('Blockchain Settings Updated'), 'type' => 'success']);
    }

    public function config()
    {
        return response()->json([
            'network' => get_static_option('blockchain_network_name') ?? 'Demo Ethereum Network',
            'currency' => get_static_option('blockchain_currency') ?? 'ETH',
            'explorer_url' => get_static_option('blockchain_explorer_url') ?? 'https://etherscan.io',
            'contract_address' => get_static_option('blockchain_contract_address'),
            'escrow_contract_address' => get_static_option('blockchain_escrow_contract_address'),
            'chain_id' => (int) (get_static_option('blockchain_chain_id') ?? 11155111),
            'rpc_url' => get_static_option('blockchain_rpc_url') ?? '',
            'min_donation' => (float) (get_static_option('blockchain_min_donation') ?? 0.001),
            'max_donation' => (float) (get_static_option('blockchain_max_donation') ?? 100),
            'wallet_login_enabled' => (get_static_option('blockchain_wallet_login_enabled') ?? 'enabled') === 'enabled',
            'demo_mode' => (get_static_option('blockchain_demo_mode') ?? 'enabled') === 'enabled',
        ]);
    }
}
