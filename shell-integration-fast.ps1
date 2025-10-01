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

# Determine repository root (directory of this script)
$script:RepoRoot = $PSScriptRoot

function Invoke-InRepo {
    param(
        [Parameter(Mandatory = $true)]
        [ScriptBlock]$Script
    )
    Push-Location $script:RepoRoot
    try { & $Script } finally { Pop-Location }
}

# Composite helpers
function emc-quality {
    Invoke-InRepo {
        if (Test-Path "vendor/bin/pint") { vendor/bin/pint -v } else { Write-Host "Pint not found" -ForegroundColor Yellow }
        if (Test-Path "vendor/bin/phpstan") { vendor/bin/phpstan analyse -c phpstan.neon.dist --no-progress --memory-limit=1G } else { Write-Host "PHPStan not found" -ForegroundColor Yellow }
        php artisan test --parallel --recreate-databases
    }
}

function emc-fresh {
    Invoke-InRepo { php artisan migrate:fresh --seed }
}

function emc-build {
    Invoke-InRepo {
        if (Test-Path "package.json") {
            if (Get-Command npm -ErrorAction SilentlyContinue) { npm run build } else { Write-Host "npm not available" -ForegroundColor Yellow }
        }
        else { Write-Host "No package.json found" -ForegroundColor Yellow }
    }
}

function emc-dev {
    Invoke-InRepo {
        if (Test-Path "package.json") {
            if (Get-Command npm -ErrorAction SilentlyContinue) { npm run dev } else { Write-Host "npm not available" -ForegroundColor Yellow }
        }
        else { Write-Host "No package.json found" -ForegroundColor Yellow }
    }
}

function emc-deploy {
    Write-Host "[emc-deploy] Running quality checks and build (placeholder)" -ForegroundColor Cyan
    emc-quality
    emc-build
    Write-Host "[emc-deploy] Done" -ForegroundColor Green
}