<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Publishes the Hardhat smart-contract bundle into the Laravel web app so the
 * frontend can talk to the real contracts from the browser:
 *
 *   - public/assets/blockchain/medifund-contracts.json  (network, addresses, ABIs)
 *   - public/assets/blockchain/ethers.min.js            (ethers v6 UMD, served locally)
 *
 * Run after every `deploy` or contract change:
 *   php artisan blockchain:sync-web3
 */
class SyncBlockchainWeb3 extends Command
{
    protected $signature = 'blockchain:sync-web3 {--network= : deployments.json network key (defaults to file content)}';

    protected $description = 'Publish contract ABIs, addresses and ethers.js into public assets for Web3 frontend use';

    private const CONTRACTS = [
        'MediFundCampaign',
        'MediFundDonation',
        'MediFundEscrow',
        'MediFundVerification',
    ];

    public function handle(): int
    {
        $blockchainDir = realpath(base_path('../blockchain'));
        if (!$blockchainDir) {
            $this->error('blockchain/ project folder not found next to @core');
            return 1;
        }

        $deploymentsPath = $blockchainDir . DIRECTORY_SEPARATOR . 'deployments.json';
        if (!is_file($deploymentsPath)) {
            $this->error('deployments.json missing — run: npx hardhat run scripts/deploy.js');
            return 1;
        }

        $deployments = json_decode(file_get_contents($deploymentsPath), true);
        $networkKey = $this->option('network') ?: ($deployments['network'] ?? null);

        /* Support multiple saved networks: deployments.<network>.json */
        if ($networkKey && $networkKey !== ($deployments['network'] ?? null)) {
            $alt = $blockchainDir . DIRECTORY_SEPARATOR . 'deployments.' . $networkKey . '.json';
            if (is_file($alt)) {
                $deployments = json_decode(file_get_contents($alt), true);
            }
        }

        $out = [
            'generatedAt' => now()->toIso8601String(),
            'network'     => $deployments['network'] ?? 'hardhat',
            'chainId'     => (int) ($deployments['chainId'] ?? 31337),
            'chainName'   => config('blockchain.chain_name', 'MediFund Demo Network'),
            'rpcUrl'      => config('blockchain.rpc_url', ''),
            'currency'    => ['name' => 'Ether', 'symbol' => 'ETH', 'decimals' => 18],
            'contracts'   => [],
        ];

        foreach (self::CONTRACTS as $name) {
            $address = $deployments['contracts'][$name] ?? null;
            $artifact = $blockchainDir . DIRECTORY_SEPARATOR . 'artifacts' . DIRECTORY_SEPARATOR
                . 'contracts' . DIRECTORY_SEPARATOR . $name . '.sol' . DIRECTORY_SEPARATOR . $name . '.json';

            if (!$address || !is_file($artifact)) {
                $this->warn("skip {$name}: " . (!$address ? 'no address' : 'artifact missing'));
                continue;
            }

            $abi = json_decode(file_get_contents($artifact), true)['abi'] ?? null;
            if (!$abi) {
                $this->warn("skip {$name}: ABI unreadable");
                continue;
            }

            $out['contracts'][$name] = ['address' => strtolower($address), 'abi' => $abi];
            $this->info("{$name} => {$address}");
        }

        if (empty($out['contracts'])) {
            $this->error('No contracts published');
            return 1;
        }

        $destDir = public_path('assets/blockchain');
        if (!is_dir($destDir)) {
            mkdir($destDir, 0775, true);
        }

        file_put_contents(
            $destDir . DIRECTORY_SEPARATOR . 'medifund-contracts.json',
            json_encode($out, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
        );

        /* ship ethers v6 UMD locally so the page works without internet */
        $ethersSrc = $blockchainDir . DIRECTORY_SEPARATOR . 'node_modules' . DIRECTORY_SEPARATOR
            . 'ethers' . DIRECTORY_SEPARATOR . 'dist' . DIRECTORY_SEPARATOR . 'ethers.umd.min.js';
        if (is_file($ethersSrc)) {
            copy($ethersSrc, $destDir . DIRECTORY_SEPARATOR . 'ethers.min.js');
            $this->info('ethers.min.js published');
        } else {
            $this->warn('ethers dist not found — run npm install in blockchain/');
        }

        $this->info('Web3 bundle written to public/assets/blockchain/');
        return 0;
    }
}
