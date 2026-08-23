@extends('frontend.frontend-page-master')
@section('site-title')
    {{__('Blockchain Explorer')}} - {{__('MediFund Demo Network')}}
@endsection
@section('style')
<style>
.bx-page{--bx-primary:#627EEA;--bx-accent:#00D4AA;--bx-warn:#FFB800;--bx-bg:#0B0E1A;--bx-card:#12162A;--bx-card2:#181D33;--bx-line:#232A47;--bx-text:#EAECF5;--bx-muted:#9AA3BF;--bx-faint:#6B7594;
    min-height:100vh;padding:48px 20px 80px;color:var(--bx-text);
    background:radial-gradient(1000px 520px at 12% -8%,rgba(98,126,234,.14),transparent 60%),radial-gradient(900px 480px at 92% 108%,rgba(0,212,170,.10),transparent 60%),var(--bx-bg);
    font-family:'Inter','Segoe UI',system-ui,-apple-system,sans-serif}
.bx-wrap{max-width:1140px;margin:0 auto}
.bx-head{text-align:center;margin-bottom:30px}
.bx-title{font-size:30px;font-weight:800;letter-spacing:-.02em;margin:0 0 6px;display:flex;align-items:center;justify-content:center;gap:12px}
.bx-title i{background:linear-gradient(135deg,var(--bx-primary),var(--bx-accent));-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
.bx-sub{color:var(--bx-muted);font-size:13.5px;margin:0}
.bx-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px;margin-bottom:24px}
.bx-stat{background:linear-gradient(180deg,#151A32,#101426);border:1px solid var(--bx-line);border-radius:16px;padding:18px 20px;position:relative;overflow:hidden}
.bx-stat::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(98,126,234,.4),transparent)}
.bx-stat .v{font-size:21px;font-weight:800;letter-spacing:-.02em;font-variant-numeric:tabular-nums}
.bx-stat .k{font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--bx-faint);margin-top:3px}
.bx-stat .ic{position:absolute;top:14px;right:14px;width:34px;height:34px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:13px;background:rgba(98,126,234,.12);color:#8FA3FF}
.bx-search{display:flex;gap:10px;background:var(--bx-card);border:1px solid var(--bx-line);border-radius:14px;padding:8px 8px 8px 20px;margin-bottom:22px;align-items:center}
.bx-search i{color:var(--bx-faint)}
.bx-search input{flex:1;background:transparent;border:none;outline:none;color:var(--bx-text);font-size:14px;font-family:'SFMono-Regular',Consolas,monospace;padding:10px 0}
.bx-search input::placeholder{color:var(--bx-faint)}
.bx-search button{background:linear-gradient(135deg,#627EEA,#4B6CEB);border:none;border-radius:10px;color:#fff;font-weight:800;font-size:13px;padding:11px 26px;cursor:pointer}
.bx-card{background:var(--bx-card);border:1px solid var(--bx-line);border-radius:18px;overflow:hidden;box-shadow:0 18px 50px rgba(0,0,0,.3)}
.bx-table{width:100%;border-collapse:collapse;font-size:13px}
.bx-table th{background:#0E1226;text-align:left;padding:13px 16px;font-size:10.5px;text-transform:uppercase;letter-spacing:.07em;color:var(--bx-faint);font-weight:800;border-bottom:1px solid var(--bx-line)}
.bx-table td{padding:13px 16px;border-bottom:1px solid rgba(35,42,71,.55);vertical-align:middle}
.bx-table tr:last-child td{border-bottom:none}
.bx-table tr:hover td{background:rgba(255,255,255,.02)}
.bx-hash{font-family:'SFMono-Regular',Consolas,monospace;font-size:12px;color:var(--bx-primary);text-decoration:none}
.bx-hash:hover{color:#8FA3FF;text-decoration:underline}
.bx-camp{color:var(--bx-text);text-decoration:none;font-weight:600;font-size:12.5px}
.bx-camp:hover{color:#fff}
.bx-amt{font-weight:800;color:var(--bx-accent);font-variant-numeric:tabular-nums}
.bx-usd{display:block;font-size:10.5px;color:var(--bx-faint);font-weight:500;margin-top:2px;font-variant-numeric:tabular-nums}
.bx-badge{display:inline-flex;align-items:center;gap:5px;border-radius:99px;padding:4px 11px;font-size:10.5px;font-weight:800;letter-spacing:.03em}
.bx-b-ok{background:rgba(0,212,170,.1);border:1px solid rgba(0,212,170,.3);color:var(--bx-accent)}
.bx-b-out{background:rgba(98,126,234,.1);border:1px solid rgba(98,126,234,.3);color:#8FA3FF}
.bx-b-in{background:rgba(255,184,0,.08);border:1px solid rgba(255,184,0,.25);color:var(--bx-warn)}
.bx-time{color:var(--bx-faint);font-size:12px;white-space:nowrap}
.bx-empty{text-align:center;padding:60px 20px;color:var(--bx-muted)}
.bx-empty i{font-size:38px;color:var(--bx-faint);margin-bottom:14px;display:block}
.bx-pag{display:flex;justify-content:center;gap:6px;padding:18px}
.bx-pag a,.bx-pag span{min-width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;border-radius:10px;border:1px solid var(--bx-line);color:var(--bx-muted);font-size:12.5px;font-weight:700;text-decoration:none;padding:0 10px}
.bx-pag span.current,.bx-pag a:hover{background:rgba(98,126,234,.15);border-color:var(--bx-primary);color:#fff}
@media(max-width:768px){.bx-table .hide-sm{display:none}.bx-table-wrap{overflow-x:auto}}
</style>
@endsection
@section('content')
<div class="bx-page">
    <div class="bx-wrap">
        <div class="bx-head">
            <h1 class="bx-title"><i class="fas fa-cubes"></i> {{__('Blockchain Explorer')}}</h1>
            <p class="bx-sub">{{__('Every donation on MediFund is recorded as an on-chain transaction — fully transparent and verifiable by anyone')}}.</p>
        </div>

        <div class="bx-stats">
            <div class="bx-stat"><i class="ic fas fa-exchange-alt"></i><div class="v">{{number_format($stats['total_tx'])}}</div><div class="k">{{__('Total Transactions')}}</div></div>
            <div class="bx-stat"><i class="ic fab fa-ethereum"></i><div class="v">{{number_format($stats['total_eth'],2)}}</div><div class="k">{{__('ETH Moved (USD ~$'.number_format($stats['total_eth']*3450).')')}}</div></div>
            <div class="bx-stat"><i class="ic fas fa-users"></i><div class="v">{{number_format($stats['unique_donors'])}}</div><div class="k">{{__('Unique Wallets')}}</div></div>
            <div class="bx-stat"><i class="ic fas fa-heartbeat"></i><div class="v">{{number_format($stats['campaigns'])}}</div><div class="k">{{__('Campaigns Funded')}}</div></div>
            <div class="bx-stat"><i class="ic fas fa-layer-group"></i><div class="v">#{{number_format($stats['latest_block'])}}</div><div class="k">{{__('Latest Block')}}</div></div>
        </div>

        <form class="bx-search" method="get" action="{{route('blockchain.explorer')}}">
            <i class="fas fa-search"></i>
            <input type="text" name="q" value="{{old('q',$q)}}" placeholder="{{__('Search by transaction hash, wallet address or block number...')}}">
            <button type="submit">{{__('Search')}}</button>
            @if($q !== '')<a href="{{route('blockchain.explorer')}}" style="color:var(--bx-faint);font-size:12px;font-weight:700;text-decoration:none;white-space:nowrap;">{{__('Clear')}}</a>@endif
        </form>

        <div class="bx-table-wrap">
        <div class="bx-card">
            <table class="bx-table">
                <thead><tr>
                    <th>{{__('Tx Hash')}}</th>
                    <th>{{__('Campaign')}}</th>
                    <th>{{__('From Wallet')}}</th>
                    <th class="hide-sm">{{__('Block')}}</th>
                    <th>{{__('Amount')}}</th>
                    <th>{{__('Type')}}</th>
                    <th class="hide-sm">{{__('Time')}}</th>
                </tr></thead>
                <tbody>
                @forelse($transactions as $tx)
                    <tr>
                        <td><a class="bx-hash" href="{{route('blockchain.transaction.show',$tx->transaction_hash)}}">{{$tx->short_hash}}</a></td>
                        <td>
                            @if($tx->campaign)
                                <a class="bx-camp" href="{{route('frontend.donations.single',$tx->campaign->slug)}}">{{\Illuminate\Support\Str::limit($tx->campaign->title,34)}}</a>
                            @else<em style="color:var(--bx-faint)">—</em>@endif
                        </td>
                        <td><span class="bx-hash" style="color:var(--bx-muted)">{{$tx->short_wallet}}</span></td>
                        <td class="hide-sm"><span style="font-family:monospace;font-size:12px;color:var(--bx-muted)">#{{number_format($tx->block_number)}}</span></td>
                        <td>
                            <span class="bx-amt">{{number_format($tx->amount,4)}} {{$tx->currency}}</span>
                            <span class="bx-usd">&asymp; ${{number_format($tx->amount*3450,2)}}</span>
                        </td>
                        <td>
                            @if(strtolower($tx->transaction_type)==='withdrawal')
                                <span class="bx-badge bx-b-out"><i class="fas fa-arrow-up" style="font-size:8px"></i>{{__('Withdrawal')}}</span>
                            @elseif(strtolower($tx->status)!=='confirmed')
                                <span class="bx-badge bx-b-in"><i class="fas fa-hourglass-half" style="font-size:8px"></i>{{ucfirst($tx->status)}}</span>
                            @else
                                <span class="bx-badge bx-b-ok"><i class="fas fa-arrow-down" style="font-size:8px"></i>{{__('Donation')}}</span>
                            @endif
                        </td>
                        <td class="hide-sm"><span class="bx-time">{{$tx->created_at?->diffForHumans()}}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="7"><div class="bx-empty"><i class="fas fa-cubes"></i>{{__('No transactions found for your search.')}}</div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        </div>

        @if($transactions->hasPages())
        <div class="bx-pag">{{ $transactions->links() }}</div>
        @endif
    </div>
</div>
@endsection
