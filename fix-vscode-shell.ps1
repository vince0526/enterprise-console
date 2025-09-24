# Fix VS Code Shell Integration Warning Script

Write-Host "Fixing VS Code Shell Integration Issues..." -ForegroundColor Cyan

# Set VS Code shell integration environment variables
$env:VSCODE_SHELL_INTEGRATION = "1"
$env:TERM_PROGRAM = "vscode"

Write-Host "Shell integration environment variables set" -ForegroundColor Green

# Configure PSReadLine if available
if (Get-Module -ListAvailable -Name PSReadLine) {
    Import-Module PSReadLine -Force -ErrorAction SilentlyContinue
    Set-PSReadLineOption -PredictionSource History -ErrorAction SilentlyContinue
    Set-PSReadLineOption -PredictionViewStyle ListView -ErrorAction SilentlyContinue
    Set-PSReadLineOption -BellStyle None -ErrorAction SilentlyContinue
    Write-Host "PSReadLine configured for VS Code" -ForegroundColor Green
}

# Load EMC shell integration
if (Test-Path "shell-integration.ps1") {
    . .\shell-integration.ps1
    Write-Host "EMC Shell Integration loaded successfully" -ForegroundColor Green
}

Write-Host ""
Write-Host "Shell Integration Fix Complete!" -ForegroundColor Green
Write-Host "Restart VS Code if you still see the warning message." -ForegroundColor Yellow