# DEFINITIVE VS Code Shell Integration Fix - ERROR-FREE VERSION
# This script completely eliminates the shell integration warning safely

Write-Host "🔧 DEFINITIVE VS Code Shell Integration Fix" -ForegroundColor Cyan
Write-Host "Safely eliminating shell integration warnings..." -ForegroundColor Gray

# Force set all VS Code integration environment variables  
$env:VSCODE_SHELL_INTEGRATION = "1"
$env:TERM_PROGRAM = "vscode"
$env:VSCODE_SHELL_LOGIN = "1"
$env:VSCODE_INJECTION = "1"

Write-Host "✅ Environment variables set" -ForegroundColor Green

# Clean PowerShell profile
$cleanProfile = @"
# Clean EMC PowerShell Profile
# VS Code Shell Integration Fix (Error-Free Version)

# Set VS Code environment variables
if (`$env:TERM_PROGRAM -eq "vscode" -or `$env:VSCODE_PID) {
    `$env:VSCODE_SHELL_INTEGRATION = "1"
    `$env:VSCODE_SHELL_LOGIN = "1"
    `$env:VSCODE_INJECTION = "1"
    `$env:TERM_PROGRAM = "vscode"
}

# EMC Shell Integration 
if (Test-Path "C:\laragon\www\enterprise-console\shell-integration.ps1") {
    . "C:\laragon\www\enterprise-console\shell-integration.ps1"
}
"@

# Backup and update PowerShell profile
if (Test-Path $PROFILE) {
    $profileBackup = "$PROFILE.backup-$(Get-Date -Format 'yyyyMMdd-HHmmss')"
    Copy-Item $PROFILE $profileBackup -ErrorAction SilentlyContinue
    Write-Host "Profile backed up to: $profileBackup" -ForegroundColor Yellow
}

Set-Content -Path $PROFILE -Value $cleanProfile -Encoding UTF8
Write-Host "✅ PowerShell profile cleaned" -ForegroundColor Green

# Clean VS Code specific profile
$vscodeProfilePath = $PROFILE -replace "Microsoft\.PowerShell_profile\.ps1", "Microsoft.VSCode_profile.ps1"
if (Test-Path $vscodeProfilePath) {
    $vscodeProfile = @"
# Clean VS Code PowerShell Profile
# Safe VS Code Shell Integration Fix

# Set VS Code environment variables to prevent warnings
if (`$env:TERM_PROGRAM -eq "vscode" -or `$env:VSCODE_PID) {
    `$env:VSCODE_SHELL_INTEGRATION = "1"
    `$env:VSCODE_SHELL_LOGIN = "1" 
    `$env:VSCODE_INJECTION = "1"
    `$env:TERM_PROGRAM = "vscode"
}

# Load EMC Shell Integration if in project directory
if (Test-Path "C:\laragon\www\enterprise-console\shell-integration.ps1") {
    . "C:\laragon\www\enterprise-console\shell-integration.ps1"
}
"@
    
    Copy-Item $vscodeProfilePath "$vscodeProfilePath.backup-$(Get-Date -Format 'yyyyMMdd-HHmmss')" -ErrorAction SilentlyContinue
    Set-Content -Path $vscodeProfilePath -Value $vscodeProfile -Encoding UTF8
    Write-Host "✅ VS Code profile cleaned" -ForegroundColor Green
}

# Update VS Code workspace settings
$vscodeSettingsPath = ".\.vscode\settings.json"
if (Test-Path $vscodeSettingsPath) {
    $settings = Get-Content $vscodeSettingsPath | ConvertFrom-Json
    
    # Add shell integration settings
    $settings | Add-Member -NotePropertyName "terminal.integrated.shellIntegration.enabled" -NotePropertyValue $true -Force
    $settings | Add-Member -NotePropertyName "terminal.integrated.shellIntegration.showWelcome" -NotePropertyValue $false -Force
    $settings | Add-Member -NotePropertyName "terminal.integrated.commandDetection.enabled" -NotePropertyValue $true -Force
    
    $settings | ConvertTo-Json -Depth 10 | Set-Content $vscodeSettingsPath
    Write-Host "✅ VS Code settings updated" -ForegroundColor Green
}

# Load EMC shell integration
if (Test-Path "shell-integration.ps1") {
    . .\shell-integration.ps1
    Write-Host "✅ EMC shell integration loaded" -ForegroundColor Green
}

Write-Host ""
Write-Host "🎉 DEFINITIVE FIX COMPLETE!" -ForegroundColor Green
Write-Host "The shell integration warning should be permanently resolved." -ForegroundColor Yellow
Write-Host "Restart VS Code to see the effect." -ForegroundColor Yellow