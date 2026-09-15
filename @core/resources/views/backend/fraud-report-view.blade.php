@extends("backend.admin-master")
@section("title", __("Fraud Report") . " #" . $fraudReport->id)
@section("content")
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-6">
            <h3 class="text-base font-bold text-d-100 mb-6 flex items-center gap-2"><i class="fas fa-shield-alt text-red-400"></i>Fraud Risk Assessment</h3>
            <div class="flex justify-center mb-8">
                @php $s = $fraudReport->fraud_score; $c = $s <= 20 ? "#22c55e" : ($s <= 50 ? "#f59e0b" : "#ef4444"); @endphp
                <div class="relative">
                    <svg width="160" height="160" viewBox="0 0 160 160"><circle cx="80" cy="80" r="70" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="10"/><circle cx="80" cy="80" r="70" fill="none" stroke="{{$c}}" stroke-width="10" stroke-dasharray="{{($s/100)*439.82}} 439.82" stroke-linecap="round" style="transform:rotate(-90deg);transform-origin:center"/></svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center"><span class="text-5xl font-extrabold text-d-100">{{$s}}</span><span class="text-sm text-d-500">/100</span></div>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="p-4 rounded-xl bg-d-800 border border-[#e8edf5]"><div class="text-[11px] text-d-500 font-semibold uppercase mb-2">Risk Level</div>{!! $fraudReport->risk_badge !!}</div>
                <div class="p-4 rounded-xl bg-d-800 border border-[#e8edf5]"><div class="text-[11px] text-d-500 font-semibold uppercase mb-2">Recommendation</div><span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-t-500/10 text-t-400">{{str_replace("_"," ",$fraudReport->recommendation)}}</span></div>
                <div class="p-4 rounded-xl bg-d-800 border border-[#e8edf5]"><div class="text-[11px] text-d-500 font-semibold uppercase mb-2">Status</div><span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-500/10 text-amber-400">{{ucfirst($fraudReport->status)}}</span></div>
            </div>
            <h4 class="text-sm font-bold text-d-100 mt-6 mb-4">Verification Checks</h4>
            @php $checks = $fraudReport->check_results ?? []; @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach(["patient_verified"=>"Patient Verified","hospital_verified"=>"Hospital Verified","documents_verified"=>"Documents Verified","no_duplicate"=>"No Duplicate Campaign","amount_normal"=>"Amount Within Range"] as $k=>$l)
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl {{isset($checks[$k])&&$checks[$k]?"bg-green-500/5 border border-green-500/10":"bg-red-500/5 border border-red-500/10"}}">
                    <i class="fas fa-{{isset($checks[$k])&&$checks[$k]?"check-circle text-green-400":"times-circle text-red-400"}}"></i>
                    <span class="text-xs font-semibold text-d-200">{{$l}}</span>
                </div>
                @endforeach
            </div>
</div>
        @if(!empty($ml))
        <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-6">
            <h3 class="text-base font-bold text-d-100 mb-1 flex items-center gap-2"><i class="fas fa-brain text-violet-400"></i>ML Model Prediction
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-violet-500/10 text-violet-400 uppercase tracking-wide">{{$ml['model']['name']}} v{{$ml['model']['version']}}</span></h3>
            <p class="text-[11px] text-d-500 mb-6">{{$ml['model']['architecture']}}</p>
            <div class="flex items-end justify-between mb-6 gap-8">
                <div class="flex-1">
                    <div class="flex justify-between text-xs mb-2"><span class="text-d-500 font-semibold uppercase tracking-wide">Fraud Probability</span><span class="font-extrabold text-d-100">{{$ml['probability_pct']}}%</span></div>
                    <div class="h-3 rounded-full bg-[#f1f5fa] overflow-hidden">
                        @php $pc = $ml['probability'] * 100; $pcColor = $pc >= 50 ? ($pc >= 65 ? "#ef4444" : "#f59e0b") : "#22c55e"; @endphp
                        <div class="h-full rounded-full transition-all" style="width:{{$pc}}%;background:{{$pcColor}};"></div>
                    </div>
                </div>
                <div class="text-center shrink-0">
                    <div class="text-4xl font-extrabold text-d-100">{{$ml['probability_pct']}}<span class="text-lg text-d-500">%</span></div>
                    <div class="text-[10px] text-d-500 font-semibold uppercase mt-1">Predicted Fraud</div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="p-4 rounded-xl bg-d-800 border border-[#e8edf5] text-center"><div class="text-[11px] text-d-500 font-semibold uppercase mb-2">Model Verdict</div>
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $ml['verdict']==='high' ? 'bg-red-500/10 text-red-400' : ($ml['verdict']==='medium' ? 'bg-amber-500/10 text-amber-400' : 'bg-green-500/10 text-green-400') }}">{{strtoupper($ml['verdict'])}} RISK</span></div>
                <div class="p-4 rounded-xl bg-d-800 border border-[#e8edf5] text-center"><div class="text-[11px] text-d-500 font-semibold uppercase mb-2">Confidence</div><span class="text-lg font-extrabold text-d-100">{{$ml['confidence']}}%</span></div>
            </div>
            @if(!empty($ml['top_features']))
            <h4 class="text-sm font-bold text-d-100 mb-3">Top Contributing Features</h4>
            <div class="flex flex-wrap gap-2 mb-6">
                @foreach($ml['top_features'] as $f)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-red-500/5 border border-red-500/15 text-[11px] font-semibold text-d-200"><i class="fas fa-sort-amount-down text-red-400 text-[9px]"></i>{{$f['name']}} <span class="text-d-500">· {{$f['importance']}}%</span></span>
                @endforeach
            </div>
            @endif
            <h4 class="text-sm font-bold text-d-100 mb-3">Model Evaluation (held-out testnet set)</h4>
            <div class="grid grid-cols-5 gap-2 mb-5">
                @foreach(["precision"=>"Precision","recall"=>"Recall","f1"=>"F1","auc"=>"AUC","accuracy"=>"Accuracy"] as $k=>$l)
                <div class="p-3 rounded-xl bg-d-800 border border-[#e8edf5] text-center"><div class="text-[10px] text-d-500 font-semibold uppercase mb-1">{{$l}}</div><div class="text-lg font-extrabold text-d-100">{{$ml['model']['metrics'][$k]}}</div></div>
                @endforeach
            </div>
            <div class="flex items-center justify-between gap-2 flex-wrap">
                <span class="text-[10px] text-d-500"><i class="fas fa-database mr-1"></i>{{$ml['model']['trained_rows']}} · {{$ml['model']['trained_on']}}</span>
                <span class="text-[10px] text-amber-400/80 italic"><i class="fas fa-flask mr-1"></i>{{$ml['disclaimer']}}</span>
            </div>
        </div>
        @endif
        @if(!empty($verifications)&&count($verifications)>0)
        <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-6">
            <h4 class="text-sm font-bold text-d-100 mb-4">Verification Records</h4>
            <table class="w-full">
                <thead><tr class="border-b border-[#e8edf5]"><th class="px-4 py-2 text-left text-[10px] font-bold text-d-500 uppercase">Type</th><th class="px-4 py-2 text-left text-[10px] font-bold text-d-500 uppercase">Status</th><th class="px-4 py-2 text-left text-[10px] font-bold text-d-500 uppercase">Verified By</th><th class="px-4 py-2 text-left text-[10px] font-bold text-d-500 uppercase">Date</th></tr></thead>
                <tbody>@foreach($verifications as $v)<tr class="border-b border-[#e8edf5]"><td class="px-4 py-2 text-xs text-d-300">{{ucfirst($v->type)}}</td><td class="px-4 py-2"><span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-t-500/10 text-t-400">{{ucfirst($v->status)}}</span></td><td class="px-4 py-2 text-xs text-d-400">{{$v->verified_by ?? "-"}}</td><td class="px-4 py-2 text-xs text-d-500">{{$v->created_at->diffForHumans()}}</td></tr>@endforeach</tbody>
            </table>
        </div>
        @endif
    </div>
    <div class="space-y-6">
        <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-6">
            <h4 class="text-sm font-bold text-d-100 mb-4">Campaign Details</h4>
            @if($fraudReport->campaign)
            <div class="space-y-3">
                <div class="flex justify-between text-xs"><span class="text-d-500 font-medium">Title</span><span class="text-d-200 font-medium">{{optional($fraudReport->campaign)->title}}</span></div>
                <div class="flex justify-between text-xs"><span class="text-d-500 font-medium">Patient</span><span class="text-d-200 font-medium">{{optional($fraudReport->campaign)->patient_name ?? "-"}}</span></div>
                <div class="flex justify-between text-xs"><span class="text-d-500 font-medium">Hospital</span><span class="text-d-200 font-medium">{{optional($fraudReport->campaign)->hospital_name ?? "-"}}</span></div>
                <div class="flex justify-between text-xs"><span class="text-d-500 font-medium">Amount</span><span class="text-d-200 font-medium">{{amount_with_currency_symbol(optional($fraudReport->campaign)->amount)}}</span></div>
                <div class="flex justify-between text-xs"><span class="text-d-500 font-medium">Raised</span><span class="text-d-200 font-medium">{{amount_with_currency_symbol(optional($fraudReport->campaign)->raised)}}</span></div>
            </div>
            @endif
        </div>
        @if($fraudReport->status === "pending")
        <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-6">
            <h4 class="text-sm font-bold text-d-100 mb-4">Admin Review</h4>
            <form action="{{route("admin.fraud.review",$fraudReport->id)}}" method="POST" class="space-y-4">@csrf
                <div><label class="block text-[11px] text-d-500 font-semibold uppercase mb-1.5">Admin Notes</label><textarea name="admin_notes" rows="3" class="w-full bg-d-800 border border-[#e8edf5] rounded-xl px-4 py-2.5 text-sm text-d-100 placeholder-d-600 focus:border-t-500 focus:ring-1 focus:ring-t-500/30 outline-none transition" placeholder="Add your notes..."></textarea></div>
                <button type="submit" name="status" value="cleared" class="w-full px-4 py-2.5 rounded-xl bg-green-500 hover:bg-green-600 text-white text-xs font-semibold transition"><i class="fas fa-check mr-1"></i>Clear Campaign</button>
                <button type="submit" name="status" value="flagged" class="w-full px-4 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white text-xs font-semibold transition"><i class="fas fa-flag mr-1"></i>Flag Campaign</button>
            </form>
        </div>
        @endif
        <a href="{{route("admin.fraud.reports")}}" class="block w-full px-4 py-2.5 rounded-xl bg-d-800 border border-[#e8edf5] text-center text-xs font-semibold text-d-300 hover:bg-[#f2f6fc] transition"><i class="fas fa-arrow-left mr-1"></i>Back to Reports</a>
    </div>
</div>
@endsection
