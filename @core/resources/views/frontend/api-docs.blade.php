@extends('frontend.frontend-master')
@section('title', __('Public API Documentation'))

@section('content')
<style>
    .api-wrap { max-width: 900px; margin: 0 auto; padding: 60px 20px 90px; }
    .api-head h1 { color: #0f172a; font-size: 30px; font-weight: 800; }
    .api-head p { color: #64748b; margin-top: 8px; line-height: 1.7; }
    .ep-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 22px 26px; margin-bottom: 18px; }
    .ep-method { display: inline-block; font-size: 11.5px; font-weight: 800; letter-spacing: .05em; padding: 4px 10px; border-radius: 7px; }
    .m-get { background: #dcfce7; color: #15803d; }
    .ep-path { font-family: 'Consolas','Courier New',monospace; font-size: 15px; color: #0f172a; font-weight: 700; margin-left: 10px; }
    .ep-desc { color: #475569; font-size: 13.5px; line-height: 1.7; margin-top: 10px; }
    .ep-sample { background: #0f172a; color: #7dd3fc; border-radius: 10px; padding: 14px 16px; font-family: monospace; font-size: 12.5px; margin-top: 12px; overflow-x: auto; white-space: pre; }
</style>
<section>
    <div class="api-wrap">
        <div class="api-head">
            <h1><i class="fas fa-code" style="color:#00D4AA;"></i> {{ __('MediFund Public API') }}</h1>
            <p>{{ __('Read-only transparency endpoints. Every campaign, escrow state and on-chain transaction is queryable — no authentication required. Data is served from the same ledger the platform runs on.') }}</p>
        </div>

        <div class="ep-card">
            <span class="ep-method m-get">GET</span><span class="ep-path">/api/campaigns</span>
            <div class="ep-desc">{{ __('List published campaigns with fraud scores, wallet-verification flags and funding progress.') }}</div>
            <div class="ep-sample">curl http://{{ request()->getHost() }}:8000/api/campaigns
{
  "ok": true,
  "count": 12,
  "data": [ { "id": 61, "title": "Emergency Open-Heart…", "fraud_score": 0,
              "risk_level": "low", "wallet_verified": true, "percent_funded": 74.2, … } ]
}</div>
        </div>

        <div class="ep-card">
            <span class="ep-method m-get">GET</span><span class="ep-path">/api/campaigns/{id}</span>
            <div class="ep-desc">{{ __('Single campaign incl. escrow summary (held / released / refunded USD), the SHA-256 document seal and beneficiary wallet.') }}</div>
            <div class="ep-sample">curl http://{{ request()->getHost() }}:8000/api/campaigns/61
{ "ok": true, "data": { "fraud_score": 0, "document_sha256_seal": "6df628df…",
  "escrow": { "held_usd": 0.49, "released_usd": 0.72, "disputed_rows": 0 }, … } }</div>
        </div>

        <div class="ep-card">
            <span class="ep-method m-get">GET</span><span class="ep-path">/api/tx/{hash}</span>
            <div class="ep-desc">{{ __('Verify any donation, release or refund transaction by its 0x hash — block number, gas fee and confirmation time straight from the ledger.') }}</div>
            <div class="ep-sample">curl http://{{ request()->getHost() }}:8000/api/tx/0x704ede…
{ "ok": true, "data": { "verified": true, "block_number": 19384716,
  "amount": "0.18000000 ETH", "network": "Demo Ethereum", … } }</div>
        </div>
    </div>
</section>
@endsection
