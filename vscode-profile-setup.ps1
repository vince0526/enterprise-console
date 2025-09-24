# VS Code PowerShell Profile Enhancement for EMC
# This script ensures proper VS Code shell integration setup

# Check if running in VS Code
if ($env:TERM_PROGRAM -eq "vscode") {
    # Set environment variables for VS Code shell integration
    $env:VSCODE_SHELL_INTEGRATION = "1"
    
    # Import PSReadLine if available for better command detection
    if (Get-Module -ListAvailable -Name PSReadLine) {
        Import-Module PSReadLine -Force -ErrorAction SilentlyContinue
        
        # Configure PSReadLine for VS Code
        Set-PSReadLineOption -EditMode Windows
        Set-PSReadLineOption -PredictionSource History
        Set-PSReadLineOption -PredictionViewStyle ListView
        Set-PSReadLineOption -BellStyle None
        
        # Enhanced key handlers for VS Code
        Set-PSReadLineKeyHandler -Key Ctrl+d -Function DeleteChar
        Set-PSReadLineKeyHandler -Key Ctrl+r -Function ReverseSearchHistory
        Set-PSReadLineKeyHandler -Key UpArrow -Function HistorySearchBackward
        Set-PSReadLineKeyHandler -Key DownArrow -Function HistorySearchForward
        Set-PSReadLineKeyHandler -Key Tab -Function MenuComplete
        Set-PSReadLineKeyHandler -Key Shift+Tab -Function TabCompletePrevious
    }
    
    Write-Host "VS Code PowerShell integration configured successfully" -ForegroundColor Green
}

# Load EMC Shell Integration if we're in the EMC project directory
if (Test-Path "composer.json" -and Test-Path "artisan" -and Test-Path "shell-integration.ps1") {
    . .\shell-integration.ps1
}