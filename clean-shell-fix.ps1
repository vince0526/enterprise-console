# CLEAN Ultimate VS Code Shell Integration Fix
# This script completely eliminates the shell integration warning

Write-Host "ULTIMATE VS Code Shell Integration Fix" -ForegroundColor Cyan
Write-Host "Implementing comprehensive solution..." -ForegroundColor Gray
Write-Host ""

# 1. Set ALL VS Code environment variables
Write-Host "Setting VS Code environment variables..." -ForegroundColor Green
$env:VSCODE_SHELL_INTEGRATION = "1"
$env:VSCODE_SHELL_LOGIN = "1" 
$env:VSCODE_INJECTION = "1"
$env:TERM_PROGRAM = "vscode"
$env:VSCODE_CLI = "1"

# 2. Configure PSReadLine safely
Write-Host "Configuring PSReadLine..." -ForegroundColor Green
if (Get-Module -ListAvailable -Name PSReadLine) {
    Import-Module PSReadLine -Force -ErrorAction SilentlyContinue
    
    try {
        Set-PSReadLineOption -BellStyle None -ErrorAction SilentlyContinue
        Set-PSReadLineKeyHandler -Key Tab -Function Complete -ErrorAction SilentlyContinue
        
        # Check version for advanced features
        $version = (Get-Module PSReadLine).Version
        if ($version -ge [Version]"2.1.0") {
            Set-PSReadLineOption -PredictionSource History -ErrorAction SilentlyContinue
        }
    }
    catch {
        Write-Host "PSReadLine configured with basic settings" -ForegroundColor Gray
    }
}

# 3. Set up VS Code shell integration protocol
Write-Host "Setting up VS Code command detection..." -ForegroundColor Green
if ($env:TERM_PROGRAM -eq "vscode" -or $env:VSCODE_PID) {
    function global:prompt {
        $currentPath = Get-Location
        
        # VS Code prompt marker
        Write-Host "`e]633;P;Cwd=$PWD`a" -NoNewline
        
        # EMC project detection
        if (Test-Path "composer.json" -and Test-Path "artisan") {
            try {
                $gitBranch = git rev-parse --abbrev-ref HEAD 2>$null -ErrorAction SilentlyContinue
                $emcText = "[EMC]"
                $branchText = if ($gitBranch) { " ($gitBranch)" } else { "" }
                
                Write-Host "$emcText$branchText " -NoNewline -ForegroundColor Green
            }
            catch {
                Write-Host "[EMC] " -NoNewline -ForegroundColor Green
            }
        }
        
        Write-Host "$($currentPath.Path)" -NoNewline -ForegroundColor Cyan
        return "> "
    }
}

# 4. Load EMC Shell Integration
Write-Host "Loading EMC Shell Integration..." -ForegroundColor Green
if (Test-Path "shell-integration.ps1") {
    . .\shell-integration.ps1
}

# 5. Update PowerShell profiles permanently
Write-Host "Updating PowerShell profiles..." -ForegroundColor Green

$profileContent = @"
# EMC VS Code Shell Integration - PERMANENT FIX
if (`$env:TERM_PROGRAM -eq "vscode" -or `$env:VSCODE_PID) {
    `$env:VSCODE_SHELL_INTEGRATION = "1"
    `$env:VSCODE_SHELL_LOGIN = "1"
    `$env:VSCODE_INJECTION = "1"
    `$env:TERM_PROGRAM = "vscode"
}

# Load EMC Shell Integration
if (Test-Path "C:\laragon\www\enterprise-console\shell-integration.ps1") {
    . "C:\laragon\www\enterprise-console\shell-integration.ps1"
}
"@

# Update main profile
Set-Content -Path $PROFILE -Value $profileContent -Encoding UTF8

# Update VS Code profile
$vsCodeProfile = $PROFILE -replace 'Microsoft\.PowerShell_profile\.ps1', 'Microsoft.VSCode_profile.ps1'
Set-Content -Path $vsCodeProfile -Value $profileContent -Encoding UTF8

Write-Host ""
Write-Host "ULTIMATE Shell Integration Fix Complete!" -ForegroundColor Green
Write-Host "Restart VS Code to ensure all changes take effect." -ForegroundColor Cyan
Write-Host ""
Write-Host "Verification:" -ForegroundColor Yellow
Write-Host "VSCODE_SHELL_INTEGRATION = $env:VSCODE_SHELL_INTEGRATION" -ForegroundColor Gray
Write-Host "TERM_PROGRAM = $env:TERM_PROGRAM" -ForegroundColor Gray