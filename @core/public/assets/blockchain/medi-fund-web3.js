/**
 * MediFund Web3 bridge
 * --------------------
 * Connects the website to the real MediFund smart contracts (MediFundCampaign,
 * MediFundDonation, MediFundEscrow) through an injected EIP-1193 provider
 * (MetaMask). Falls back gracefully when no wallet is installed so the rest of
 * the demo keeps working.
 *
 * Requires: /assets/blockchain/medifund-contracts.json + ethers.min.js (v6 UMD)
 *
 *   MediFundWeb3.ready()                     -> Promise<boolean>  (contracts+ethers loaded)
 *   MediFundWeb3.hasInjectedProvider()       -> bool              (real wallet present)
 *   MediFundWeb3.connectWallet()             -> {address, chainId}
 *   MediFundWeb3.ensureNetwork(chainId)      -> switches/adds chain in the wallet
 *   MediFundWeb3.readCampaign(id)            -> on-chain campaign state
 *   MediFundWeb3.readCampaignTrust(id)       -> {isDonatable, fraudScore, status, hospitalVerified...}
 *   MediFundWeb3.escrowBalance(campaignId)   -> BigInt wei held for campaign
 *   MediFundWeb3.donate(campaignId, ethStr)  -> {hash} after 1 confirmation
 *   MediFundWeb3.networkStats()              -> {blockNumber, gasPriceGwei}
 */
