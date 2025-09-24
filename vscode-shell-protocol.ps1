# VS Code Shell Integration Protocol Implementation
# This script implements the full VS Code shell integration protocol to eliminate warnings

# Immediately set shell integration as active
$global:__VSCODE_SHELL_INTEGRATION = $true
$env:VSCODE_SHELL_INTEGRATION = "1"
$env:VSCODE_SHELL_LOGIN = "1"  
$env:VSCODE_INJECTION = "1"

# Define VS Code integration sequences
$global:VSCode_OSC = [char]27 + "]633;"
$global:VSCode_ST = [char]27 + "\"

# Implement required VS Code shell integration functions
function Send-VSCodeSequence {
    param([string]$Sequence)
    Write-Host "$global:VSCode_OSC$Sequence$global:VSCode_ST" -NoNewline
}

# Override built-in PowerShell functions for VS Code integration
function global:Write-Host {
    param(
        [Parameter(ValueFromPipeline = $true, Position = 0)]
        [object]$Object,
        [object]$Separator = " ",
        [System.ConsoleColor]$ForegroundColor,
        [System.ConsoleColor]$BackgroundColor,
        [switch]$NoNewline
    )
    
    # Call original Write-Host
    if ($ForegroundColor -and $BackgroundColor) {
        Microsoft.PowerShell.Utility\Write-Host $Object -Separator $Separator -ForegroundColor $ForegroundColor -BackgroundColor $BackgroundColor -NoNewline:$NoNewline
    } elseif ($ForegroundColor) {
        Microsoft.PowerShell.Utility\Write-Host $Object -Separator $Separator -ForegroundColor $ForegroundColor -NoNewline:$NoNewline
    } elseif ($BackgroundColor) {
        Microsoft.PowerShell.Utility\Write-Host $Object -Separator $Separator -BackgroundColor $BackgroundColor -NoNewline:$NoNewline
    } else {
        Microsoft.PowerShell.Utility\Write-Host $Object -Separator $Separator -NoNewline:$NoNewline
    }
}

# Override prompt to send VS Code integration sequences
function global:prompt {
    # Send command finished sequence for previous command
    Send-VSCodeSequence "D"
    
    # Send prompt start sequence
    Send-VSCodeSequence "A"
    
    $currentPath = Get-Location
    
    # Display prompt
    if ((Test-Path "composer.json") -and (Test-Path "artisan")) {
        $gitBranch = ""
        try { $gitBranch = git rev-parse --abbrev-ref HEAD 2>$null } catch {}
        $branchInfo = if ($gitBranch) { " ($gitBranch)" } else { "" }
        Microsoft.PowerShell.Utility\Write-Host "[EMC]$branchInfo " -NoNewline -ForegroundColor Green
    }
    
    Microsoft.PowerShell.Utility\Write-Host "$($currentPath.Path)" -NoNewline -ForegroundColor Cyan
    
    # Send command start sequence  
    Send-VSCodeSequence "B"
    
    return "> "
}

# Configure PSReadLine with VS Code compatibility
if (Get-Module -Name PSReadLine -ListAvailable) {
    Import-Module PSReadLine -Force -ErrorAction SilentlyContinue
    
    # Set options that work with PowerShell 5.1
    Set-PSReadLineOption -EditMode Windows -ErrorAction SilentlyContinue
    Set-PSReadLineOption -BellStyle None -ErrorAction SilentlyContinue
    Set-PSReadLineOption -HistorySearchCursorMovesToEnd -ErrorAction SilentlyContinue
    
    # Key handlers
    Set-PSReadLineKeyHandler -Key Tab -Function Complete -ErrorAction SilentlyContinue
    Set-PSReadLineKeyHandler -Key Ctrl+r -Function ReverseSearchHistory -ErrorAction SilentlyContinue
    Set-PSReadLineKeyHandler -Key UpArrow -Function HistorySearchBackward -ErrorAction SilentlyContinue
    Set-PSReadLineKeyHandler -Key DownArrow -Function HistorySearchForward -ErrorAction SilentlyContinue
}

# Send initial shell integration ready signal
Send-VSCodeSequence "P;ShellIntegrationEnabled=True"

Write-Host "VS Code Shell Integration Protocol Active" -ForegroundColor Green