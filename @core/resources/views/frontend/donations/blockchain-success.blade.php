@extends('frontend.frontend-page-master')
@section('site-title')
    {{__('Payment Successful')}} - {{__('Blockchain Confirmed')}}
@endsection

@section('style')
<style>
:root {
    --blockchain-primary: #627EEA;
    --blockchain-accent: #00D4AA;
    --blockchain-dark: #0D0D1A;
    --blockchain-card: #16162A;
    --blockchain-border: #2A2A4A;
    --blockchain-text: #E0E0E0;
    --glow-primary: rgba(98, 126, 234, 0.3);
    --glow-accent: rgba(0, 212, 170, 0.3);
}

.blockchain-success-page {
    background: linear-gradient(135deg, var(--blockchain-dark) 0%, #0A0A1F 50%, #0D1117 100%);
    min-height: 100vh;
    color: var(--blockchain-text);
    font-family: 'Inter', 'Segoe UI', sans-serif;
    position: relative;
    overflow: hidden;
}

.blockchain-success-page::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle at 50% 30%, var(--glow-accent) 0%, transparent 40%);
    animation: bgPulse 15s ease-in-out infinite;
    z-index: 0;
}

@keyframes bgPulse {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(30px, -30px) scale(1.05); }
    66% { transform: translate(-20px, 20px) scale(0.95); }
}

.blockchain-success-page .container {
    position: relative;
    z-index: 1;
}

.success-card {
    background: rgba(22, 22, 42, 0.85);
    backdrop-filter: blur(20px);
    border: 1px solid var(--blockchain-border);
    border-radius: 24px;
    padding: 50px 40px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    max-width: 600px;
    margin: 0 auto;
}

.success-icon-wrap {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--blockchain-accent), #00B894);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 25px;
    animation: popIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    box-shadow: 0 0 40px var(--glow-accent);
}

@keyframes popIn {
    0% { transform: scale(0); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}

.success-icon-wrap i {
    font-size: 48px;
    color: white;
}

.success-title {
    font-size: 28px;
    font-weight: 800;
    text-align: center;
    margin-bottom: 8px;
    background: linear-gradient(135deg, var(--blockchain-accent), #55EFC4);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.success-subtitle {
    text-align: center;
    color: rgba(224, 224, 224, 0.6);
    font-size: 15px;
    margin-bottom: 30px;
}

.tx-detail-grid {
    display: grid;
    gap: 12px;
    margin-bottom: 30px;
}

.tx-detail-row {
    background: rgba(13, 13, 26, 0.6);
    border: 1px solid var(--blockchain-border);
    border-radius: 14px;
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s ease;
}

.tx-detail-row:hover {
    border-color: var(--blockchain-primary);
    background: rgba(13, 13, 26, 0.8);
}

.tx-detail-label {
    color: rgba(224, 224, 224, 0.5);
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.tx-detail-value {
    color: #fff;
    font-weight: 700;
    font-size: 15px;
    text-align: right;
}

.tx-hash-value {
    font-family: 'Courier New', monospace;
    font-size: 13px;
    color: var(--blockchain-accent);
    background: rgba(0, 212, 170, 0.1);
    padding: 6px 12px;
    border-radius: 8px;
    border: 1px solid rgba(0, 212, 170, 0.2);
    word-break: break-all;
}

.confirmed-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(0, 212, 170, 0.15);
    border: 1px solid rgba(0, 212, 170, 0.3);
    border-radius: 50px;
    padding: 6px 16px;
    font-size: 13px;
    font-weight: 700;
    color: var(--blockchain-accent);
}

.confirmed-badge .dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--blockchain-accent);
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
}

.explorer-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, var(--blockchain-primary), #4B6CEB);
    border: none;
    border-radius: 12px;
    color: white;
    padding: 14px 28px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    width: 100%;
    justify-content: center;
}

.explorer-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px var(--glow-primary);
    color: white;
    text-decoration: none;
}

