#!/usr/bin/env pwsh

# Fix VS Code Shell Integration Warning Script
# This script programmatically resolves "Enable shell integration to improve command detection" warnings

Write-Host "🔧 Fixing VS Code Shell Integration Issues..." -ForegroundColor Cyan
Write-Host ""

# Check if running in VS Code
if ($env:TERM_PROGRAM -eq "vscode" -or $env:VSCODE_PID) {
    Write-Host "✅ VS Code environment detected" -ForegroundColor Green
    
    # Set required environment variables
    $env:VSCODE_SHELL_INTEGRATION = "1"
    $env:TERM_PROGRAM = "vscode"
    
    Write-Host "✅ Shell integration environment variables set" -ForegroundColor Green
    
    # Check if PSReadLine is available and configure it
    if (Get-Module -ListAvailable -Name PSReadLine) {
        Import-Module PSReadLine -Force -ErrorAction SilentlyContinue
        
        # Configure PSReadLine for optimal VS Code integration
        Set-PSReadLineOption -EditMode Windows -ErrorAction SilentlyContinue
        Set-PSReadLineOption -PredictionSource History -ErrorAction SilentlyContinue
        Set-PSReadLineOption -PredictionViewStyle ListView -ErrorAction SilentlyContinue
        Set-PSReadLineOption -BellStyle None -ErrorAction SilentlyContinue
        Set-PSReadLineOption -HistorySearchCursorMovesToEnd -ErrorAction SilentlyContinue
        
        Write-Host "✅ PSReadLine configured for VS Code" -ForegroundColor Green
    }
    
    # Check PowerShell profile
    $profilePath = $PROFILE
    Write-Host "📋 PowerShell Profile: $profilePath" -ForegroundColor Cyan
    
    if (Test-Path $profilePath) {
        $profileContent = Get-Content $profilePath -Raw
        
        # Check if EMC shell integration is already in profile
        if ($profileContent -match "shell-integration\.ps1") {
            Write-Host "✅ EMC Shell Integration found in profile" -ForegroundColor Green
        } else {
            Write-Host "⚠️ EMC Shell Integration not found in profile" -ForegroundColor Yellow
            
            # Add EMC shell integration to profile
            $emcIntegrationPath = Join-Path (Get-Location) "shell-integration.ps1"
            if (Test-Path $emcIntegrationPath) {
                Add-Content $profilePath "`n# EMC Shell Integration`n. `"$emcIntegrationPath`""
                Write-Host "✅ Added EMC Shell Integration to PowerShell profile" -ForegroundColor Green
            }
        }
        
        # Check for VS Code specific configuration
        if ($profileContent -notmatch "VSCODE_SHELL_INTEGRATION") {
            Write-Host "⚠️ Adding VS Code shell integration configuration..." -ForegroundColor Yellow
            
            $vsCodeConfig = @"

# VS Code Shell Integration Configuration
if (`$env:TERM_PROGRAM -eq "vscode") {
    `$env:VSCODE_SHELL_INTEGRATION = "1"
    
    # Import and configure PSReadLine for VS Code
    if (Get-Module -ListAvailable -Name PSReadLine) {
        Import-Module PSReadLine -Force -ErrorAction SilentlyContinue
        Set-PSReadLineOption -PredictionSource History -ErrorAction SilentlyContinue
        Set-PSReadLineOption -PredictionViewStyle ListView -ErrorAction SilentlyContinue
    }
}
"@
            
            Add-Content $profilePath $vsCodeConfig
            Write-Host "✅ Added VS Code configuration to PowerShell profile" -ForegroundColor Green
        } else {
            Write-Host "✅ VS Code configuration already present in profile" -ForegroundColor Green
        }
    } else {
        Write-Host "⚠️ PowerShell profile doesn't exist, creating..." -ForegroundColor Yellow
        
        $profileDir = Split-Path $profilePath
        if (-not (Test-Path $profileDir)) {
            New-Item -ItemType Directory -Path $profileDir -Force | Out-Null
        }
        
        # Create new profile with VS Code and EMC integration
        $newProfileContent = @"
# VS Code Shell Integration
if (`$env:TERM_PROGRAM -eq "vscode") {
    `$env:VSCODE_SHELL_INTEGRATION = "1"
    
    if (Get-Module -ListAvailable -Name PSReadLine) {
        Import-Module PSReadLine -Force -ErrorAction SilentlyContinue
        Set-PSReadLineOption -PredictionSource History -ErrorAction SilentlyContinue
        Set-PSReadLineOption -PredictionViewStyle ListView -ErrorAction SilentlyContinue
    }
}

# EMC Shell Integration
if (Test-Path "$(Get-Location)\shell-integration.ps1") {
    . "$(Get-Location)\shell-integration.ps1"
}
"@
        
        Set-Content $profilePath $newProfileContent
        Write-Host "✅ Created new PowerShell profile with integrations" -ForegroundColor Green
    }
    
    # Reload the profile
    . $PROFILE
    Write-Host "✅ PowerShell profile reloaded" -ForegroundColor Green
    
} else {
    Write-Host "⚠️ Not running in VS Code environment" -ForegroundColor Yellow
    Write-Host "This script is designed to fix VS Code shell integration issues" -ForegroundColor Gray
}

# Check EMC project and load shell integration
if (Test-Path "composer.json" -and Test-Path "artisan") {
    Write-Host "✅ EMC project detected" -ForegroundColor Green
    
    if (Test-Path "shell-integration.ps1") {
        . .\shell-integration.ps1
        Write-Host "✅ EMC Shell Integration loaded" -ForegroundColor Green
    }
} else {
    Write-Host "⚠️ Not in EMC project directory" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "🎉 Shell Integration Fix Complete!" -ForegroundColor Green
Write-Host ""
Write-Host "If you're still seeing the warning:" -ForegroundColor Cyan
Write-Host "1. Restart VS Code completely" -ForegroundColor Gray
Write-Host "2. Open a new PowerShell terminal" -ForegroundColor Gray
Write-Host "3. The warning should no longer appear" -ForegroundColor Gray
Write-Host ""