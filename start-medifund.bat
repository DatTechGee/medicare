@echo off
title MediFund Stack Launcher
echo ============================================
echo   MediFund - starting local blockchain + app
echo ============================================

cd /d C:\xampp\htdocs\fundorex\blockchain
start "MediFund Hardhat Node (keep open)" /min cmd /k "npx hardhat node"

timeout /t 8 /nobreak >nul

cd /d C:\xampp\htdocs\fundorex\@core
start "MediFund Laravel App (keep open)" /min cmd /k "C:\xampp\php\php.exe artisan serve --host=127.0.0.1 --port=8000"

echo.
echo Waiting for chain...
timeout /t 6 /nobreak >nul

cd /d C:\xampp\htdocs\fundorex\blockchain
echo Deploying contracts...
call npx hardhat run scripts/deploy.js --network localhost
echo Registering campaigns on-chain...
call npx hardhat run scripts/sync-campaigns.js --network localhost
echo Funding your MetaMask wallet with 50 test ETH...
call npx hardhat run scripts/fund-wallet.js --network localhost
echo Syncing frontend contract bundle + DB options...
call node scripts/post-deploy-sync.js

echo.
echo ============================================
echo   DONE. App:  http://127.0.0.1:8000
echo   Admin login: http://127.0.0.1:8000/login/admin
echo   IMPORTANT: In MetaMask, reload the extension
echo   before donating so its throttle counter clears.
echo ============================================
pause