.secondary-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--blockchain-border);
    border-radius: 12px;
    color: var(--blockchain-text);
    padding: 14px 28px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    width: 100%;
    justify-content: center;
}

.secondary-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: var(--blockchain-primary);
    color: white;
    text-decoration: none;
}

.verification-section {
    background: rgba(98, 126, 234, 0.08);
    border: 1px solid rgba(98, 126, 234, 0.2);
    border-radius: 14px;
    padding: 16px 20px;
    margin-top: 20px;
}

.verify-btn-blockchain {
    background: rgba(98, 126, 234, 0.15);
    border: 1px solid var(--blockchain-primary);
    border-radius: 10px;
    color: var(--blockchain-primary);
    padding: 10px 20px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 13px;
}

.verify-btn-blockchain:hover {
    background: var(--blockchain-primary);
    color: white;
}

.spinner-verify {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(224,224,224,0.3);
    border-top-color: var(--blockchain-accent);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin-right: 8px;
}

@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endsection

@section('content')
<div class="blockchain-success-page py-5">
    <div class="container">
        <div class="success-card">
            <div class="success-icon-wrap">
                <i class="fas fa-check"></i>
            </div>

            <h1 class="success-title">{{__('Payment Successful')}}</h1>
            <p class="success-subtitle">{{__('Your donation has been confirmed on the blockchain')}}</p>

            <div class="tx-detail-grid">
                <div class="tx-detail-row">
                    <span class="tx-detail-label">{{__('Amount')}}</span>
                    <span class="tx-detail-value" style="color: var(--blockchain-accent); font-size: 20px;">
                        {{$transaction->formatted_amount}}
                    </span>
                </div>

                <div class="tx-detail-row">
                    <span class="tx-detail-label">{{__('Campaign')}}</span>
                    <span class="tx-detail-value">{{Str::limit(optional($campaign)->title ?? '-', 35)}}</span>
                </div>

                <div class="tx-detail-row">
                    <span class="tx-detail-label">{{__('Your Wallet')}}</span>
                    <span class="tx-hash-value">{{$transaction->short_wallet}}</span>
                </div>

                <div class="tx-detail-row">
                    <span class="tx-detail-label">{{__('Transaction Hash')}}</span>
                    <span class="tx-hash-value">{{$transaction->short_hash}}</span>
                </div>

                <div class="tx-detail-row">
                    <span class="tx-detail-label">{{__('Block Number')}}</span>
                    <span class="tx-detail-value" style="color: var(--blockchain-primary);">#{{$transaction->block_number}}</span>
                </div>

                <div class="tx-detail-row">
                    <span class="tx-detail-label">{{__('Network')}}</span>
                    <span class="tx-detail-value">{{$transaction->network}}</span>
                </div>

                <div class="tx-detail-row">
                    <span class="tx-detail-label">{{__('Status')}}</span>
                    <span class="confirmed-badge">
                        <span class="dot"></span>
                        @if($causeLog && $causeLog->donation_status === 'mismatch_flagged')
                            {{__('Held — Under Review')}}
                        @else
                            {{__('Confirmed')}}
                        @endif
                    </span>
                </div>

                @if($causeLog && $causeLog->donation_status === 'mismatch_flagged')
                <div style="margin-top:14px;border:1px solid rgba(255,184,0,.4);background:rgba(255,184,0,.08);border-radius:12px;padding:14px 16px;display:flex;gap:10px;align-items:flex-start;">
                    <i class="fas fa-triangle-exclamation" style="color:#FFB800;font-size:18px;margin-top:2px;"></i>
                    <div>
                        <b style="color:#FFB800;font-size:13.5px;">{{__('Amount verification pending')}}</b>
                        <p style="margin:4px 0 0;font-size:12.5px;line-height:1.6;color:rgba(230,232,240,.75);">
                            {{__('Our payment-integrity system detected a difference between the wallet-confirmed amount and the amount recorded for this donation. The funds are safely held in escrow and a mediator will review the transaction shortly.')}}
                        </p>
                    </div>
                </div>
                @endif

                <div class="tx-detail-row">
                    <span class="tx-detail-label">{{__('Gas Fee')}}</span>
                    <span class="tx-detail-value" style="color: rgba(224,224,224,0.7);">{{$transaction->gas_fee}}</span>
                </div>

                <div class="tx-detail-row">
                    <span class="tx-detail-label">{{__('Time')}}</span>
                    <span class="tx-detail-value" style="font-size: 13px;">{{$transaction->confirmed_at ? $transaction->confirmed_at->diffForHumans() : '-'}}</span>
                </div>
            </div>

            <div class="d-grid gap-2">
                <a href="{{\App\Services\DemoBlockchainService::getExplorerUrl($transaction->transaction_hash)}}" target="_blank" class="explorer-btn">
                    <i class="fas fa-external-link-alt"></i>
                    {{__('View on Block Explorer')}}
                </a>

                <div class="verification-section">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div style="color: #fff; font-weight: 600; font-size: 14px;">{{__('Verify Transaction')}}</div>
                            <div style="color: rgba(224,224,224,0.5); font-size: 12px;">{{__('Click to verify on-chain')}}</div>
                        </div>
                        <button class="verify-btn-blockchain" onclick="verifyTransaction(this)">
                            <i class="fas fa-shield-alt"></i> {{__('Verify')}}
                        </button>
                    </div>
                    <div id="verifyResult" class="mt-3" style="display: none;"></div>
                </div>

                <a href="{{route('blockchain.transaction.show', $transaction->transaction_hash)}}" class="secondary-btn">
                    <i class="fas fa-receipt"></i>
                    {{__('View Full Receipt')}}
                </a>

                <a href="{{route('homepage')}}" class="secondary-btn">
                    <i class="fas fa-home"></i>
                    {{__('Back to Home')}}
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function verifyTransaction(btn) {
    btn.innerHTML = '<span class="spinner-verify"></span> {{__("Verifying...")}}';
    btn.disabled = true;

    fetch('{{route("blockchain.transaction.verify", $transaction->transaction_hash)}}')
        .then(r => r.json())
        .then(data => {
            const resultDiv = document.getElementById('verifyResult');
            resultDiv.style.display = 'block';

            if (data.verified) {
                resultDiv.innerHTML = `
                    <div style="background: rgba(0, 212, 170, 0.1); border: 1px solid rgba(0, 212, 170, 0.3); border-radius: 12px; padding: 14px; font-size: 13px;">
                        <div style="color: var(--blockchain-accent); font-weight: 700; margin-bottom: 8px;">
                            <i class="fas fa-check-circle"></i> {{__("Transaction Verified")}}
                        </div>
                        <div style="color: rgba(224,224,224,0.7);">
                            Block: #${data.block_number} &bull; Status: ${data.status} &bull; Amount: ${data.amount}
                        </div>
                    </div>
                `;
                btn.innerHTML = '<i class="fas fa-check"></i> {{__("Verified")}}';
                btn.style.background = 'var(--blockchain-accent)';
                btn.style.color = 'white';
                btn.style.borderColor = 'var(--blockchain-accent)';
            } else {
                resultDiv.innerHTML = `
                    <div style="background: rgba(255, 107, 107, 0.1); border: 1px solid rgba(255, 107, 107, 0.3); border-radius: 12px; padding: 14px; font-size: 13px; color: #ff6b6b;">
                        <i class="fas fa-times-circle"></i> ${data.message || '{{__("Verification failed")}}'}
                    </div>
                `;
                btn.innerHTML = '<i class="fas fa-times"></i> {{__("Failed")}}';
            }
        })
        .catch(() => {
            btn.innerHTML = '<i class="fas fa-shield-alt"></i> {{__("Verify")}}';
            btn.disabled = false;
        });
}
</script>
@endpush
@endsection
