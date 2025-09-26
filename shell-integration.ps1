# Enterprise Management Console - PowerShell Shell Integration

# Suppress VS Code shell integration warnings
$env:VSCODE_SHELL_INTEGRATION = "1"
$env:TERM_PROGRAM = "vscode"

Write-Host "Loading EMC Shell Integration..." -ForegroundColor Cyan

# Check if we're in an EMC project
function Test-EmcProject {
    return (Test-Path "composer.json") -and (Test-Path "artisan")
}

# EMC Command Aliases
function emc-serve { 
    Write-Host "Starting EMC development server..." -ForegroundColor Green
    php artisan serve
}

function emc-test { 
    Write-Host "Running EMC tests..." -ForegroundColor Green
    php artisan test
}

function emc-pint { 
    Write-Host "Running Laravel Pint formatter..." -ForegroundColor Green
    vendor/bin/pint
}

function emc-stan { 
    Write-Host "Running PHPStan analysis..." -ForegroundColor Green
    vendor/bin/phpstan analyse
}

function emc-migrate { 
    Write-Host "Running database migrations..." -ForegroundColor Green
    php artisan migrate
}

function emc-fresh { 
    Write-Host "Fresh migration with seeders..." -ForegroundColor Green
    php artisan migrate:fresh --seed
}

function emc-status {
    Write-Host "Git status..." -ForegroundColor Green
    git status
}

function emc-quality {
    Write-Host "Running EMC code quality checks..." -ForegroundColor Green
    Write-Host "Running Pint..." -ForegroundColor Gray
    vendor/bin/pint --test
    Write-Host "Running PHPStan..." -ForegroundColor Gray  
    vendor/bin/phpstan analyse
    Write-Host "Running Tests..." -ForegroundColor Gray
    php artisan test
    Write-Host "Quality checks complete!" -ForegroundColor Green
}

function emc-deploy {
    Write-Host "Deploying EMC changes..." -ForegroundColor Green
    git add -A
    $commitMsg = Read-Host "Enter commit message"
    git commit -m "$commitMsg"
    git push origin main
    Write-Host "Deploy complete!" -ForegroundColor Green
}

function emc-activity {
    param([string]$Activity, [string]$Description)
    
    if (-not $Activity) {
        Write-Host "EMC Activity Logger" -ForegroundColor Cyan
        Write-Host "Usage: emc-activity ACTIVITY_TYPE description" -ForegroundColor Gray
        Write-Host "Activities: FEATURE, BUGFIX, REFACTOR, TESTING, DEPLOYMENT, SETUP, DOCUMENTATION" -ForegroundColor Gray
        return
    }
    
    if (Test-Path "dev-log-tracker.ps1") {
        . .\dev-log-tracker.ps1 -Activity $Activity -Description $Description
    }
    else {
        Write-Host "Activity logger not found" -ForegroundColor Yellow
    }
}

function emc-log-view { 
    $logFile = "logs\computer-profiles\$env:COMPUTERNAME-activity.log"
    if (Test-Path $logFile) {
        Write-Host "Recent EMC Activity on $env:COMPUTERNAME" -ForegroundColor Cyan
        Get-Content $logFile | Select-Object -Last 10
    }
    else {
        Write-Host "No activity log found" -ForegroundColor Yellow
    }
}

# Enhanced PSReadLine configuration for better command detection (Fast loading)
try {
    if (Get-Module -ListAvailable -Name PSReadLine -ErrorAction SilentlyContinue) {
        Import-Module PSReadLine -Force -ErrorAction SilentlyContinue -WarningAction SilentlyContinue
        
        # Quick configuration without version checks to prevent delays
        Set-PSReadLineOption -BellStyle None -ErrorAction SilentlyContinue
        Set-PSReadLineKeyHandler -Key UpArrow -Function HistorySearchBackward -ErrorAction SilentlyContinue
        Set-PSReadLineKeyHandler -Key DownArrow -Function HistorySearchForward -ErrorAction SilentlyContinue
        Set-PSReadLineKeyHandler -Key Tab -Function Complete -ErrorAction SilentlyContinue
    }
}
catch {
    # Skip PSReadLine configuration if it causes issues
}

# Configure VS Code shell integration
if ($env:TERM_PROGRAM -eq "vscode") {
    # Set up command detection for VS Code
    $global:__VSCode_Command_Start = "`e]633;A`a"
    $global:__VSCode_Command_End = "`e]633;B`a"
    $global:__VSCode_Prompt_Start = "`e]633;P;Cwd=$PWD`a"
    
    # Override prompt to work with VS Code shell integration
    function prompt {
        $currentPath = Get-Location
        
        # VS Code command start marker
        Write-Host $global:__VSCode_Prompt_Start -NoNewline
        
        if (Test-EmcProject) {
            $gitBranch = ""
            try {
                # Simple, fast git branch detection without hanging
                if (Test-Path ".git\HEAD") {
                    $headContent = Get-Content ".git\HEAD" -ErrorAction SilentlyContinue
                    if ($headContent -and $headContent -match "ref: refs/heads/(.+)") {
                        $gitBranch = $matches[1]
                    }
                    else {
                        $gitBranch = "main"
                    }
                }
            }
            catch {
                $gitBranch = ""
            }
            
            $emcIndicator = "[EMC]"
            $branchInfo = if ($gitBranch -and $gitBranch.Trim()) { " ($($gitBranch.Trim()))" } else { "" }
            
            Write-Host "$emcIndicator$branchInfo " -NoNewline -ForegroundColor Green
            Write-Host "$($currentPath.Path)" -NoNewline -ForegroundColor Cyan
            return "> "
        }
        else {
            Write-Host "$($currentPath.Path)" -NoNewline -ForegroundColor Cyan
            return "> "
        }
    }
}

# Welcome message
if (Test-EmcProject) {
    Write-Host ""
    Write-Host "EMC Shell Integration Loaded!" -ForegroundColor Green
    Write-Host "Available commands: emc-serve, emc-test, emc-quality, emc-deploy, emc-activity" -ForegroundColor Gray
    Write-Host "VS Code command detection: ENABLED" -ForegroundColor Green
    Write-Host ""
}