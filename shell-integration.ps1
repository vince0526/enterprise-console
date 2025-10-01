<#
Enterprise Console PowerShell shell integration (stub)
This script is intentionally minimal and safe to load in any PowerShell session.
Add your custom aliases/functions below as needed.
#>

# Prevent double-loading in the same session
if ($global:EC_SHELL_INTEGRATION_LOADED) { return }
$global:EC_SHELL_INTEGRATION_LOADED = $true

# Repository root (directory of this script)
$script:RepoRoot = Split-Path -Parent $MyInvocation.MyCommand.Path

# Hint VS Code to enable shell integration & command detection markers
$env:VSCODE_SHELL_INTEGRATION = "1"
if (-not $env:TERM_PROGRAM) { $env:TERM_PROGRAM = "vscode" }

# If a fast integration script exists, dot-source it first for richer commands
$fast = Join-Path $script:RepoRoot 'shell-integration-fast.ps1'
if (Test-Path $fast) {
    . $fast
}

function Invoke-ECArtisan {
    [CmdletBinding(PositionalBinding = $false)]
    param(
        [Parameter(ValueFromRemainingArguments = $true)]
        [string[]]$Args
    )
    Push-Location $script:RepoRoot
    try {
        php artisan @Args
    }
    finally {
        Pop-Location
    }
}

# Handy alias: `ec-artisan migrate` -> runs `php artisan migrate` from the repo root
Set-Alias ec-artisan Invoke-ECArtisan -ErrorAction SilentlyContinue

# Quiet success (no output by default)