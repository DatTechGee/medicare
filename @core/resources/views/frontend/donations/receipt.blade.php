<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{__('Donation Receipt')}} #{{$donation->id}} - MediFund</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',system-ui,-apple-system,sans-serif;background:#EEF1F6;color:#1a1f2e;padding:32px 16px;-webkit-print-color-adjust:exact;print-color-adjust:exact}
.rc{max-width:640px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 10px 40px rgba(15,20,40,.12)}
.rc-head{background:linear-gradient(135deg,#627EEA,#00D4AA);color:#fff;padding:28px 34px;display:flex;justify-content:space-between;align-items:center}
.rc-brand{font-size:21px;font-weight:800;display:flex;align-items:center;gap:9px}
.rc-tag{font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:99px;padding:5px 13px}
.rc-body{padding:30px 34px}
.rc-amount{ text-align:center;padding:24px;border:2px dashed #D8DEF0;border-radius:14px;margin-bottom:26px;background:#F8FAFE}
.rc-amount .lbl{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.09em;color:#7c86a0;margin-bottom:6px}
.rc-amount .val{font-size:38px;font-weight:800;color:#141428;letter-spacing:-.02em}
.rc-amount .eth{font-size:13px;color:#627EEA;font-weight:700;margin-top:4px}
.rc-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px 22px;margin-bottom:26px}
.rc-row{padding:12px 14px;background:#F7F9FD;border-radius:11px}
.rc-row.full{grid-column:1/-1}
.rc-row .k{font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.07em;color:#7c86a0;margin-bottom:4px}
.rc-row .v{font-size:13.5px;font-weight:600;color:#1a1f2e;word-break:break-word}
.mono{font-family:'SFMono-Regular',Consolas,monospace;font-size:12px !important}
.tx-ok{display:inline-flex;align-items:center;gap:6px;background:#E7FAF4;color:#06A77D;border-radius:99px;padding:5px 14px;font-size:12px;font-weight:800}
.rc-foot{border-top:1px solid #EAEFF7;padding:20px 34px;font-size:11.5px;color:#8b93ab;line-height:1.7;text-align:center}
.rc-actions{max-width:640px;margin:18px auto 0;display:flex;gap:10px;justify-content:flex-end}
.rc-btn{display:inline-flex;align-items:center;gap:8px;border:none;border-radius:11px;padding:12px 24px;font-size:13.5px;font-weight:800;cursor:pointer;text-decoration:none}
.rc-btn-print{background:linear-gradient(135deg,#627EEA,#4B6CEB);color:#fff}
.rc-btn-back{background:#fff;color:#3d4560;border:1.5px solid #D8DEF0}
@media print{
    body{background:#fff;padding:0}
    .rc{box-shadow:none;border-radius:0}
    .rc-actions{display:none}
}
</style>
</head>
<body>
<div class="rc-actions">
    <a class="rc-btn rc-btn-back" href="{{url('/')}}"><i class="fas fa-arrow-left"></i> {{__('Back to site')}}</a>
    <button class="rc-btn rc-btn-print" onclick="window.print()"><i class="fas fa-print"></i> {{__('Print / Save as PDF')}}</button>
</div>

<div class="rc" style="margin-top:18px;">
    <div class="rc-head">
        <div class="rc-brand"><i class="fas fa-heartbeat"></i> MediFund</div>
        <span class="rc-tag">{{__('Blockchain Donation Receipt')}}</span>
    </div>
    <div class="rc-body">
        <div class="rc-amount">
            <div class="lbl">{{__('Amount Received')}}</div>
            <div class="val">{{amount_with_currency_symbol($donation->amount)}}</div>
            @if($donation->payment_gateway === 'blockchain_demo' || $donation->payment_type === 'crypto')
                <?php $eth = round((float)$donation->amount / 3450, 6); ?>
                <div class="eth">&asymp; {{number_format($eth,6)}} ETH @ 1 ETH = $3,450</div>
            @endif
        </div>

        <div class="rc-grid">
            <div class="rc-row"><div class="k">{{__('Receipt No.')}}</div><div class="v mono">MF-{{str_pad($donation->id,6,'0',STR_PAD_LEFT)}}</div></div>
            <div class="rc-row"><div class="k">{{__('Date')}}</div><div class="v">{{$donation->created_at?->format('d M Y, H:i')}}</div></div>
            <div class="rc-row"><div class="k">{{__('Donor Name')}}</div><div class="v">{{$donation->anonymous ? 'Anonymous Donor' : $donation->name}}</div></div>
            <div class="rc-row"><div class="k">{{__('Status')}}</div><div class="v"><span class="tx-ok"><i class="fas fa-check-circle"></i> {{__('Confirmed')}}</span></div></div>
            <div class="rc-row full"><div class="k">{{__('Campaign')}}</div><div class="v">{{$campaign?->title ?? '-'}}</div></div>
            @if($campaign?->wallet_address)
                <div class="rc-row full"><div class="k">{{__('Beneficiary Wallet')}} ({{__('verified')}})</div><div class="v mono">{{strtolower($campaign->wallet_address)}}</div></div>
            @endif
            @if($donation->donor_wallet_address)
                <div class="rc-row full"><div class="k">{{__('Donor Wallet')}}</div><div class="v mono">{{strtolower($donation->donor_wallet_address)}}</div></div>
            @endif
            @if($donation->blockchain_transaction_hash)
                <div class="rc-row full"><div class="k">{{__('Transaction Hash')}} ({{__('Demo Network')}})</div><div class="v mono">{{strtolower($donation->blockchain_transaction_hash)}}</div></div>
            @endif
        </div>
    </div>
    <div class="rc-foot">
        {{__('This receipt was generated by MediFund — Transparent Medical Crowdfunding on the Blockchain.')}}<br>
        {{__('Recorded on the demo Ethereum network for educational purposes. No real funds were transferred.')}}
    </div>
</div>
</body>
</html>
