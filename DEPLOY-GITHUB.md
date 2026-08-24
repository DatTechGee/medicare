# Deploy MediFund from GitHub to Hostinger (SSH)

The Laravel app lives in the `@core/` folder of this repo.

## 1. Open a terminal on the host
Hostinger panel (hPanel) -> Advanced -> **SSH Access** (or "Terminal").
Log in with your hosting account username/password.

## 2. Clone into a subfolder of your website
```bash
cd ~/public_html
git clone https://github.com/DatTechGee/medicare.git medifund
cd medifund/@core
```
Your site will then live at: `https://YOURDOMAIN.com/medifund`

## 3. Install PHP packages
```bash
composer install --no-dev --optimize-autoloader
```
(If `composer` is not found, run: `composer -V`; on Hostinger SSH it is usually available. PHP must be 8.x — set it in hPanel -> Advanced -> PHP Configuration.)

## 4. Create the environment file + app key
```bash
cp .env.example .env
php artisan key:generate
nano .env   # set APP_URL=https://YOURDOMAIN.com/medifund
```

## 5. Database

### Option A - SQLite (zero config, recommended for demo)
Leave `.env` as-is (`DB_CONNECTION=sqlite`, empty `DB_DATABASE`) and run:
```bash
touch database/database.sqlite
php artisan migrate --seed
```

### Option B - MySQL (hPanel -> Databases -> create DB first)
In `.env` comment the sqlite lines and uncomment:
```
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456789_medifund
DB_USERNAME=u123456789_admin
DB_PASSWORD=your_db_password
```
then:
```bash
php artisan migrate --seed
```

### Seeded money
`--seed` pre-funds every demo account, so donations and payouts work instantly:
- patient `rafiq_patient` -> 100 ETH (wallet verified)
- donor `amina_donor` -> 50 ETH
- every new donor/admin -> automatic 10 ETH faucet on first login
- all 6 campaigns come with donation history and progress bars

## 6. Permissions + caches
```bash
chmod -R 775 storage bootstrap/cache database
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

## 7. Visit
- Site:        https://YOURDOMAIN.com/medifund
- Admin login: https://YOURDOMAIN.com/medifund/login/admin  (`medifund_admin` / `password`)
- Demo users:  `rafiq_patient` (patient) / `amina_donor` (donor), both `password`

**Change all demo passwords before going live.**

## Notes
- Shared hosting has no local Hardhat node, so blockchain runs in demo mode
  (DB-backed). The real MetaMask <-> contract flow works only where the local
  node runs (your dev PC), or after migrating contracts to a public testnet
  such as Sepolia.
- To pull updates later:
```bash
cd ~/public_html/medifund && git pull && cd @core && composer install --no-dev && php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache
```
