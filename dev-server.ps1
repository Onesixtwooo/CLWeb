# Development Server Launcher
# This script runs both npm dev server and Laravel artisan serve concurrently

Write-Host "Starting Development Servers..." -ForegroundColor Green

# Start npm run dev in a new PowerShell window
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PSScriptRoot'; npm run dev"

# Wait a moment to stagger the starts
Start-Sleep -Seconds 2

# Start php artisan serve in a new PowerShell window
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PSScriptRoot'; php artisan serve"

Write-Host "Both servers started in separate windows!" -ForegroundColor Green
Write-Host "- Vite dev server (npm run dev)" -ForegroundColor Cyan
Write-Host "- Laravel server (php artisan serve)" -ForegroundColor Cyan
Write-Host "`nClose each window to stop the respective server." -ForegroundColor Yellow
