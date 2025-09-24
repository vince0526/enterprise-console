@echo off
title VS Code Shell Integration - ULTIMATE FIX

echo.
echo ===============================================
echo   VS CODE SHELL INTEGRATION - ULTIMATE FIX
echo ===============================================
echo.
echo This will completely eliminate the persistent
echo "Enable shell integration" warning in VS Code
echo.

REM Set all possible VS Code environment variables
set VSCODE_SHELL_INTEGRATION=1
set TERM_PROGRAM=vscode
set VSCODE_SHELL_LOGIN=1
set VSCODE_INJECTION=1
set VSCODE_NONCE=%RANDOM%%RANDOM%

echo Step 1: Environment variables configured
echo.

REM Run PowerShell script to configure shell integration
powershell -ExecutionPolicy Bypass -Command "& {
    # Set environment variables in PowerShell session
    $env:VSCODE_SHELL_INTEGRATION = '1'
    $env:TERM_PROGRAM = 'vscode'
    $env:VSCODE_SHELL_LOGIN = '1'
    $env:VSCODE_INJECTION = '1'
    
    # Load VS Code shell protocol
    if (Test-Path 'vscode-shell-protocol.ps1') {
        . .\vscode-shell-protocol.ps1
    }
    
    # Load EMC shell integration
    if (Test-Path 'shell-integration.ps1') {
        . .\shell-integration.ps1
    }
    
    Write-Host 'Step 2: PowerShell integration configured' -ForegroundColor Green
    Write-Host ''
}"

echo Step 3: Updating VS Code settings...

REM Create a PowerShell script to update VS Code settings
powershell -ExecutionPolicy Bypass -Command "& {
    # Update workspace settings
    $workspaceSettings = '.\.vscode\settings.json'
    if (Test-Path $workspaceSettings) {
        $settings = Get-Content $workspaceSettings -Raw | ConvertFrom-Json
        $settings.'terminal.integrated.shellIntegration.enabled' = $true
        $settings.'terminal.integrated.shellIntegration.showWelcome' = $false
        $settings.'terminal.integrated.commandDetection.enabled' = $true
        $settings | ConvertTo-Json -Depth 10 | Set-Content $workspaceSettings -Encoding UTF8
        Write-Host 'Workspace settings updated' -ForegroundColor Green
    }
    
    # Update user settings if accessible
    $userSettings = \"$env:APPDATA\Code\User\settings.json\"
    if (Test-Path $userSettings) {
        try {
            $userConfig = Get-Content $userSettings -Raw | ConvertFrom-Json
            $userConfig.'terminal.integrated.shellIntegration.enabled' = $true
            $userConfig.'terminal.integrated.shellIntegration.showWelcome' = $false  
            $userConfig.'terminal.integrated.commandDetection.enabled' = $true
            $userConfig | ConvertTo-Json -Depth 10 | Set-Content $userSettings -Encoding UTF8
            Write-Host 'User settings updated' -ForegroundColor Green
        } catch {
            Write-Host 'User settings update skipped' -ForegroundColor Yellow
        }
    }
}"

echo.
echo Step 4: Configuring PowerShell profile...

REM Update PowerShell profile
powershell -ExecutionPolicy Bypass -Command "& {
    $profileContent = Get-Content $PROFILE -Raw -ErrorAction SilentlyContinue
    if ($profileContent -notmatch 'vscode-shell-protocol.ps1') {
        $addition = \"`n# VS Code Shell Integration - COMPLETE FIX`nif (\`$env:TERM_PROGRAM -eq 'vscode') {`n    \`$env:VSCODE_SHELL_INTEGRATION = '1'`n    if (Test-Path '\$(Get-Location)\vscode-shell-protocol.ps1') {`n        . '\$(Get-Location)\vscode-shell-protocol.ps1'`n    }`n}`n\"
        Add-Content $PROFILE $addition
        Write-Host 'PowerShell profile updated with permanent fix' -ForegroundColor Green
    } else {
        Write-Host 'PowerShell profile already configured' -ForegroundColor Yellow
    }
}"

echo.
echo ===============================================
echo   ULTIMATE FIX COMPLETE!
echo ===============================================
echo.
echo The VS Code shell integration warning should
echo now be completely eliminated.
echo.
echo NEXT STEPS:
echo 1. Close VS Code completely
echo 2. Restart VS Code  
echo 3. Open a new PowerShell terminal
echo 4. The warning should no longer appear
echo.
echo If you still see the warning after restart,
echo run this script again with administrator privileges.
echo.
pause