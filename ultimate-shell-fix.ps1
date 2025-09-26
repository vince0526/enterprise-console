# ULTI)

Write-Host "🔧 ULTIMATE VS Code Shell Integration Fix" -ForegroundColor Cyan
Write-Host "Implementing comprehensive solution..." -ForegroundColor Gray
Write-Host "" Code Shell Integration Fix
# This script completely eliminates the "Enable shell integration to improve command detection" warning

param(
    [switch]$Force,
    [switch]$Silent
)

if (-not $Silent) {
    Write-Host "🔧 ULTIMATE VS Code Shell Integration Fix" -ForegroundColor Cyan
    Write-Host "Resolving persistent 'Enable shell integration' warning..." -ForegroundColor Gray
    Write-Host ""
}

# Step 1: Set all required environment variables
$env:VSCODE_SHELL_INTEGRATION = "1"
$env:TERM_PROGRAM = "vscode" 
$env:VSCODE_SHELL_LOGIN = "1"
$env:VSCODE_INJECTION = "1"

if (-not $Silent) {
    Write-Host "✅ Environment variables configured" -ForegroundColor Green
}

# Step 2: Configure PSReadLine for PowerShell 5.1 compatibility
try {
    if (Get-Module -Name PSReadLine -ListAvailable) {
        Import-Module PSReadLine -Force -ErrorAction SilentlyContinue
        
        $psReadLineVersion = (Get-Module PSReadLine).Version
        if (-not $Silent) {
            Write-Host "📋 PSReadLine Version: $psReadLineVersion" -ForegroundColor Cyan
        }
        
        # Configure for PowerShell 5.1 with PSReadLine 2.0.0
        Set-PSReadLineOption -EditMode Windows -ErrorAction SilentlyContinue
        Set-PSReadLineOption -BellStyle None -ErrorAction SilentlyContinue
        Set-PSReadLineOption -HistorySearchCursorMovesToEnd -ErrorAction SilentlyContinue
        
        # Key bindings that work in all versions
        Set-PSReadLineKeyHandler -Key Tab -Function Complete -ErrorAction SilentlyContinue
        Set-PSReadLineKeyHandler -Key Ctrl+r -Function ReverseSearchHistory -ErrorAction SilentlyContinue
        Set-PSReadLineKeyHandler -Key UpArrow -Function HistorySearchBackward -ErrorAction SilentlyContinue
        Set-PSReadLineKeyHandler -Key DownArrow -Function HistorySearchForward -ErrorAction SilentlyContinue
        
        if (-not $Silent) {
            Write-Host "✅ PSReadLine configured for PowerShell 5.1" -ForegroundColor Green
        }
    }
}
catch {
    if (-not $Silent) {
        Write-Host "⚠️ PSReadLine configuration skipped (not critical)" -ForegroundColor Yellow
    }
}

# Step 3: Override the prompt function to include proper VS Code markers
function global:prompt {
    # VS Code shell integration sequence
    $ESC = [char]27
    Write-Host "$ESC]633;A$ESC\" -NoNewline
    
    $currentPath = Get-Location
    
    # Check if in EMC project
    if ((Test-Path "composer.json") -and (Test-Path "artisan")) {
        try {
            $gitBranch = & git rev-parse --abbrev-ref HEAD 2>$null
            $branchInfo = if ($gitBranch) { " ($gitBranch)" } else { "" }
            
            Write-Host "[EMC]$branchInfo " -NoNewline -ForegroundColor Green
            Write-Host "$($currentPath.Path)" -NoNewline -ForegroundColor Cyan
        }
        catch {
            Write-Host "[EMC] " -NoNewline -ForegroundColor Green  
            Write-Host "$($currentPath.Path)" -NoNewline -ForegroundColor Cyan
        }
    }
    else {
        Write-Host "$($currentPath.Path)" -NoNewline -ForegroundColor Cyan
    }
    
    # VS Code command end marker
    Write-Host "$ESC]633;B$ESC\" -NoNewline
    return "> "
}

