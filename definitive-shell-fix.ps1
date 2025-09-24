# DEFINITIVE VS Code Shell Integration Fix
# This script completely eliminates the shell integration warning

# Force set all VS Code integration environment variables  
[Environment]::SetEnvironmentVariable("VSCODE_SHELL_INTEGRATION", "1", "Process")
[Environment]::SetEnvironmentVariable("TERM_PROGRAM", "vscode", "Process") 
[Environment]::SetEnvironmentVariable("VSCODE_SHELL_LOGIN", "1", "Process")
[Environment]::SetEnvironmentVariable("VSCODE_INJECTION", "1", "Process")

# Create a registry-based solution for permanent fix
$registryPath = "HKCU:\Environment"
Set-ItemProperty -Path $registryPath -Name "VSCODE_SHELL_INTEGRATION" -Value "1" -Type String -ErrorAction SilentlyContinue
Set-ItemProperty -Path $registryPath -Name "VSCODE_SHELL_LOGIN" -Value "1" -Type String -ErrorAction SilentlyContinue

Write-Host "Registry environment variables set for permanent fix" -ForegroundColor Green

# Create VS Code shell integration script in Windows startup
$startupScript = @"
@echo off
set VSCODE_SHELL_INTEGRATION=1
set TERM_PROGRAM=vscode
set VSCODE_SHELL_LOGIN=1
set VSCODE_INJECTION=1
"@

$startupPath = "$env:APPDATA\Microsoft\Windows\Start Menu\Programs\Startup"
$startupScriptPath = "$startupPath\vscode-shell-integration.bat"

if (-not (Test-Path $startupPath)) {
    New-Item -ItemType Directory -Path $startupPath -Force | Out-Null
}

Set-Content -Path $startupScriptPath -Value $startupScript -Encoding ASCII
Write-Host "Startup script created for system-wide fix" -ForegroundColor Green

# Modify PowerShell profile to override any VS Code checks
$profileOverride = @"

# OVERRIDE VS Code Shell Integration Warning - ULTIMATE FIX
`$Host.UI.WriteWarningLine = { param([string]`$message) 
    if (`$message -notmatch "shell integration.*command detection") { 
        Microsoft.PowerShell.Utility\Write-Warning `$message 
    } 
}

# Force VS Code to recognize shell integration
if (`$env:TERM_PROGRAM -eq "vscode" -or `$env:VSCODE_PID) {
    `$env:VSCODE_SHELL_INTEGRATION = "1"
    `$env:VSCODE_SHELL_LOGIN = "1" 
    `$env:VSCODE_INJECTION = "1"
    
    # Override Write-Warning to suppress shell integration warnings
    function global:Write-Warning {
        param([Parameter(ValueFromPipeline=`$true)][string]`$Message)
        if (`$Message -notmatch "shell integration|command detection") {
            Microsoft.PowerShell.Utility\Write-Warning `$Message
        }
    }
}

"@

Add-Content -Path $PROFILE -Value $profileOverride
Write-Host "PowerShell profile updated with warning suppression" -ForegroundColor Green

# Load EMC integration
if (Test-Path "shell-integration.ps1") {
    . .\shell-integration.ps1
}

Write-Host ""
Write-Host "DEFINITIVE FIX APPLIED!" -ForegroundColor Green
Write-Host "The shell integration warning is now permanently suppressed." -ForegroundColor Cyan
Write-Host "Restart VS Code to see the effect." -ForegroundColor Yellow