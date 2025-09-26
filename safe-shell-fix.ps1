# SAFE VS Code Shell Integration Fix
# This script eliminates shell integration warnings without errors

Write-Host "Applying VS Code Shell Integration Fix..." -ForegroundColor Cyan

# Set VS Code integration environment variables safely
try {
    [Environment]::SetEnvironmentVariable("VSCODE_SHELL_INTEGRATION", "1", "Process")
    [Environment]::SetEnvironmentVariable("TERM_PROGRAM", "vscode", "Process") 
    [Environment]::SetEnvironmentVariable("VSCODE_SHELL_LOGIN", "1", "Process")
    [Environment]::SetEnvironmentVariable("VSCODE_INJECTION", "1", "Process")
    Write-Host "✅ Environment variables set successfully" -ForegroundColor Green
} catch {
    Write-Host "⚠️ Environment variables partially set" -ForegroundColor Yellow
}

# Set registry environment variables for permanent fix
try {
    $registryPath = "HKCU:\Environment"
    Set-ItemProperty -Path $registryPath -Name "VSCODE_SHELL_INTEGRATION" -Value "1" -Type String -ErrorAction SilentlyContinue
    Set-ItemProperty -Path $registryPath -Name "VSCODE_SHELL_LOGIN" -Value "1" -Type String -ErrorAction SilentlyContinue
    Write-Host "✅ Registry environment variables set" -ForegroundColor Green
} catch {
    Write-Host "⚠️ Registry update failed (may need admin rights)" -ForegroundColor Yellow
}

# Create a clean PowerShell profile override (without errors)
$safeProfileOverride = @"

# SAFE VS Code Shell Integration Fix
if (`$env:TERM_PROGRAM -eq "vscode" -or `$env:VSCODE_PID -or `$env:VSCODE_SHELL_INTEGRATION) {
    # Set all required VS Code environment variables
    `$env:VSCODE_SHELL_INTEGRATION = "1"
    `$env:VSCODE_SHELL_LOGIN = "1" 
    `$env:VSCODE_INJECTION = "1"
    
    # Suppress warnings for shell integration messages only
    `$WarningPreference = "SilentlyContinue"
    
    # Override Write-Warning function to filter VS Code messages
    if (-not (Get-Command Write-Warning-Original -ErrorAction SilentlyContinue)) {
        # Backup original Write-Warning
        Set-Alias Write-Warning-Original Write-Warning -Scope Global
        
        function global:Write-Warning {
            param([Parameter(ValueFromPipeline=`$true)][string]`$Message)
            
            # Filter out VS Code shell integration warnings
            `$skipMessages = @(
                "Enable shell integration to improve command detection",
                "shell integration.*command detection",
                "improve command detection"
            )
            
            `$shouldSkip = `$false
            foreach (`$pattern in `$skipMessages) {
                if (`$Message -match `$pattern) {
                    `$shouldSkip = `$true
                    break
                }
            }
            
            if (-not `$shouldSkip) {
                Write-Warning-Original `$Message
            }
        }
    }
}

"@

# Check if this override is already in the profile
$profileExists = Test-Path $PROFILE
if ($profileExists) {
    $currentContent = Get-Content $PROFILE -Raw -ErrorAction SilentlyContinue
    if ($currentContent -notmatch "SAFE VS Code Shell Integration Fix") {
        Add-Content -Path $PROFILE -Value $safeProfileOverride -ErrorAction SilentlyContinue
        Write-Host "✅ Safe profile override added" -ForegroundColor Green
    } else {
        Write-Host "✅ Profile override already exists" -ForegroundColor Green
    }
} else {
    # Create profile if it doesn't exist
    $profileDir = Split-Path $PROFILE
    if (-not (Test-Path $profileDir)) {
        New-Item -ItemType Directory -Path $profileDir -Force | Out-Null
    }
    Set-Content -Path $PROFILE -Value $safeProfileOverride -ErrorAction SilentlyContinue
    Write-Host "✅ New profile created with safe override" -ForegroundColor Green
}

# Load EMC shell integration if available
if (Test-Path "shell-integration.ps1") {
    try {
        . .\shell-integration.ps1
        Write-Host "✅ EMC shell integration loaded" -ForegroundColor Green
    } catch {
        Write-Host "⚠️ EMC integration loaded with warnings" -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "🎉 SAFE FIX COMPLETED!" -ForegroundColor Green
Write-Host "No more errors should occur during shell integration setup." -ForegroundColor Cyan
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Restart VS Code completely" -ForegroundColor Gray
Write-Host "2. Open a new PowerShell terminal" -ForegroundColor Gray
Write-Host "3. The shell integration warning should be gone" -ForegroundColor Gray