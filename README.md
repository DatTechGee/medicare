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

## Step 1 — Install the Required Software

Install these in order **before** cloning the repo. Nothing else is needed.

| # | Software | Version | Why it's needed | Download |
|---|----------|---------|-----------------|----------|
| 1 | **Git** | latest | clone this repo, pull updates | https://git-scm.com/downloads |
| 2 | **XAMPP** | PHP **8.1+ (8.2 recommended)** | the PHP engine Laravel runs on. Apache/MySQL are NOT needed — the app uses SQLite | https://www.apachefriends.org |
| 3 | **Composer** | 2.x | **optional** — only needed if you rebuild the PHP packages (this repo ships pre-built `vendor/`, so you normally skip it) | https://getcomposer.org/download/ |
| 4 | **Node.js** | LTS **18 or newer** | runs the Hardhat blockchain node + the contract scripts | https://nodejs.org |
| 5 | **MetaMask** | browser extension | the wallet used to make test donations | https://metamask.io/download/ |

When installing **XAMPP**, accept the default PHP version (8.2). On the *Select Components*
screen you can uncheck Apache and MySQL — only PHP is used.

### Verify each installation

Open **one terminal** (Windows: press `Win + R`, type `cmd`, press Enter — or use PowerShell)
and run each line. Every command should print a version, not "not recognized":

```
git --version
php -v
composer -V
node -v
npm -v
```

> **Windows PATH tip:** if `php -v` says "not recognized", add `C:\xampp\php` to your PATH:
> Start → *Edit the system environment variables* → *Environment Variables* → under *System
> variables* select `Path` → *Edit* → *New* → paste `C:\xampp\php` → *OK* → reopen the terminal.
> (Or just always type the full path: `C:\xampp\php\php.exe` below.)

---

## Step 2 — Clone the Project

In that same terminal, run:

```
git clone https://github.com/DatTechGee/medicare.git
cd medicare
```

This downloads the whole project into a folder named `medicare`. From here on, **all commands
must be run from inside `medicare`** (or the `@core`/`blockchain` subfolders as shown).

