@echo off
title MediFund Dev Server (watchdog)
echo ============================================
echo  MediFund dev watchdog - keeps MySQL + 
echo  php artisan serve running on :8000
echo  Keep this window OPEN while developing.
echo ============================================
powershell -NoProfile -ExecutionPolicy Bypass -File "C:\xampp\htdocs\fundorex\@core\dev-watchdog.ps1"
pause
