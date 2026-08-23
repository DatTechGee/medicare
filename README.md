# MediFund — Transparent Medical Crowdfunding on the Blockchain

MediFund is a medical crowdfunding platform where patients launch campaigns, donors pay with
MetaMask, and every donation is screened by a fraud engine and held in an **on-chain escrow
contract** until admin verifies treatment milestones. Built as a final-year/thesis project.

## Highlights

- **Patient campaigns** — rich-text stories, goal in USD (auto ETH conversion), media & medical documents
- **MetaMask donations** — real transactions to deployed Solidity contracts on a local Hardhat chain (chainId 31337)
- **Escrow** — funds are locked in `MediFundEscrow` and released per-campaign to the patient's verified wallet
- **Fraud engine** — 11 weighted trust checks (patient/hospital/document verification, duplicate detection, text quality, wallet reputation…) producing a 0–100 risk score; flagged campaigns become non-donatable
- **Admin control center** — approve/flag campaigns, verify wallets & hospitals, disburse withdrawals (fraud-gated), fraud reports, blockchain explorer
- **Dashboards** — dark-themed patient/donor dashboards with live stats, progress bars, recent-donation feeds

## Tech Stack

| Layer      | Tech |
|------------|------|
| Backend    | Laravel 9 (PHP 8.2), SQLite |
| Frontend   | Blade + Bootstrap, ethers.js, Font Awesome |
| Blockchain | Solidity ^0.8.x, Hardhat, Ethers v6 |
| Contracts  | `MediFundCampaign`, `MediFundDonation`, `MediFundEscrow`, `MediFundVerification` |

## Project Structure

```
fundorex/
├── @core/            Laravel application
│   ├── app/          Controllers, Models, Helpers (FraudEngine, AuditLogger)
│   ├── resources/    Blade views (frontend + admin backend)
│   └── public/       Assets incl. assets/blockchain/medi-fund-web3.js
└── blockchain/       Hardhat project
    ├── contracts/    The four Solidity contracts
    ├── scripts/      deploy.js, sync-campaigns.js, fund-wallet.js,
    │                 check-sync.js, post-deploy-sync.js, demo-transfer.js…
    └── deployments.json   Contract addresses (deterministic per local node)
```

## Local Setup

```bash
# 1. PHP side
cd @core
composer install
cp .env.example .env            # or copy from a teammate
php artisan key:generate
# .env: DB_CONNECTION=sqlite  (leave DB_DATABASE empty)
touch database/database.sqlite
php artisan migrate --seed      # seeds demo users/campaigns (MediFundDemoSeeder)

# 2. Chain side (separate terminal)
cd ../blockchain
npm install
npx hardhat compile
npx hardhat node                                    # keep running
npx hardhat run scripts/deploy.js --network localhost
node scripts/post-deploy-sync.js                    # syncs bundle + DB options
node scripts/sync-campaigns.js                      # pushes campaigns on-chain
node scripts/fund-wallet.js                         # tops up your MetaMask test ETH

# 3. App server
cd ../@core
php artisan serve        # http://127.0.0.1:8000
```

Add the Hardhat network to MetaMask manually: RPC `http://127.0.0.1:8545`, chain ID `31337`, currency `ETH`.

### Demo Accounts (after seeding)

| Role   | URL                  | Username         | Password  |
|--------|----------------------|------------------|-----------|
| Admin  | `/login/admin`       | `medifund_admin` | `password`|
| Patient| `/login`             | `rafiq_patient`  | `password`|
| Donor  | `/login`             | `amina_donor`    | `password`|

Login pages use a simple math captcha.

## Donation Flow

1. Donor connects MetaMask on a campaign page → picks USD amount (auto-converted to ETH at `MEDIFUND_USD_PER_ETH`)
2. Transaction sent to `MediFundDonation` → forwarded to `MediFundEscrow` (funds held)
3. Fraud engine scores the campaign live; flagged campaigns reject donations at contract level
4. Patient requests withdrawal → **admin approves** → `WithdrawController@Withdraw_Approval`
   runs the fraud gate again, requires a verified payout wallet, then records the on-chain disbursement

## Deployment

A ready-made Hostinger package (`medifund-hostinger-upload.zip`) installs the app into a
subfolder (`yoursite.com/medifund`) — see `DEPLOY-STEPS.txt` inside the zip. Shared hosting runs
everything except real on-chain payments (no Hardhat node there); those work locally, or after
redeploying the contracts to a public testnet such as Sepolia.
