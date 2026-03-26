@echo off
title Laravel Dev Launcher

echo Starting Development Servers...

REM Move to script directory (important!)
cd /d %~dp0

REM Start npm
start "NPM Dev Server" cmd /k "cd /d %~dp0 && npm run dev"

timeout /t 2 /nobreak >nul

REM Start Laravel
start "Laravel Server" cmd /k "cd /d %~dp0 && php artisan serve"

echo.
echo Both servers started!
echo.
pause