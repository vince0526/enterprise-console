<#
Enterprise Console - Bootstrap script
Idempotent setup for a fresh clone or after a pull: deps, env, DB, assets, extensions.
#>
[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'
function Info($m) { Write-Host "[bootstrap] $m" -ForegroundColor Cyan }
function Warn($m) { Write-Host "[bootstrap] $m" -ForegroundColor Yellow }
function Ok($m) { Write-Host "[bootstrap] $m" -ForegroundColor Green }

Set-Location -LiteralPath (Split-Path -Parent $MyInvocation.MyCommand.Path) | Out-Null
Set-Location ..

Info "Checking toolchain"
if (-not (Get-Command php -ErrorAction SilentlyContinue)) { throw 'PHP not found in PATH' }
if (-not (Get-Command composer -ErrorAction SilentlyContinue)) { throw 'Composer not found in PATH' }
if (-not (Get-Command npm -ErrorAction SilentlyContinue)) { throw 'npm not found in PATH' }

Info "Composer install"
composer install --no-interaction --prefer-dist --no-progress | Write-Host

Info "NPM install"
if (Test-Path package-lock.json) { npm ci | Write-Host } else { npm install | Write-Host }

Info ".env setup"
if (-not (Test-Path .env)) {
    if (Test-Path .env.example) { Copy-Item .env.example .env -Force; Info 'Created .env from .env.example' }
    else { Warn 'No .env.example found; creating minimal .env'; "APP_NAME=Laravel`nAPP_ENV=local`nAPP_KEY=`nAPP_DEBUG=true`nAPP_URL=http://localhost" | Out-File -Encoding utf8 .env }
}

Info "App key"
try { php artisan key:generate --force | Write-Host } catch { Warn "key:generate failed: $($_.Exception.Message)" }

Info "Storage link & caches"
try { php artisan storage:link | Write-Host } catch { Warn "storage:link failed" }
php artisan config:clear | Write-Host
php artisan route:clear | Write-Host
php artisan view:clear  | Write-Host

# If using sqlite, ensure DB file exists
try {
    $envText = Get-Content .env -Raw
    if ($envText -match "(?im)^DB_CONNECTION\s*=\s*sqlite\s*$") {
        $dbMatch = [regex]::Match($envText, "(?im)^DB_DATABASE\s*=\s*(.+)$")
        $dbPath = if ($dbMatch.Success) { $dbMatch.Groups[1].Value.Trim() } else { 'database/database.sqlite' }
        if (-not (Test-Path $dbPath)) {
            $dbDir = Split-Path -Parent $dbPath
            if ($dbDir -and -not (Test-Path $dbDir)) { New-Item -ItemType Directory -Path $dbDir | Out-Null }
            New-Item -ItemType File -Path $dbPath | Out-Null
            Info "Created sqlite DB at $dbPath"
        }
    }
}
catch { Warn "sqlite check failed: $($_.Exception.Message)" }

Info "Database migrate (safe)"
try { php artisan migrate --force --no-interaction | Write-Host } catch { Warn "migrate failed: $($_.Exception.Message)" }

Info "Build assets"
try { npm run build | Write-Host } catch { Warn "asset build failed: $($_.Exception.Message)" }

Info "Install VS Code extensions (if CLI available)"
try { & powershell -NoProfile -ExecutionPolicy Bypass -File .\tools\install-vscode-extensions.ps1 } catch { Warn "extension install skipped: $($_.Exception.Message)" }

Ok "Bootstrap complete"
