@extends("backend.admin-master")
@section("title", __("Blockchain & MetaMask Settings"))
@section("content")
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-d-900 border border-[#e8edf5] rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-[#e8edf5] flex items-center gap-2.5"><div class="w-8 h-8 rounded-lg bg-t-500/10 flex items-center justify-center"><i class="fas fa-cog text-t-400 text-sm"></i></div><span class="text-sm font-bold text-d-100">Blockchain Configuration</span></div>
        <div class="p-6">
            <form action="{{route("admin.blockchain.settings")}}" method="POST" class="space-y-5">@csrf
                <div>
                    <label class="block text-[11px] text-d-500 font-semibold uppercase tracking-wider mb-2">Network Name</label>
                    <input type="text" name="blockchain_network_name" value="{{get_static_option('blockchain_network_name') ?? 'Demo Ethereum Network'}}" class="w-full bg-d-800 border border-[#e8edf5] rounded-xl px-4 py-2.5 text-sm text-d-100 placeholder-d-600 focus:border-t-500 focus:ring-1 focus:ring-t-500/30 outline-none transition">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] text-d-500 font-semibold uppercase tracking-wider mb-2">Chain ID</label>
                        <input type="number" name="blockchain_chain_id" value="{{get_static_option('blockchain_chain_id') ?? 11155111}}" class="w-full bg-d-800 border border-[#e8edf5] rounded-xl px-4 py-2.5 text-sm text-d-100 font-mono focus:border-t-500 outline-none transition">
                        <p class="text-[10px] text-d-600 mt-1">11155111 = Sepolia, 80069 = Polygon Amoy</p>
                    </div>
                    <div>
                        <label class="block text-[11px] text-d-500 font-semibold uppercase tracking-wider mb-2">Currency Symbol</label>
                        <input type="text" name="blockchain_currency" value="{{get_static_option('blockchain_currency') ?? 'ETH'}}" class="w-full bg-d-800 border border-[#e8edf5] rounded-xl px-4 py-2.5 text-sm text-d-100 focus:border-t-500 outline-none transition">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] text-d-500 font-semibold uppercase tracking-wider mb-2">RPC URL</label>
                    <input type="text" name="blockchain_rpc_url" value="{{get_static_option('blockchain_rpc_url') ?? 'https://rpc.sepolia.org'}}" class="w-full bg-d-800 border border-[#e8edf5] rounded-xl px-4 py-2.5 text-sm text-d-100 font-mono focus:border-t-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-[11px] text-d-500 font-semibold uppercase tracking-wider mb-2">Donation Contract Address</label>
                    <input type="text" name="blockchain_contract_address" value="{{get_static_option('blockchain_contract_address')}}" placeholder="0x..." class="w-full bg-d-800 border border-[#e8edf5] rounded-xl px-4 py-2.5 text-sm text-d-100 font-mono placeholder-d-600 focus:border-t-500 outline-none transition">
                    <p class="text-[10px] text-d-600 mt-1">Deployed MediFundDonation contract � shown to donors in MetaMask confirmations</p>
                </div>
                <div>
                    <label class="block text-[11px] text-d-500 font-semibold uppercase tracking-wider mb-2">Escrow Contract Address</label>
                    <input type="text" name="blockchain_escrow_contract_address" value="{{get_static_option('blockchain_escrow_contract_address')}}" placeholder="0x..." class="w-full bg-d-800 border border-[#e8edf5] rounded-xl px-4 py-2.5 text-sm text-d-100 font-mono placeholder-d-600 focus:border-t-500 outline-none transition">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] text-d-500 font-semibold uppercase tracking-wider mb-2">Min Donation ({{get_static_option('blockchain_currency') ?? 'ETH'}})</label>
                        <input type="number" step="0.0001" name="blockchain_min_donation" value="{{get_static_option('blockchain_min_donation') ?? 0.001}}" class="w-full bg-d-800 border border-[#e8edf5] rounded-xl px-4 py-2.5 text-sm text-d-100 font-mono focus:border-t-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-[11px] text-d-500 font-semibold uppercase tracking-wider mb-2">Max Donation ({{get_static_option('blockchain_currency') ?? 'ETH'}})</label>
                        <input type="number" step="0.0001" name="blockchain_max_donation" value="{{get_static_option('blockchain_max_donation') ?? 100}}" class="w-full bg-d-800 border border-[#e8edf5] rounded-xl px-4 py-2.5 text-sm text-d-100 font-mono focus:border-t-500 outline-none transition">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] text-d-500 font-semibold uppercase tracking-wider mb-2">Explorer URL</label>
                    <input type="text" name="blockchain_explorer_url" value="{{get_static_option('blockchain_explorer_url') ?? 'https://sepolia.etherscan.io'}}" class="w-full bg-d-800 border border-[#e8edf5] rounded-xl px-4 py-2.5 text-sm text-d-100 focus:border-t-500 outline-none transition">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] text-d-500 font-semibold uppercase tracking-wider mb-2">Transfer Mode</label>
                        <select name="blockchain_transfer_mode" class="w-full bg-d-800 border border-[#e8edf5] rounded-xl px-4 py-2.5 text-sm text-d-100 focus:border-t-500 outline-none transition">
                            <option value="simulated" {{(get_static_option('blockchain_transfer_mode') ?? 'simulated')==="simulated"?"selected":""}}>Simulated (demo wallet)</option>
                            <option value="real" {{get_static_option('blockchain_transfer_mode')==="real"?"selected":""}}>Real (MetaMask on-chain)</option>
                        </select>
                        <p class="text-[10px] text-d-600 mt-1">Real = donors pay from actual MetaMask wallets via smart contract</p>
                    </div>
                    <div>
                        <label class="block text-[11px] text-d-500 font-semibold uppercase tracking-wider mb-2">Demo Mode</label>
                        <select name="blockchain_demo_mode" class="w-full bg-d-800 border border-[#e8edf5] rounded-xl px-4 py-2.5 text-sm text-d-100 focus:border-t-500 outline-none transition">
                            <option value="enabled" {{(get_static_option('blockchain_demo_mode') ?? 'enabled')==="enabled"?"selected":""}}>Enabled</option>
                            <option value="disabled" {{get_static_option('blockchain_demo_mode')==="disabled"?"selected":""}}>Disabled</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] text-d-500 font-semibold uppercase tracking-wider mb-2">Platform Receiving Wallet (disbursements)</label>
                    <input type="text" name="site_receiving_wallet" value="{{get_static_option('site_receiving_wallet') ?? '0x80354450F4c300F178de2Ab718AbA6D2818CE102'}}" placeholder="0x..." class="w-full bg-d-800 border border-[#e8edf5] rounded-xl px-4 py-2.5 text-sm text-d-100 font-mono placeholder-d-600 focus:border-t-500 outline-none transition">
                    <p class="text-[10px] text-d-600 mt-1">Escrow releases / platform fees are sent to this address</p>
                </div>
                    <div>
                        <label class="block text-[11px] text-d-500 font-semibold uppercase tracking-wider mb-2">Wallet Login</label>
                        <select name="blockchain_wallet_login_enabled" class="w-full bg-d-800 border border-[#e8edf5] rounded-xl px-4 py-2.5 text-sm text-d-100 focus:border-t-500 outline-none transition">
                            <option value="enabled" {{(get_static_option('blockchain_wallet_login_enabled') ?? 'enabled')==="enabled"?"selected":""}}>Enabled</option>
                            <option value="disabled" {{get_static_option('blockchain_wallet_login_enabled')==="disabled"?"selected":""}}>Disabled</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-t-500 to-t-600 hover:from-t-600 hover:to-t-700 text-white text-xs font-semibold transition shadow-lg shadow-t-500/20"><i class="fas fa-save mr-1"></i>Save Settings</button>
            </form>
        </div>
    </div>
    <div class="space-y-6">
        <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-4"><div class="w-10 h-10 rounded-xl bg-[#4285f4]/10 flex items-center justify-center"><i class="fas fa-wallet text-[#4285f4]"></i></div><span class="text-sm font-bold text-d-100">MetaMask Integration</span></div>
            <div class="space-y-3 text-xs text-d-400 leading-relaxed">
                <p><span class="text-d-200 font-semibold">Wallet Login:</span> patients sign a nonce message with MetaMask to authenticate � no password.</p>
                <p><span class="text-d-200 font-semibold">Linked Donations:</span> every donation stores the donor wallet + tx hash, linked to the campaign and patient.</p>
                <p><span class="text-d-200 font-semibold">Fraud Detection:</span> self-donations (patient wallet = donor wallet) are auto-flagged.</p>
            </div>
        </div>
        <div class="bg-d-900 border border-[#e8edf5] rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-4"><div class="w-10 h-10 rounded-xl bg-t-500/10 flex items-center justify-center"><i class="fas fa-info-circle text-t-400"></i></div><span class="text-sm font-bold text-d-100">About Demo Mode</span></div>
            <div class="space-y-3 text-xs text-d-400 leading-relaxed">
                <p>All blockchain transactions are simulated. No real cryptocurrency is used.</p>
                <p>Gas fees are calculated but not actually charged.</p>
                <p>Transaction hashes are randomly generated for demo purposes.</p>
            </div>
        </div>
    </div>
</div>
@endsection
