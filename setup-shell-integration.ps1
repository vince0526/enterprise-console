<#
Enable VS Code shell integration and EMC shell commands for this repository.
#>
[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'

Write-Host "Configuring VS Code settings for shell integration..." -ForegroundColor Cyan
$settingsDir = Join-Path $PSScriptRoot '.vscode'
$settingsFile = Join-Path $settingsDir 'settings.json'
if (-not (Test-Path $settingsDir)) { New-Item -ItemType Directory -Path $settingsDir | Out-Null }

# Merge or create settings.json with required keys
$required = @{
  'terminal.integrated.shellIntegration.enabled' = $true
  'terminal.integrated.shellIntegration.showWelcome' = $false
  'terminal.integrated.commandDetection.enabled' = $true
}

$settings = @{}
if (Test-Path $settingsFile) {
  try { $settings = Get-Content $settingsFile -Raw | ConvertFrom-Json -AsHashtable } catch { $settings = @{} }
}
foreach ($k in $required.Keys) { $settings[$k] = $required[$k] }
$settings | ConvertTo-Json -Depth 10 | Out-File -Encoding UTF8 $settingsFile

Write-Host "Updating PowerShell profile to load shell integration..." -ForegroundColor Cyan
$profilePath = $PROFILE
$profileDir = Split-Path -Parent $profilePath
if (-not (Test-Path $profileDir)) { New-Item -ItemType Directory -Path $profileDir | Out-Null }

$sourceLine = ". '$PSScriptRoot\shell-integration.ps1'"
if (Test-Path $profilePath) {
  $content = Get-Content $profilePath -Raw
  if ($content -notmatch [regex]::Escape($sourceLine)) {
    Add-Content $profilePath "`n$sourceLine`n"
  }
} else {
  Set-Content $profilePath $sourceLine -Encoding UTF8
}

Write-Host "Shell integration enabled. Restart VS Code terminals (or run: . $PROFILE)." -ForegroundColor Green