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
medicare/
├── @core/            Laravel application
│   ├── app/          Controllers, Models, Helpers (FraudEngine, AuditLogger)
│   ├── resources/    Blade views (frontend + admin backend)
│   └── public/       Assets incl. assets/blockchain/medi-fund-web3.js
├── blockchain/       Hardhat project
│   ├── contracts/    The four Solidity contracts
│   ├── scripts/      deploy.js, sync-campaigns.js, fund-wallet.js,
│   │                 check-sync.js, post-deploy-sync.js, demo-transfer.js…
│   └── deployments.json   Contract addresses (deterministic per local node)
├── redeploy.bat      One-click chain bootstrap + contract deploy (Windows)
└── DEPLOY-GITHUB.md  Shared-hosting install from this repo
```

## Run on a New Device (full install)

### What to download first

| # | Tool | Why | Download |
|---|------|-----|----------|
| 1 | XAMPP (PHP 8.2+) | PHP engine (Apache/MySQL not needed — app uses SQLite) | apachefriends.org |
| 2 | Composer | installs PHP packages | getcomposer.org |
| 3 | Node.js LTS 18+ | runs the Hardhat blockchain node | nodejs.org |
| 4 | Git | clone this repo | git-scm.com |
| + | MetaMask extension | browser wallet for test payments | metamask.io |

### Install steps

```bash
git clone https://github.com/DatTechGee/medicare.git
cd medicare/@core

# --- PHP side ---
composer install
copy .env.example .env            (Windows)   # cp on mac/linux
php artisan key:generate
# edit .env:  APP_ENV=local  APP_DEBUG=true  APP_URL=http://127.0.0.1:8000
echo. > database/database.sqlite  (Windows)   # touch on mac/linux
php artisan migrate --seed        # demo users, funded accounts, 6 campaigns

# --- Chain side: just run the portable script ---
cd ../blockchain
npm install                       # first time only (redeploy.bat also does this)
```

Then double-click **`redeploy.bat`** in the repo root (or run it from cmd). It:

1. starts the Hardhat node in its own window (**keep it open**)
2. deploys all four contracts (writes fresh `blockchain/deployments.json`)
3. registers the seeded campaigns on-chain
4. funds your MetaMask wallet with 50 test ETH
5. syncs contract addresses into the app DB + frontend bundle

```bash
# --- App server (second terminal) ---
cd @core
php artisan serve                 # http://127.0.0.1:8000
```

> Contracts must be redeployed on every new device — each Hardhat instance is a fresh
> chain, so old addresses never carry over. `redeploy.bat` handles all of it.
> If deploy fails, an old Hardhat window is still open — close it and rerun.

### MetaMask setup

1. Add network manually:
   - Network name: `MediFund Local Network`
   - RPC URL: `http://127.0.0.1:8545`
   - Chain ID: `31337`
   - Currency: `ETH`
2. **Reload the MetaMask extension** before donating (clears its throttle counter).
3. Donations then use the **real MetaMask popup spending Hardhat test ETH** ("fake"
   money on a local chain — nothing of value is transacted). If no wallet is installed,
   the page automatically falls back to a built-in simulator.

### Demo Accounts (after seeding)

| Role   | URL                  | Username         | Password   |
|--------|----------------------|------------------|------------|
| Admin  | `/login/admin`       | `medifund_admin` | `password` |
| Patient| `/login`             | `rafiq_patient`  | `password` |
| Donor  | `/login`             | `amina_donor`    | `password` |

Login pages use a simple math captcha. Seeded balances: patient **100 ETH**, donor **50 ETH**;
new donors/admins get a 10 ETH faucet automatically.

## Changing the Wallet Address

There are three places, depending on what you want to change:

| What | Where | Notes |
|------|-------|-------|
| **Receiving wallet** (where donor money is sent / who receives payouts) | Admin panel → **Blockchain Settings → Receiving Wallet** (`site_receiving_wallet`) | Live change — feeds the MetaMask donation flow immediately |
| **Payout wallet of seeded patients/campaigns** | `.env` → `MEDIFUND_RECEIVING_WALLET=0xYourAddress` **before** running `migrate --seed` | Seeder writes this into the patient user + all seeded campaigns |
| **Which account gets the 50 test ETH from funding** | `set TARGET=0xYourAddress` before running the funding step (or edit default in `blockchain/scripts/fund-wallet.js`) | Default: `0x80354450F4c300F178de2Ab718AbA6D2818CE102` |

After changing a seeded wallet, rerun `php artisan migrate --seed --force` (or update
the user/campaign wallet in the admin panel) and reload the MetaMask extension.

## Donation Flow

1. Donor connects MetaMask on a campaign page → picks USD amount (auto-converted to ETH at `MEDIFUND_USD_PER_ETH`)
2. Transaction sent to `MediFundDonation` → forwarded to `MediFundEscrow` (funds held)
3. Fraud engine scores the campaign live; flagged campaigns reject donations at contract level
4. Patient requests withdrawal → **admin approves** → `WithdrawController@Withdraw_Approval`
   runs the fraud gate again, requires a verified payout wallet, then records the on-chain disbursement

## Deploy to Shared Hosting (from this repo)

See **[DEPLOY-GITHUB.md](DEPLOY-GITHUB.md)** — SSH in, `git clone`, `composer install`,
`migrate --seed`, done. The site runs in demo mode there (no Hardhat on shared hosting);
real MetaMask↔contract payments work locally, or after redeploying the contracts to a
public testnet such as Sepolia.
