# Enterprise Management Console - PowerShell Shell Integration
# Enhanced command detection, aliases, and development workflow integration

# =============================================================================
# CORE FUNCTIONS
# =============================================================================

function Test-EmcProject {
    return (Test-Path "composer.json") -and (Test-Path "artisan")
}

function Invoke-EmcCommand {
    param(
        [string]$Command,
        [string]$Description,
        [switch]$LogActivity
    )
    
    Write-Host "🔧 $Description" -ForegroundColor Cyan
    
    if ($LogActivity -and (Test-Path "dev-log-tracker.ps1")) {
        try {
            Invoke-Expression $Command
            $exitCode = $LASTEXITCODE
            
            # Log the activity
            $activity = if ($Description -match "test") { "TESTING" } 
                       elseif ($Description -match "migrate") { "DEPLOYMENT" }
                       elseif ($Description -match "serve|server") { "SETUP" }
                       else { "FEATURE" }
            
            . .\dev-log-tracker.ps1 -Activity $activity -Description $Description
            
            return $exitCode
        } catch {
            Write-Host "❌ Command failed: $_" -ForegroundColor Red
            return 1
        }
    } else {
        Invoke-Expression $Command
    }
}

# =============================================================================
# EMC-SPECIFIC COMMAND ALIASES
# =============================================================================

function emc-serve { Invoke-EmcCommand "php artisan serve" "Start EMC development server" -LogActivity }
function emc-migrate { Invoke-EmcCommand "php artisan migrate" "Run database migrations" -LogActivity }
function emc-fresh { Invoke-EmcCommand "php artisan migrate:fresh --seed" "Fresh migration with seeders" -LogActivity }
function emc-test { Invoke-EmcCommand "php artisan test" "Run EMC tests" -LogActivity }
function emc-pint { Invoke-EmcCommand "vendor/bin/pint" "Run Laravel Pint formatter" -LogActivity }
function emc-stan { Invoke-EmcCommand "vendor/bin/phpstan analyse" "Run PHPStan analysis" -LogActivity }
function emc-queue { Invoke-EmcCommand "php artisan queue:work" "Start queue worker" -LogActivity }

# Development workflow functions
function emc-setup {
    Write-Host "🚀 Setting up EMC development environment..." -ForegroundColor Green
    Invoke-EmcCommand "composer install" "Install PHP dependencies" -LogActivity
    Invoke-EmcCommand "npm install" "Install Node.js dependencies" -LogActivity
    Invoke-EmcCommand "php artisan migrate" "Run database migrations" -LogActivity
    Write-Host "✅ EMC setup complete!" -ForegroundColor Green
}

function emc-quality {
    Write-Host "🔍 Running EMC code quality checks..." -ForegroundColor Green
    Invoke-EmcCommand "vendor/bin/pint --test" "Check code formatting" -LogActivity
    Invoke-EmcCommand "vendor/bin/phpstan analyse" "Run static analysis" -LogActivity
    Invoke-EmcCommand "php artisan test" "Run tests" -LogActivity
    Write-Host "✅ Quality checks complete!" -ForegroundColor Green
}

