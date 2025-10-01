<#
Enterprise Console PowerShell shell integration (stub)
This script is intentionally minimal and safe to load in any PowerShell session.
Add your custom aliases/functions below as needed.
#>

# Prevent double-loading in the same session, but allow forced reload
$forceReload = ($env:EC_SHELL_RELOAD -eq '1')
if ($global:EC_SHELL_INTEGRATION_LOADED -and -not $forceReload) { return }
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

# Composer helper that runs from repo root
function Invoke-ECComposer {
    [CmdletBinding(PositionalBinding = $false)]
    param(
        [Parameter(ValueFromRemainingArguments = $true)]
        [string[]]$Args
    )
    Push-Location $script:RepoRoot
    try {
        composer @Args
    }
    finally {
        Pop-Location
    }
}
Set-Alias ec-composer Invoke-ECComposer -ErrorAction SilentlyContinue

# NPM helper that runs from repo root
function Invoke-ECNpm {
    [CmdletBinding(PositionalBinding = $false)]
    param(
        [Parameter(ValueFromRemainingArguments = $true)]
        [string[]]$Args
    )
    Push-Location $script:RepoRoot
    try {
        npm @Args
    }
    finally {
        Pop-Location
    }
}
Set-Alias ec-npm Invoke-ECNpm -ErrorAction SilentlyContinue

# Quality and workflow shortcuts
function ec-pint { Push-Location $script:RepoRoot; try { composer run lint } finally { Pop-Location } }
function ec-stan { Push-Location $script:RepoRoot; try { composer run stan } finally { Pop-Location } }
function ec-check { Push-Location $script:RepoRoot; try { composer run check } finally { Pop-Location } }
function ec-check-all { Push-Location $script:RepoRoot; try { composer run check-all } finally { Pop-Location } }
function ec-test { Push-Location $script:RepoRoot; try { php artisan test } finally { Pop-Location } }
function ec-build { Push-Location $script:RepoRoot; try { npm run build } finally { Pop-Location } }
function ec-dev { Push-Location $script:RepoRoot; try { composer run dev } finally { Pop-Location } }

# Composite helpers
function ec-quality { Push-Location $script:RepoRoot; try { composer run check-all } finally { Pop-Location } }
function ec-fresh { Push-Location $script:RepoRoot; try { php artisan migrate:fresh --seed } finally { Pop-Location } }

# End of integration helpers

# Reload helper to pick up changes without opening a new terminal
function ec-reload {
    [CmdletBinding()]
    param()
    try {
        Remove-Variable -Name EC_SHELL_INTEGRATION_LOADED -Scope Global -ErrorAction SilentlyContinue
        $env:EC_SHELL_RELOAD = '1'
        . (Join-Path $script:RepoRoot 'shell-integration.ps1')
    }
    finally {
        Remove-Item Env:EC_SHELL_RELOAD -ErrorAction SilentlyContinue | Out-Null
    }
}
