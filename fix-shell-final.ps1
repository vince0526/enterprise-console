# ULTIMATE VS Code Shell Integration Fix - Simple Version

Write-Host "Fixing VS Code Shell Integration Warning..." -ForegroundColor Cyan

# Set ALL required environment variables for VS Code shell integration
$env:VSCODE_SHELL_INTEGRATION = "1"
$env:TERM_PROGRAM = "vscode" 
$env:VSCODE_SHELL_LOGIN = "1"
$env:VSCODE_INJECTION = "1"
$env:VSCODE_NONCE = [System.Guid]::NewGuid().ToString()

Write-Host "Environment variables set" -ForegroundColor Green

# Configure PSReadLine properly for PowerShell 5.1
if (Get-Module -Name PSReadLine -ListAvailable) {
    Import-Module PSReadLine -Force -ErrorAction SilentlyContinue
    Set-PSReadLineOption -EditMode Windows -ErrorAction SilentlyContinue
    Set-PSReadLineOption -BellStyle None -ErrorAction SilentlyContinue
    Set-PSReadLineOption -HistorySearchCursorMovesToEnd -ErrorAction SilentlyContinue
    Write-Host "PSReadLine configured" -ForegroundColor Green
}

# Create proper VS Code shell integration sequences
function global:prompt {
    $ESC = [char]27
    # Send shell integration start sequence
    Write-Host "$ESC]633;A$ESC\" -NoNewline
    
    $currentPath = Get-Location
    
    if ((Test-Path "composer.json") -and (Test-Path "artisan")) {
        $gitBranch = ""
        try { $gitBranch = git rev-parse --abbrev-ref HEAD 2>$null } catch {}
        $branchInfo = if ($gitBranch) { " ($gitBranch)" } else { "" }
        Write-Host "[EMC]$branchInfo " -NoNewline -ForegroundColor Green
    }
    
    Write-Host "$($currentPath.Path)" -NoNewline -ForegroundColor Cyan
    # Send command end sequence
    Write-Host "$ESC]633;B$ESC\" -NoNewline
    return "> "
}

# Load EMC integration
if (Test-Path "shell-integration.ps1") {
    . .\shell-integration.ps1
}

Write-Host "VS Code Shell Integration Fix Applied!" -ForegroundColor Green
Write-Host "Restart VS Code if warning persists." -ForegroundColor Yellow