function emc-deploy {
    Write-Host "🚀 Deploying EMC changes..." -ForegroundColor Green
    Invoke-EmcCommand "git add -A" "Stage all changes"
    $commitMsg = Read-Host "Enter commit message"
    Invoke-EmcCommand "git commit -m `"$commitMsg`"" "Commit changes" -LogActivity
    Invoke-EmcCommand "git push origin main" "Push to GitHub" -LogActivity
    Write-Host "✅ Deploy complete!" -ForegroundColor Green
}

# Git shortcuts
function emc-status { Invoke-EmcCommand "git status" "Check git status" }
function emc-log { Invoke-EmcCommand "git log --oneline -10" "View recent commits" }

# Activity logging function
function emc-activity {
    param(
        [Parameter(Position=0)]
        [string]$Activity,
        [Parameter(Position=1)]
        [string]$Description
    )
    
    if (-not $Activity) {
        Write-Host "📝 EMC Activity Logger" -ForegroundColor Cyan
        Write-Host "Usage: emc-activity <ACTIVITY_TYPE> <description>" -ForegroundColor Gray
        Write-Host "Activities: FEATURE, BUGFIX, REFACTOR, TESTING, DEPLOYMENT, SETUP, DOCUMENTATION" -ForegroundColor Gray
        return
    }
    
    if (Test-Path "dev-log-tracker.ps1") {
        . .\dev-log-tracker.ps1 -Activity $Activity -Description $Description
    } else {
        Write-Host "❌ Activity logger not found" -ForegroundColor Red
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

# =============================================================================
# COMMAND COMPLETION ENHANCEMENTS
# =============================================================================

# Register argument completers for better command detection
Register-ArgumentCompleter -CommandName emc-activity -ParameterName Activity -ScriptBlock {
    param($commandName, $parameterName, $wordToComplete, $commandAst, $fakeBoundParameters)
    $activities = @('FEATURE', 'BUGFIX', 'REFACTOR', 'TESTING', 'DEPLOYMENT', 'SETUP', 'DOCUMENTATION', 'OTHER')
    $activities | Where-Object { $_ -like "$wordToComplete*" }
}

# =============================================================================
# ENHANCED PROMPT FOR EMC PROJECTS
# =============================================================================

function prompt {
    $currentPath = Get-Location
    $pathString = $currentPath.Path
    
    # Check if in EMC project
    if (Test-EmcProject) {
        $gitBranch = ""
        try {
            $gitBranch = & git rev-parse --abbrev-ref HEAD 2>$null
        } catch {
            # Ignore git errors
        }
        
        $emcIndicator = "[EMC]"
        $branchInfo = if ($gitBranch) { " ($gitBranch)" } else { "" }
        
        Write-Host "$emcIndicator" -NoNewline -ForegroundColor Green
        Write-Host "$branchInfo" -NoNewline -ForegroundColor Yellow
        Write-Host " $pathString" -NoNewline -ForegroundColor Cyan
        
        # Show recent activity
        $logFile = "logs\computer-profiles\$env:COMPUTERNAME-activity.log"
        if (Test-Path $logFile) {
            $lastActivity = Get-Content $logFile | Select-Object -Last 1
            if ($lastActivity) {
                $activityPart = $lastActivity.Substring(22)
                Write-Host " 📋 $activityPart" -NoNewline -ForegroundColor Gray
            }
        }
        
        return "> "
    } else {
        Write-Host "$pathString" -NoNewline -ForegroundColor Cyan
        return "> "
    }
}

# =============================================================================
# ENHANCED COMMAND DETECTION AND HISTORY
# =============================================================================

# Enable advanced command history if PSReadLine is available
if (Get-Module -ListAvailable -Name PSReadLine) {
    try {
        Set-PSReadLineOption -PredictionSource History
        Set-PSReadLineOption -PredictionViewStyle ListView
        Set-PSReadLineOption -EditMode Emacs

        # Enhanced key bindings for better command detection
        Set-PSReadLineKeyHandler -Key Ctrl+d -Function DeleteChar
        Set-PSReadLineKeyHandler -Key Ctrl+r -Function ReverseSearchHistory
        Set-PSReadLineKeyHandler -Key UpArrow -Function HistorySearchBackward
        Set-PSReadLineKeyHandler -Key DownArrow -Function HistorySearchForward
    } catch {
        # Ignore PSReadLine configuration errors
        Write-Host "Note: PSReadLine enhancements not available" -ForegroundColor Yellow
    }
}

# Welcome message if in EMC project
if (Test-EmcProject) {
    Write-Host ""
    Write-Host "🎉 EMC Shell Integration Loaded!" -ForegroundColor Green
    Write-Host "Enhanced command detection and aliases are now available." -ForegroundColor Gray
    Write-Host ""
    Write-Host "Available commands:" -ForegroundColor Cyan
    Write-Host "  emc-serve, emc-test, emc-quality, emc-deploy, emc-activity" -ForegroundColor Gray
    Write-Host "  emc-setup, emc-pint, emc-stan, emc-migrate, emc-fresh" -ForegroundColor Gray
    Write-Host ""
}