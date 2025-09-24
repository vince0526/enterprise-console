# Enterprise Management Console - PowerShell Shell Integration
# Enhanced command detection, aliases, and development workflow integration

Write-Host "Loading EMC Shell Integration..." -ForegroundColor Cyan

# Check if we're in an EMC project
function Test-EmcProject {
    return (Test-Path "composer.json") -and (Test-Path "artisan")
}

# Core command execution function
function Invoke-EmcCommand {
    param(
        [string]$Command,
        [string]$Description,
        [switch]$LogActivity
    )
    
    Write-Host "🔧 $Description" -ForegroundColor Cyan
    Invoke-Expression $Command
}

# EMC Command Aliases
function emc-serve { 
    Write-Host "🚀 Starting EMC development server..." -ForegroundColor Green
    php artisan serve
}

function emc-test { 
    Write-Host "🧪 Running EMC tests..." -ForegroundColor Green
    php artisan test
}

function emc-pint { 
    Write-Host "🎨 Running Laravel Pint formatter..." -ForegroundColor Green
    vendor/bin/pint
}

function emc-stan { 
    Write-Host "🔍 Running PHPStan analysis..." -ForegroundColor Green
    vendor/bin/phpstan analyse
}

function emc-migrate { 
    Write-Host "📊 Running database migrations..." -ForegroundColor Green
    php artisan migrate
}

function emc-fresh { 
    Write-Host "🆕 Fresh migration with seeders..." -ForegroundColor Green
    php artisan migrate:fresh --seed
}

function emc-status {
    Write-Host "📋 Git status..." -ForegroundColor Green
    git status
}

function emc-quality {
    Write-Host "🔍 Running EMC code quality checks..." -ForegroundColor Green
    Write-Host "  -> Running Pint..." -ForegroundColor Gray
    vendor/bin/pint --test
    Write-Host "  -> Running PHPStan..." -ForegroundColor Gray
    vendor/bin/phpstan analyse
    Write-Host "  -> Running Tests..." -ForegroundColor Gray
    php artisan test
    Write-Host "✅ Quality checks complete!" -ForegroundColor Green
}

function emc-deploy {
    Write-Host "🚀 Deploying EMC changes..." -ForegroundColor Green
    git add -A
    $commitMsg = Read-Host "Enter commit message"
    git commit -m "$commitMsg"
    git push origin main
    Write-Host "✅ Deploy complete!" -ForegroundColor Green
}

function emc-activity {
    param(
        [string]$Activity,
        [string]$Description
    )
    
    if (-not $Activity) {
        Write-Host "📝 EMC Activity Logger" -ForegroundColor Cyan
        Write-Host "Usage: emc-activity ACTIVITY_TYPE description" -ForegroundColor Gray
        Write-Host "Activities: FEATURE, BUGFIX, REFACTOR, TESTING, DEPLOYMENT, SETUP, DOCUMENTATION" -ForegroundColor Gray
        return
    }
    
    if (Test-Path "dev-log-tracker.ps1") {
        . .\dev-log-tracker.ps1 -Activity $Activity -Description $Description
    } else {
        Write-Host "Activity logger not found" -ForegroundColor Yellow
    }
}

function emc-log-view { 
    $logFile = "logs\computer-profiles\$env:COMPUTERNAME-activity.log"
    if (Test-Path $logFile) {
        Write-Host "📋 Recent EMC Activity on $env:COMPUTERNAME" -ForegroundColor Cyan
        Get-Content $logFile | Select-Object -Last 10
    } else {
        Write-Host "No activity log found" -ForegroundColor Yellow
    }
}

# Enhanced prompt
function prompt {
    $currentPath = Get-Location
    
    if (Test-EmcProject) {
        $gitBranch = ""
        try {
            $gitBranch = git rev-parse --abbrev-ref HEAD 2>$null
        } catch {}
        
        $emcIndicator = "[EMC]"
        $branchInfo = if ($gitBranch) { " ($gitBranch)" } else { "" }
        
        Write-Host "$emcIndicator$branchInfo " -NoNewline -ForegroundColor Green
        Write-Host "$($currentPath.Path)" -NoNewline -ForegroundColor Cyan
        return "> "
    } else {
        Write-Host "$($currentPath.Path)" -NoNewline -ForegroundColor Cyan
        return "> "
    }
}

# Welcome message
if (Test-EmcProject) {
    Write-Host ""
    Write-Host "🎉 EMC Shell Integration Loaded!" -ForegroundColor Green
    Write-Host "Available commands: emc-serve, emc-test, emc-quality, emc-deploy, emc-activity" -ForegroundColor Gray
    Write-Host ""
}