> **Important — clone, don't download the ZIP.** Use `git clone` as shown above. The project
> ships a pre-built `@core\vendor\` folder inside the repo, so a plain **"Download ZIP"**
> actually **works too** — but a ZIP has a `.git` folder issue-free copy that never needs a
> GitHub token. Either way, you get `vendor` included if you download *after* this commit.
> If your path shows a **`-main` suffix** (e.g. `medicare-main`), you got a ZIP, which is fine —
> just make sure to use the latest download.

---

## Step 3 — Set Up the Database & App Key

The PHP packages are **already included** in the repo as a pre-built `@core\vendor\` folder, so
you normally do **not** need Composer at all.

**Optional — only if you want to rebuild the packages from scratch:**
```
cd medicare\@core
composer install
```
(If Composer asks for a GitHub token on the `Sharifur/paymentgateway` package, that's because
that one dependency is a private repository. You can ignore it — the shipped `vendor\` already
contains everything you need. **Do not run `composer update`** — it will fail and prompt for a
token.)

Generate the app's secret key (this also confirms your PHP setup works — it should print
`Application key [base64:...] set successfully.`):

```
copy .env.example .env
php artisan key:generate
```

> **PowerShell note:** the `copy` command above is written for the classic `cmd` terminal. If
> you're using **PowerShell**, type `Copy-Item .env.example .env` instead — both do the same job.

Open `.env` in a text editor and make sure these four lines read:

```
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=sqlite
```
(leave `DB_DATABASE` empty as-is — the database file is created next)

Create the SQLite database file and load the demo data (users, funded accounts, 6 campaigns):

**Command prompt (cmd):**
```
echo. > database\database.sqlite
php artisan migrate --seed
```

**PowerShell** (the `echo.` above is cmd-only and **fails in PowerShell** — use `New-Item` instead):
```
New-Item database\database.sqlite -ItemType File
php artisan migrate --seed
```

---

## Step 4 — Start the Blockchain & Deploy the Contracts

You now need **two terminals**, because two separate servers must stay running.
**Do not close either window** while you're using the site.

### Terminal A — the blockchain node

Open a new terminal and run:

```
cd medicare\blockchain
npm install
node scripts/deploy.js --network localhost
```

Wait for the script to print "Deployment Summary" with four contract addresses. Now start the
node — **this terminal must keep running**, so leave it open:

```
npx hardhat node
```

Because each Hardhat instance is a fresh chain, the contracts must be wired up after the node
boots. In a **third** terminal run:

```
cd medicare\blockchain
npx hardhat run scripts/sync-campaigns.js --network localhost
npx hardhat run scripts/fund-wallet.js --network localhost
node scripts/post-deploy-sync.js
```
- `sync-campaigns.js`   → registers the seeded campaigns on-chain
- `fund-wallet.js`      → tops up the receiving wallet with 50 test ETH
- `post-deploy-sync.js` → copies the contract addresses into the app DB + frontend bundle

### Terminal B — the app (website) server

Open a new terminal and run:

```
cd medicare\@core
php artisan serve
```

Your website is now live at **http://127.0.0.1:8000** — keep this terminal open too.

> **Windows shortcut:** instead of Terminal A + the third terminal, double-click
> **`redeploy.bat`** in the `medicare` folder. It runs `npm install`, starts the Hardhat
> node in its own window, deploys the contracts, registers campaigns, funds the wallet and
> syncs the addresses. After it finishes, still run `php artisan serve` in a second terminal
> as shown above, and leave the Hardhat window it opened running.

### Demo Accounts (after seeding)

| Role   | URL                  | Username         | Password   |
|--------|----------------------|------------------|------------|
| Admin  | `/login/admin`       | `medifund_admin` | `password` |
| Patient| `/login`             | `rafiq_patient`  | `password` |
| Donor  | `/login`             | `amina_donor`    | `password` |

Login pages use a simple math captcha. Seeded balances: patient **100 ETH**, donor **50 ETH**;
new donors/admins get a 10 ETH faucet automatically.

## Changing the Wallet Address (use your OWN wallet)

**Which kind of wallet?**
An **EVM / Ethereum-compatible wallet** — the same kind of address used by Ethereum,
BSC, Polygon, etc. The recommended one is **MetaMask** (browser extension), but any
wallet that gives you a standard `0x…` address works: Rabby, Coinbase Wallet,
Trust Wallet, Brave Wallet. ❌ Bitcoin/other-chain addresses do **not** work.

Your address looks like `0x` + 40 hex characters, e.g.
`0x80354450F4c300F178de2Ab718AbA6D2818CE102`.
To get yours: open MetaMask → click the address pill at the top → **Copy**.

**Where to put it — 4 places, depending on what should change:**

| What | Where | Notes |
|------|-------|-------|
| **1. Site receiving wallet** (where donor money is sent / escrow beneficiary) | Admin panel → **Blockchain Settings → Receiving Wallet** (`site_receiving_wallet`) | Live change — feeds the MetaMask donation flow immediately |
| **2. Payout wallet of seeded patients/campaigns** | `.env` → `MEDIFUND_RECEIVING_WALLET=0xYourAddress` **before** running `migrate --seed` | Seeder writes this into the patient user + all seeded campaigns |
| **3. A patient's personal payout wallet** | Log in as that patient → dashboard → **Wallet card → Connect MetaMask / Change Wallet** | Per-user; admin must verify it before payouts are approved |
| **4. Which account gets the 50 test ETH from funding** | `set TARGET=0xYourAddress` before running the funding step (or edit default in `blockchain/scripts/fund-wallet.js`) | Default: `0x80354450F4c300F178de2Ab718AbA6D2818CE102` |

After changing a seeded wallet, rerun `php artisan migrate --seed --force` (or update
the user/campaign wallet in the admin panel), then reload the wallet extension and
make sure the active account is the one you configured.

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
