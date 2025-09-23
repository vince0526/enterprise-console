<#
Sail Quickstart Script
Initializes Laravel Sail services and runs migrations & dev environment.
Usage:
  Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
  ./scripts/sail-quickstart.ps1 -Build
#>
[CmdletBinding()]
param(
  [switch]$Build,
  [switch]$Fresh
)
function Write-Step($m){ Write-Host "[SAIL] $m" -ForegroundColor Cyan }

if(-not (Test-Path .\vendor\bin\sail)){
  Write-Host 'Sail not installed. Run: php artisan sail:install' -ForegroundColor Yellow
  exit 1
}

$prefix = if($IsWindows){ 'bash' } else { '' }
$cmd = "$prefix ./vendor/bin/sail"

if($Build){ Write-Step 'Building containers'; iex "$cmd build --no-cache" }

Write-Step 'Starting containers'
iex "$cmd up -d"

if($Fresh){
  Write-Step 'Running fresh migration'
  iex "$cmd artisan migrate:fresh --seed"
} else {
  Write-Step 'Running pending migrations'
  iex "$cmd artisan migrate"
}

Write-Step 'Installing Node dependencies'
iex "$cmd npm install"

Write-Step 'Starting Vite dev server (detached)'
Start-Process bash -ArgumentList "./vendor/bin/sail npm run dev" -WindowStyle Minimized

Write-Step 'Environment ready'
Write-Host 'Open application: http://localhost' -ForegroundColor Green
