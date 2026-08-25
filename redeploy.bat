@echo off
title MediFund - Redeploy Contracts (portable)
setlocal
cd /d "%~dp0blockchain"

echo ============================================
echo   MediFund - contract redeploy
echo   Repo: %~dp0
echo ============================================

rem -- 1) dependencies (first run on a new device) --
if not exist node_modules (
    echo Installing blockchain packages ^(first run^)...
    call npm install
)

rem -- 2) start the local chain in its own window --
start "MediFund Hardhat Node (keep open)" cmd /k "npx hardhat node"

echo Waiting for chain to boot...
timeout /t 8 /nobreak >nul

rem -- 3) deploy + wire everything up --
echo.
echo [1/4] Deploying contracts...
call npx hardhat run scripts/deploy.js --network localhost || goto :fail
echo [2/4] Registering campaigns on-chain...
call npx hardhat run scripts/sync-campaigns.js --network localhost
echo [3/4] Funding MetaMask wallet with 50 test ETH...
call npx hardhat run scripts/fund-wallet.js --network localhost
set TARGET=
echo [4/4] Syncing contract addresses into the app DB + frontend bundle...
call node scripts/post-deploy-sync.js

echo.
echo ============================================
echo   REDEPLOY DONE.
echo   Chain RPC:  http://127.0.0.1:8545  (chain id 31337)
echo   Addresses:  blockchain\deployments.json
echo   In MetaMask: reload the extension, then make sure the
echo   network is "MediFund Local Network" before donating.
echo ============================================
pause
exit /b 0

:fail
echo.
echo DEPLOY FAILED - is another Hardhat node already running?
echo Close old "Hardhat Node" windows and run this again.
pause
exit /b 1
