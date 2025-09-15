<#
Bootstrap Development Environment for Enterprise Console
Usage (PowerShell, run as non-admin unless prompted):
  Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
  ./scripts/bootstrap-dev-environment.ps1 -RepositoryUrl https://github.com/your-org/enterprise-console.git -TargetPath C:\laragon\www\enterprise-console
Parameters:
  -RepositoryUrl : Git clone URL
  -TargetPath    : Destination directory
  -SkipClone     : Switch to skip git clone if repo already present
  -InstallVSCodeExtensions : Install recommended VS Code extensions
Requires pre-installed: Git, Laragon (PHP 8.3), Node.js LTS, VS Code
#>
[CmdletBinding()]
param(
  [string]$RepositoryUrl = 'https://github.com/your-org/enterprise-console.git',
  [string]$TargetPath = 'C:\laragon\www\enterprise-console',
  [switch]$SkipClone,
  [switch]$InstallVSCodeExtensions
)

function Write-Step($msg){ Write-Host "[STEP] $msg" -ForegroundColor Cyan }
function Write-Info($msg){ Write-Host "[INFO] $msg" -ForegroundColor Gray }
function Write-Ok($msg){ Write-Host "[ OK ] $msg" -ForegroundColor Green }
function Assert-Cmd($cmd){ if(-not (Get-Command $cmd -ErrorAction SilentlyContinue)){ Write-Host "Missing required command: $cmd" -ForegroundColor Red; exit 1 } }

Write-Step 'Pre-flight checks'
Assert-Cmd git
Assert-Cmd php
Assert-Cmd composer
Assert-Cmd node
Assert-Cmd npm

if(-not $SkipClone){
  Write-Step "Cloning repository -> $TargetPath"
  if(Test-Path $TargetPath){ Write-Info 'Target path exists, skipping clone'; }
  else { git clone $RepositoryUrl $TargetPath }
}

Set-Location $TargetPath

Write-Step 'Copying .env if missing'
if(-not (Test-Path .env) -and (Test-Path .env.example)){ Copy-Item .env.example .env }

Write-Step 'Creating SQLite database file'
if(-not (Test-Path .\database\database.sqlite)){ New-Item -ItemType File .\database\database.sqlite | Out-Null }

Write-Step 'Composer install'
composer install --no-interaction

Write-Step 'NPM install'
npm install

Write-Step 'Generate app key'
php artisan key:generate --force

Write-Step 'Run migrations'
php artisan migrate --force

Write-Step 'Link storage'
php artisan storage:link | Out-Null

if($InstallVSCodeExtensions){
  Write-Step 'Installing VS Code extensions'
  $exts = @(
    'bmewburn.vscode-intelephense-client',
    'ryannaddy.laravel-artisan',
    'shufo.vscode-blade-formatter',
    'onecentlin.laravel-blade',
    'amiralizadeh9480.laravel-extra-intellisense',
    'open-sourcing.laravel-pint',
    'bradlc.vscode-tailwindcss',
    'csstools.postcss',
    'eamodio.gitlens',
    'GitHub.vscode-pull-request-github',
    'mikestead.dotenv',
    'usernamehw.errorlens',
    'christian-kohler.path-intellisense',
    'yzhang.markdown-all-in-one'
  )
  foreach($e in $exts){ code --install-extension $e }
}

Write-Step 'Quality check (optional)'
try { composer run check-all } catch { Write-Info 'Quality check failed or partial; review manually.' }

Write-Step 'Finished'
Write-Ok 'Environment bootstrap complete.'
Write-Info 'Start development: composer run dev'
