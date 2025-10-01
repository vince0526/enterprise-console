<#
Install recommended VS Code extensions from .vscode/extensions.json using `code` CLI.
#>
[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'
function Info($m) { Write-Host "[vscode-ext] $m" -ForegroundColor Cyan }
function Warn($m) { Write-Host "[vscode-ext] $m" -ForegroundColor Yellow }

if (-not (Get-Command code -ErrorAction SilentlyContinue)) { Warn 'VS Code `code` CLI not found; skipping'; return }

$extFile = Join-Path $PSScriptRoot '..\.vscode\extensions.json'
if (-not (Test-Path $extFile)) { Warn 'No .vscode/extensions.json found'; return }

$json = Get-Content $extFile -Raw | ConvertFrom-Json
if (-not $json.recommendations) { Warn 'No recommendations found'; return }

foreach ($id in $json.recommendations) {
  try {
    Info "Installing $id"
    code --install-extension $id --force | Out-Null
  }
  catch {
    $msg = $_.Exception.Message
    Warn ("Failed to install {0}: {1}" -f $id, $msg)
  }
}
Info "VS Code extension installation complete"
