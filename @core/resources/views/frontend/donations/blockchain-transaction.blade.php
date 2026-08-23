@extends('frontend.frontend-page-master')
@section('site-title')
    {{__('Transaction Details')}} - {{Str::limit($transaction->transaction_hash, 20)}}
@endsection

@section('style')
<style>
:root {
    --blockchain-primary: #627EEA;
    --blockchain-accent: #00D4AA;
    --blockchain-dark: #0D0D1A;
    --blockchain-border: #2A2A4A;
    --blockchain-text: #E0E0E0;
}

.blockchain-tx-page {
    background: linear-gradient(135deg, var(--blockchain-dark) 0%, #0A0A1F 50%, #0D1117 100%);
    min-height: 100vh;
    color: var(--blockchain-text);
    font-family: 'Inter', 'Segoe UI', sans-serif;
    position: relative;
}

.blockchain-tx-page .container { position: relative; z-index: 1; }

.tx-card {
    background: rgba(22, 22, 42, 0.85);
    backdrop-filter: blur(20px);
    border: 1px solid var(--blockchain-border);
    border-radius: 24px;
    padding: 40px;
    max-width: 720px;
    margin: 0 auto;
}

.tx-header {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 25px;
    border-bottom: 1px solid var(--blockchain-border);
}

.tx-header h2 {
    font-size: 22px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 8px;
}

.tx-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 16px;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 700;
}

.tx-badge.confirmed {
    background: rgba(0, 212, 170, 0.15);
    border: 1px solid rgba(0, 212, 170, 0.3);
    color: var(--blockchain-accent);
}

.tx-section {
    margin-bottom: 25px;
}

.tx-section-title {
    color: rgba(224, 224, 224, 0.5);
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin-bottom: 12px;
}

.tx-field {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 0;
    border-bottom: 1px solid rgba(42, 42, 74, 0.5);
}

.tx-field:last-child { border-bottom: none; }

.tx-field-label {
    color: rgba(224, 224, 224, 0.6);
    font-size: 14px;
}

.tx-field-value {
    color: #fff;
    font-weight: 600;
    font-size: 14px;
    text-align: right;
    max-width: 60%;
    word-break: break-all;
}

.tx-hash-box {
    background: rgba(13, 13, 26, 0.8);
    border: 1px solid var(--blockchain-border);
    border-radius: 12px;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.tx-hash-text {
    font-family: 'Courier New', monospace;
    font-size: 13px;
    color: var(--blockchain-accent);
    word-break: break-all;
}

.copy-hash-btn {
    background: rgba(98, 126, 234, 0.15);
    border: 1px solid var(--blockchain-primary);
    border-radius: 8px;
    color: var(--blockchain-primary);
    padding: 6px 14px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.3s;
    white-space: nowrap;
}

.copy-hash-btn:hover {
    background: var(--blockchain-primary);
    color: white;
}

.tx-amount-display {
    text-align: center;
    padding: 25px;
    background: linear-gradient(135deg, rgba(0, 212, 170, 0.08), rgba(98, 126, 234, 0.08));
    border: 1px solid rgba(0, 212, 170, 0.2);
    border-radius: 16px;
    margin-bottom: 25px;
}

.tx-amount-value {
    font-size: 36px;
    font-weight: 800;
    background: linear-gradient(135deg, var(--blockchain-accent), #55EFC4);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.tx-amount-label {
    color: rgba(224, 224, 224, 0.5);
    font-size: 13px;
    margin-top: 5px;
}

.action-buttons {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-top: 25px;
}

.action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 20px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    text-align: center;
}

.action-btn-primary {
    background: linear-gradient(135deg, var(--blockchain-primary), #4B6CEB);
    border: none;
    color: white;
}

.action-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(98, 126, 234, 0.3);
    color: white;
    text-decoration: none;
}

.action-btn-outline {
    background: transparent;
    border: 1px solid var(--blockchain-border);
    color: var(--blockchain-text);
}

.action-btn-outline:hover {
    border-color: var(--blockchain-primary);
    background: rgba(98, 126, 234, 0.1);
    color: white;
    text-decoration: none;
}

.blockchain-logo-footer {
    text-align: center;
    margin-top: 30px;
    padding-top: 25px;
    border-top: 1px solid var(--blockchain-border);
}

.chain-dots {
    display: flex;
    justify-content: center;
    gap: 4px;
    margin-bottom: 10px;
}

.chain-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--blockchain-primary);
    animation: chainDotPulse 2s ease-in-out infinite;
}

.chain-dot:nth-child(2) { animation-delay: 0.3s; }
.chain-dot:nth-child(3) { animation-delay: 0.6s; }

@keyframes chainDotPulse {
    0%, 100% { opacity: 0.3; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.5); }
}
</style>
@endsection