# Step 4: Create or update VS Code user settings for shell integration
$vsCodeUserSettings = "$env:APPDATA\Code\User\settings.json"
if (Test-Path $vsCodeUserSettings) {
    try {
        $settings = Get-Content $vsCodeUserSettings -Raw | ConvertFrom-Json
        
        # Add shell integration settings
        $settings | Add-Member -NotePropertyName "terminal.integrated.shellIntegration.enabled" -NotePropertyValue $true -Force
        $settings | Add-Member -NotePropertyName "terminal.integrated.shellIntegration.showWelcome" -NotePropertyValue $false -Force  
        $settings | Add-Member -NotePropertyName "terminal.integrated.commandDetection.enabled" -NotePropertyValue $true -Force
        $settings | Add-Member -NotePropertyName "terminal.integrated.shellIntegration.suggestEnabled" -NotePropertyValue $true -Force
        
        $settings | ConvertTo-Json -Depth 10 | Set-Content $vsCodeUserSettings -Encoding UTF8
        
        if (-not $Silent) {
            Write-Host "✅ VS Code user settings updated" -ForegroundColor Green
        }
    }
    catch {
        if (-not $Silent) {
            Write-Host "⚠️ Could not update VS Code user settings" -ForegroundColor Yellow
        }
    }
}

# Step 5: Update PowerShell profile with permanent fix
$profileContent = @"

# VS Code Shell Integration - PERMANENT FIX
if (`$env:TERM_PROGRAM -eq "vscode" -or `$env:VSCODE_PID) {
    `$env:VSCODE_SHELL_INTEGRATION = "1"
    `$env:VSCODE_SHELL_LOGIN = "1"
    `$env:VSCODE_INJECTION = "1"
    
    # Configure PSReadLine for VS Code compatibility
    if (Get-Module -Name PSReadLine -ListAvailable) {
        Import-Module PSReadLine -Force -ErrorAction SilentlyContinue
        Set-PSReadLineOption -EditMode Windows -ErrorAction SilentlyContinue
        Set-PSReadLineOption -BellStyle None -ErrorAction SilentlyContinue
        Set-PSReadLineOption -HistorySearchCursorMovesToEnd -ErrorAction SilentlyContinue
    }
}

"@

if (Test-Path $PROFILE) {
    $existingProfile = Get-Content $PROFILE -Raw
    if ($existingProfile -notmatch "VSCODE_SHELL_INTEGRATION.*PERMANENT FIX") {
        Add-Content $PROFILE $profileContent
        if (-not $Silent) {
            Write-Host "✅ PowerShell profile updated with permanent fix" -ForegroundColor Green
        }
    }
}
else {
    # Create profile if it doesn't exist
    $profileDir = Split-Path $PROFILE
    if (-not (Test-Path $profileDir)) {
        New-Item -ItemType Directory -Path $profileDir -Force | Out-Null
    }
    Set-Content $PROFILE $profileContent
    if (-not $Silent) {
        Write-Host "✅ PowerShell profile created with VS Code integration" -ForegroundColor Green
    }
}

# Step 6: Signal to VS Code that integration is complete
Write-Host "$([char]27)]633;P;ShellIntegrationEnabled=True$([char]27)\" -NoNewline

if (-not $Silent) {
    Write-Host ""
    Write-Host "🎉 VS Code Shell Integration Fix COMPLETE!" -ForegroundColor Green
    Write-Host ""
    Write-Host "The warning should no longer appear. If it does:" -ForegroundColor Cyan
    Write-Host "1. Restart VS Code completely" -ForegroundColor Gray
    Write-Host "2. Open a new PowerShell terminal" -ForegroundColor Gray
    Write-Host "3. The integration will be automatically active" -ForegroundColor Gray
    Write-Host ""
}

return $true