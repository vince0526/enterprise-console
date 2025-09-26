# Enterprise Management Console - PowerShell Shell Integration (FAST VERSION)

# Set VS Code environment variables immediately
$env:VSCODE_SHELL_INTEGRATION = "1"
$env:TERM_PROGRAM = "vscode"

# Simple EMC project check
function Test-EmcProject { return (Test-Path "composer.json") }

# EMC Command Aliases (Minimal)
function emc-serve { php artisan serve }
function emc-test { php artisan test }
function emc-pint { vendor/bin/pint }
function emc-stan { vendor/bin/phpstan analyse }
function emc-migrate { php artisan migrate }
function emc-status { git status }

function emc-activity {
    param([string]$Activity, [string]$Description)
    if (-not $Activity) {
        Write-Host "Usage: emc-activity ACTIVITY_TYPE description" -ForegroundColor Cyan
        return
    }
    if (Test-Path "log-activity.ps1") {
        . .\log-activity.ps1 -Activity $Activity -Description $Description
    }
}

# Simple welcome message
if (Test-EmcProject) {
    Write-Host "EMC Shell Integration: READY" -ForegroundColor Green
}