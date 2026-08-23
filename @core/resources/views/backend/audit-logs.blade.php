@extends("backend.admin-master")
@section("title", __("Admin Audit Log"))
@section("content")
<div class="mb-6 flex items-center justify-between">
    <div>
        <h4 class="text-xl font-extrabold text-d-100"><i class="fas fa-clipboard-list text-t-400 mr-2"></i>{{ __("Admin Audit Log") }}</h4>
        <p class="text-xs text-d-500 mt-1">{{ __("Every verify / release / refund / review action with actor, timestamp and IP — full accountability") }}</p>
    </div>
    <form method="get" class="flex items-center gap-2">
        <select name="action" class="form-select form-control-sm bg-d-800 border-[#e8edf5] text-d-200 text-xs rounded-lg px-3 py-2">
            <option value="">{{ __("All actions") }}</option>
            @foreach($actions as $a)
            <option value="{{ $a }}" {{ $currentAction === $a ? "selected" : "" }}>{{ $a }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-3 py-2 rounded-lg text-xs font-bold bg-t-500/15 text-t-400 border border-t-500/25 hover:bg-t-500/25 transition">{{ __("Filter") }}</button>
    </form>
</div>

<div class="bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead><tr class="border-b border-[#e8edf5]">
                <th class="px-5 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">#</th>
                <th class="px-5 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Actor</th>
                <th class="px-5 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Action</th>
                <th class="px-5 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Target</th>
                <th class="px-5 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">Details</th>
                <th class="px-5 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">IP Address</th>
                <th class="px-5 py-3 text-left text-[10px] font-bold text-d-500 uppercase tracking-wider">When</th>
            </tr></thead>
            <tbody>
                @forelse($logs as $log)
                <tr class="border-b border-[#e8edf5] hover:bg-[#f2f6fc]">
                    <td class="px-5 py-2.5 text-[11px] text-d-500">{{ $log->id }}</td>
                    <td class="px-5 py-2.5">
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold {{ $log->user_type === 'admin' ? 'bg-t-500/10 text-t-400' : ($log->user_type === 'user' ? 'bg-blue-500/10 text-blue-400' : 'bg-d-700 text-d-400') }}">{{ $log->user_type ?? 'system' }}{{ $log->user_id ? '#'.$log->user_id : '' }}</span>
                    </td>
                    <td class="px-5 py-2.5">
                        @php $danger = in_array($log->action, ['escrow_refund','wallet_blacklist','document_tamper_alert','milestone_reject']); @endphp
                        <span class="font-mono text-[11px] font-bold {{ $danger ? 'text-red-400' : 'text-green-400' }}">{{ $log->action }}</span>
                    </td>
                    <td class="px-5 py-2.5 text-[11px] text-d-300">{{ $log->model }}#{{ $log->model_id }}</td>
                    <td class="px-5 py-2.5 text-[10.5px] text-d-400 font-mono max-w-[320px] truncate" title="{{ json_encode($log->new_values) }}">{{ Str::limit(json_encode($log->new_values ?? []), 70) }}</td>
                    <td class="px-5 py-2.5 text-[11px] text-d-400 font-mono">{{ $log->ip_address ?? '-' }}</td>
                    <td class="px-5 py-2.5 text-[11px] text-d-500 whitespace-nowrap">{{ optional($log->created_at)->format('M j, H:i:s') }} <span class="opacity-60">({{ optional($log->created_at)->diffForHumans() }})</span></td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-10 text-center text-xs text-d-500">No audit entries yet — perform an admin action (verify wallet, release milestone…)</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