@section('content')
<div class="blockchain-tx-page py-5">
    <div class="container">
        <div class="tx-card">
            <div class="tx-header">
                <h2>{{__('Transaction Details')}}</h2>
                <span class="tx-badge confirmed">
                    <span class="dot" style="width:8px;height:8px;border-radius:50%;background:var(--blockchain-accent);animation:pulse 2s ease-in-out infinite;"></span>
                    {{__('Confirmed on')}} {{$transaction->network}}
                </span>
            </div>

            <div class="tx-amount-display">
                <div class="tx-amount-value">{{$transaction->formatted_amount}}</div>
                <div class="tx-amount-label">{{__('Donation Amount')}}</div>
            </div>

            <div class="tx-section">
                <div class="tx-section-title">{{__('Transaction Information')}}</div>

                <div class="tx-hash-box mb-3">
                    <span class="tx-hash-text" id="txHashFull">{{$transaction->transaction_hash}}</span>
                    <button class="copy-hash-btn" onclick="copyHash()"><i class="fas fa-copy"></i> {{__('Copy')}}</button>
                </div>

                <div class="tx-field">
                    <span class="tx-field-label">{{__('Campaign')}}</span>
                    <span class="tx-field-value">{{optional($campaign)->title ?? '-'}}</span>
                </div>

                <div class="tx-field">
                    <span class="tx-field-label">{{__('Block Number')}}</span>
                    <span class="tx-field-value" style="color: var(--blockchain-primary);">
                        <a href="{{\App\Services\DemoBlockchainService::getBlockExplorerUrl((int)$transaction->block_number)}}" target="_blank" style="color: var(--blockchain-primary);">
                            #{{$transaction->block_number}} <i class="fas fa-external-link-alt" style="font-size:11px;"></i>
                        </a>
                    </span>
                </div>

                <div class="tx-field">
                    <span class="tx-field-label">{{__('Network')}}</span>
                    <span class="tx-field-value">{{$transaction->network}}</span>
                </div>

                <div class="tx-field">
                    <span class="tx-field-label">{{__('Currency')}}</span>
                    <span class="tx-field-value">{{$transaction->currency}}</span>
                </div>

                <div class="tx-field">
                    <span class="tx-field-label">{{__('Transaction Type')}}</span>
                    <span class="tx-field-value">
                        <span class="tx-badge confirmed" style="font-size:12px;">{{ucfirst($transaction->transaction_type)}}</span>
                    </span>
                </div>

                <div class="tx-field">
                    <span class="tx-field-label">{{__('Gas Fee')}}</span>
                    <span class="tx-field-value" style="color: rgba(224,224,224,0.7);">{{$transaction->gas_fee}}</span>
                </div>

                <div class="tx-field">
                    <span class="tx-field-label">{{__('Confirmed At')}}</span>
                    <span class="tx-field-value" style="font-size: 13px;">
                        {{$transaction->confirmed_at ? $transaction->confirmed_at->format('M d, Y h:i:s A') : '-'}}
                    </span>
                </div>
            </div>

            <div class="tx-section">
                <div class="tx-section-title">{{__('Wallet Addresses')}}</div>

                <div class="tx-hash-box mb-3">
                    <div>
                        <div style="color: rgba(224,224,224,0.5); font-size: 11px; margin-bottom: 4px;">{{__('DONOR WALLET')}}</div>
                        <span class="tx-hash-text">{{$transaction->wallet_address}}</span>
                    </div>
                    <button class="copy-hash-btn" onclick="copyText('{{$transaction->wallet_address}}')"><i class="fas fa-copy"></i></button>
                </div>
            </div>

            <div class="action-buttons">
                <a href="{{\App\Services\DemoBlockchainService::getExplorerUrl($transaction->transaction_hash)}}" target="_blank" class="action-btn action-btn-primary">
                    <i class="fas fa-external-link-alt"></i> {{__('Block Explorer')}}
                </a>
                <a href="{{route('homepage')}}" class="action-btn action-btn-outline">
                    <i class="fas fa-home"></i> {{__('Home')}}
                </a>
            </div>

            <div class="blockchain-logo-footer">
                <div class="chain-dots">
                    <span class="chain-dot"></span>
                    <span class="chain-dot"></span>
                    <span class="chain-dot"></span>
                </div>
                <small style="color: rgba(224,224,224,0.3);">{{__('Demo Blockchain Transaction')}}</small>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyHash() {
    const hash = document.getElementById('txHashFull').textContent;
    navigator.clipboard.writeText(hash);
    const btn = event.target.closest('button');
    const original = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check"></i> {{__("Copied!")}}';
    setTimeout(() => btn.innerHTML = original, 2000);
}

function copyText(text) {
    navigator.clipboard.writeText(text);
    const btn = event.target.closest('button');
    const original = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check"></i>';
    setTimeout(() => btn.innerHTML = original, 2000);
}
</script>
@endpush
@endsection