window.MediFundWeb3 = (function () {
    'use strict';

    let cfg = null;      // medifund-contracts.json contents
    let ethersLib = null;
    let provider = null; // BrowserProvider (ethers v6)
    let contracts = {};  // name -> ethers Contract (read-only until connect)

    const STATUS_LABELS = {
        0: 'Pending', 1: 'Completed', 2: 'Cancelled',
        3: 'Flagged', 4: 'Active', 5: 'Rejected'
    };

    function hasInjectedProvider() {
        return typeof window.ethereum !== 'undefined';
    }

    async function ready() {
        if (cfg && ethersLib) return true;
        if (typeof window.ethers === 'undefined') return false;
        try {
            const res = await fetch('/assets/blockchain/medifund-contracts.json', { cache: 'no-store' });
            if (!res.ok) return false;
            cfg = await res.json();
        } catch (e) { return false; }
        ethersLib = window.ethers;
        return !!(cfg && cfg.contracts && Object.keys(cfg.contracts).length);
    }

    function makeReadProvider(rpcUrl) {
        /* Prefer an RPC URL from config (works without any wallet), fall back to injected.
           staticNetwork + no batching keeps the request pattern simple so MetaMask
           and the local node never disagree on chain id or choke on batches. */
        const opts = { staticNetwork: true, batchMaxCount: 1, cacheTimeout: -1 };
        if (rpcUrl || (cfg && cfg.rpcUrl)) {
            try {
                return new ethersLib.JsonRpcProvider(
                    rpcUrl || cfg.rpcUrl,
                    { chainId: cfg.chainId, name: cfg.chainName || 'medifund' },
                    opts
                );
            } catch (e) {}
        }
        if (hasInjectedProvider()) return new ethersLib.BrowserProvider(window.ethereum, 'any');
        return null;
    }

    function wireContracts(readProvider) {
        if (!readProvider || !cfg) return false;
        ['MediFundCampaign', 'MediFundDonation', 'MediFundEscrow', 'MediFundVerification'].forEach(function (n) {
            const c = cfg.contracts[n];
            if (c) contracts[n] = new ethersLib.Contract(c.address, c.abi, readProvider);
        });
        return !!contracts.MediFundCampaign;
    }

    async function initReadonly() {
        if (!(await ready())) return false;
        if (contracts.MediFundCampaign) return true;
        const rp = makeReadProvider();
        if (!rp) return false;
        return wireContracts(rp);
    }

    async function ensureNetwork() {
        const target = '0x' + cfg.chainId.toString(16);
        const current = await window.ethereum.request({ method: 'eth_chainId' });
        if (current === target) return current;
        try {
            await window.ethereum.request({
                method: 'wallet_switchEthereumChain',
                params: [{ chainId: target }]
            });
        } catch (err) {
            if (err && err.code === 4902) {
                await window.ethereum.request({
                    method: 'wallet_addEthereumChain',
                    params: [{
                        chainId: target,
                        chainName: cfg.chainName || ('MediFund Network ' + cfg.chainId),
                        nativeCurrency: cfg.currency || { name: 'Ether', symbol: 'ETH', decimals: 18 },
                        rpcUrls: [cfg.rpcUrl || 'http://127.0.0.1:8545']
                    }]
                });
            } else { throw err; }
        }
        return await window.ethereum.request({ method: 'eth_chainId' });
    }

    async function connectWallet() {
        if (!(await ready())) throw new Error('Web3 bundle not available');
        if (!hasInjectedProvider()) throw new Error('NO_WALLET');
        await ensureNetwork();
        provider = new ethersLib.BrowserProvider(window.ethereum);
        const accounts = await provider.send('eth_requestAccounts', []);
        wireContracts(provider);
        const net = await provider.getNetwork();
        return { address: accounts[0], chainId: Number(net.chainId) };
    }

    /**
     * Full on-chain trust snapshot for a campaign id.
     * Mirrors Algorithm 1 state stored by MediFundCampaign.createCampaign.
     */
    async function readCampaignTrust(campaignId) {
        if (!(await initReadonly())) return null;
        try {
            const id = BigInt(campaignId);
            const c = await contracts.MediFundCampaign.getCampaign(id);
            let donatable = null;
            try { donatable = await contracts.MediFundCampaign.isDonatable(id); } catch (e) {}
            return {
                exists: c.exists === true,
                title: c.title,
                fraudScore: Number(c.fraudScore),
                status: Number(c.status),
                statusLabel: STATUS_LABELS[Number(c.status)] || 'Unknown',
                hospitalVerified: c.hospitalVerified === true,
                patientVerified: c.patientVerified === true,
                documentVerified: c.documentVerified === true,
                beneficiary: c.beneficiary,
                goalWei: c.goalAmount ? c.goalAmount.toString() : '0',
                raisedWei: c.raisedAmount ? c.raisedAmount.toString() : '0',
                isDonatable: donatable === true
            };
        } catch (e) {
            return null;
        }
    }

    async function escrowBalance(campaignId) {
        if (!(await initReadonly())) return null;
        try {
            return (await contracts.MediFundEscrow.campaignEscrowBalance(BigInt(campaignId))).toString();
        } catch (e) { return null; }
    }

    async function donationTotals(campaignId) {
        if (!(await initReadonly())) return null;
        try {
            const count = await contracts.MediFundDonation.getDonationCount(BigInt(campaignId));
            let totalWei = ethersLib.BigNumber.from(0);
            try {
                const ids = await contracts.MediFundDonation.getCampaignDonations(BigInt(campaignId));
                for (const id of ids.slice(0, 100)) {
                    const don = await contracts.MediFundDonation.getDonation(id);
                    if (don && don.amount) totalWei = totalWei.add(don.amount);
                }
            } catch (e3) {}
            return {
                totalWei: totalWei.toString(),
                donorCount: null,
                donationCount: Number(count)
            };
        } catch (e) { return null; }
    }

    /** Translate raw ethers/RPC failures into messages a human can act on. */
    function friendlyError(err) {
        if (!err) return 'Unknown error';
        if (err.code === 4001 || err.action === 'rejectRequest') return 'You rejected the transaction in MetaMask.';
        if (err.code === 'INSUFFICIENT_FUNDS') return 'Not enough ETH in your wallet for this donation plus gas.';

        /* ethers v6 wraps unclassified RPC failures as UNKNOWN_ERROR ("could not coalesce error") */
        const inner = (err.info && err.info.error) ? err.info.error : null;
        const raw = [err.reason, err.shortMessage, err.message, inner && inner.message, inner && inner.data && inner.data.message]
            .filter(Boolean).join(' | ');

        if (/returned too many errors|retrying in/i.test(raw))
            return 'MetaMask temporarily throttled the local node (it remembers earlier failed calls). Wait about a minute, or reload the MetaMask extension, then try again.';
        if (/user rejected|User denied/i.test(raw)) return 'You rejected the transaction in MetaMask.';
        if (/insufficient funds/i.test(raw)) return 'Not enough ETH in your wallet for this donation plus gas.';
        if (/Campaign not open|not donatable|Invalid campaign/i.test(raw))
            return 'This campaign is not registered/open on-chain. Run: npx hardhat run scripts/sync-campaigns.js --network localhost';
        if (/could not detect network|network does not support ENS|missing provider|bad response|FETCH|network error/i.test(raw))
            return 'Cannot reach the local blockchain. Start it with: npx hardhat node (then retry).';
        if (/chain|Chain/i.test(raw) && /match|expect|configur/i.test(raw))
            return 'Your wallet is on the wrong network. Switch to MediFund Local Network (chain ' + cfg.chainId + ').';

        console.error('[MediFundWeb3] raw error:', err, inner);
        return raw ? ('Transaction failed: ' + raw.slice(0, 180)) : 'Transaction failed. See browser console for details.';
    }

    /** Payable donation through MediFundDonation.donate (Algorithm 2 custody path). */
    async function donate(campaignId, ethAmountString) {
        if (!(await ready())) throw new Error('Web3 bundle not available');
        if (!hasInjectedProvider()) throw new Error('NO_WALLET');

        await ensureNetwork();

        provider = new ethersLib.BrowserProvider(window.ethereum);
        wireContracts(provider);

        let signer;
        try { signer = await provider.getSigner(); }
        catch (e) { throw new Error(friendlyError(e)); }

        const donation = contracts.MediFundDonation.connect(signer);

        /* parseEther rejects scientific notation & junk — normalise first */
        const clean = String(ethAmountString).trim();
        let value;
        try {
            value = ethersLib.parseEther(clean.replace(/[^0-9.]/g, ''));
        } catch (e) {
            throw new Error('Invalid donation amount: "' + clean + '"');
        }

        try {
            const tx = await donation.donate(BigInt(campaignId), { value: value });
            const receipt = await tx.wait(1);
            return { hash: receipt.hash, blockNumber: receipt.blockNumber, gasUsed: receipt.gasUsed ? receipt.gasUsed.toString() : null };
        } catch (e) {
            throw new Error(friendlyError(e));
        }
    }

    async function networkStats() {
        if (!(await initReadonly())) return null;
        try {
            const blockNumber = await provider.getBlockNumber();
            let gasPriceGwei = null;
            try {
                const fee = await provider.getFeeData();
                if (fee && fee.gasPrice) gasPriceGwei = Number(fee.gasPrice) / 1e9;
            } catch (e) {}
            return { blockNumber: Number(blockNumber), gasPriceGwei: gasPriceGwei };
        } catch (e) { return null; }
    }

    function formatEther(weiString) {
        try { return parseFloat(ethersLib.formatEther(BigInt(weiString))); } catch (e) { return 0; }
    }

    return {
        ready, hasInjectedProvider, connectWallet, ensureNetwork,
        readCampaignTrust, escrowBalance, donationTotals, donate,
        networkStats, formatEther, config: function () { return cfg; }
    };
})();
