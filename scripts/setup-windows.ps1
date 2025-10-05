<#
Enterprise Console - Windows Setup Helper (Option B)

Usage examples (PowerShell):
  # 1) Provide your PHP 8.2/8.3 directory so this session uses it
  ./scripts/setup-windows.ps1 -PhpDir 'C:\laragon\bin\php\php-8.3.x' -Seed -Serve

  # 2) If PATH already points to PHP >= 8.2 (php -v shows 8.2+), you can omit -PhpDir
  ./scripts/setup-windows.ps1 -Seed -Serve

What it does:
  - Ensures php in this session is 8.2+
  - Copies .env if missing and generates APP_KEY if empty
  - Ensures SQLite database file exists
  - composer install (if vendor missing)
  - npm install (if node_modules missing) and vite build
  - php artisan migrate and optional seeding
  - php artisan storage:link
  - optional: php artisan serve
#>
[CmdletBinding(PositionalBinding = $false)]
param(
    [string]$PhpDir,
    [switch]$Seed,
    [switch]$Serve,
    [switch]$SkipNpm,
    [switch]$ResetCaches,
    [string]$BindHost = '127.0.0.1',
    [int]$Port = 8000,
    [switch]$Sweep
)

function Step($m) { Write-Host "[STEP] $m" -ForegroundColor Cyan }
function Info($m) { Write-Host "[INFO] $m" -ForegroundColor Gray }
function Ok($m) { Write-Host "[ OK ] $m" -ForegroundColor Green }
function Warn($m) { Write-Host "[WARN] $m" -ForegroundColor Yellow }
function Fail($m) { Write-Host "[FAIL] $m" -ForegroundColor Red }

function Get-NodeVersion() {
    try {
        $out = & node --version 2>$null
        if (-not $out) { return $null }
        # Strip leading 'v'
        $str = ($out -replace "^v", "").Trim()
        try { return [version]$str } catch { return $null }
    }
    catch { return $null }
}

function ComposerAvailable() {
    return [bool](Get-Command composer -ErrorAction SilentlyContinue)
}

function Invoke-Composer([string]$composerArgs) {
    if (ComposerAvailable) {
        & composer $composerArgs
    }
    else {
        Warn 'composer not found on PATH. Please install Composer (https://getcomposer.org/download/) or ensure composer.exe is available.'
        throw 'ComposerMissing'
    }
}

function Set-EnvVar([string]$key, [string]$value, [string]$envPath) {
    $content = Get-Content $envPath -Raw
    if ($content -match "(?m)^$([regex]::Escape($key))=") {
        if ($content -notmatch "(?m)^$([regex]::Escape($key))=$([regex]::Escape($value))$") {
            Info "Updating $key in .env"
            $content = $content -replace "(?m)^$([regex]::Escape($key))=.*$", "$key=$value"
            $content | Set-Content $envPath -NoNewline
        }
    }
    else {
        Info "Appending $key to .env"
        Add-Content -Path $envPath -Value "`n$key=$value"
    }
}

function PortInUse([int]$p) {
    try {
        $conns = Get-NetTCPConnection -LocalPort $p -State Listen -ErrorAction SilentlyContinue
        return ($null -ne $conns)
    }
    catch { return $false }
}

try {
    Push-Location (Split-Path -Parent $MyInvocation.MyCommand.Path) | Out-Null
    # Repo root is parent of scripts folder
    $repoRoot = Resolve-Path (Join-Path (Get-Location) '..')
    Set-Location $repoRoot

    Step 'Pre-flight: PHP availability and version'

    function Get-PHPVersion([string]$exe) {
        try { & $exe -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION.'.'.PHP_RELEASE_VERSION;" 2>$null } catch { $null }
    }

    function Find-PHP83 {
        $candidates = @()
        # If -PhpDir was specified, make it first
        if ($PhpDir) {
            $candidates += (Join-Path $PhpDir 'php.exe')
        }
        # Common install paths
        $commonRoots = @(
            'C:\laragon\bin\php',
            'C:\xampp',
            'C:\Program Files\php',
            'C:\Program Files (x86)\php',
            'C:\tools\php',
            'C:\php'
        )
        foreach ($root in $commonRoots) {
            if (Test-Path $root) {
                Get-ChildItem -Path $root -Recurse -Filter 'php.exe' -ErrorAction SilentlyContinue | ForEach-Object { $candidates += $_.FullName }
            }
        }
        # Current where.exe result last (may be old)
        try { $where = & where.exe php 2>$null; if ($where) { $candidates += $where } } catch {}
        # Unique
        $candidates = $candidates | Select-Object -Unique
        # Sort by version desc and return first >= 8.2
        $scored = @()
        foreach ($exe in $candidates) {
            $ver = Get-PHPVersion $exe
            if ($ver) {
                try { $v = [version]$ver } catch { continue }
                $scored += [pscustomobject]@{Exe = $exe; Ver = $v }
            }
        }
        $best = $scored | Sort-Object Ver -Descending | Where-Object { $_.Ver.Major -gt 8 -or ($_.Ver.Major -eq 8 -and $_.Ver.Minor -ge 2) } | Select-Object -First 1
        if ($best) { return $best.Exe } else { return $null }
    }

    # If -PhpDir provided, prepend it; else try to auto-find a suitable php.exe
    if ($PhpDir) {
        $phpExe = Join-Path $PhpDir 'php.exe'
        if (-not (Test-Path $phpExe)) { Fail "php.exe not found in -PhpDir: $PhpDir"; exit 1 }
        $env:Path = "$PhpDir;$env:Path"
        Info "Temporarily prepended to PATH: $PhpDir"
    }
    else {
        $auto = Find-PHP83
        if ($auto) {
            $autoDir = Split-Path -Parent $auto
            $env:Path = "$autoDir;$env:Path"
            Info "Auto-selected PHP >= 8.2: $auto"
        }
        else {
            Warn 'No PHP 8.2+ found automatically. You can install Laragon (PHP 8.3) or pass -PhpDir to this script.'
        }
    }

    $phpBin = & php -r "echo PHP_BINARY;" 2>$null
    if (-not $phpBin) { Fail 'php not found on PATH. Install PHP 8.2/8.3 or provide -PhpDir'; exit 1 }

    $phpVersion = & php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION.'.'.PHP_RELEASE_VERSION;" 2>$null
    if (-not $phpVersion) { $phpVersion = (php -v) }
    Info "Using PHP: $phpBin ($phpVersion)"
    try { $v = [version]$phpVersion } catch { $v = [version]'0.0.0' }
    if ($v.Major -lt 8 -or ($v.Major -eq 8 -and $v.Minor -lt 2)) {
        Fail "PHP >= 8.2 required. Detected $phpVersion (from $phpBin). Provide -PhpDir pointing to PHP 8.2/8.3."
        exit 1
    }

    # Verify pdo_sqlite extension loaded
    $pdoSqlite = & php -r "echo extension_loaded('pdo_sqlite') ? 'yes' : 'no';" 2>$null
    if ($pdoSqlite -ne 'yes') {
        Warn 'PHP extension pdo_sqlite is not loaded. SQLite will not work. Enable it in your php.ini (extension=pdo_sqlite).'
    }

    # Node check: vite 7 requires Node 20.19+ or 22.12+
    Step 'Pre-flight: Node.js availability and version'
    $nodeVer = Get-NodeVersion
    if ($null -eq $nodeVer) {
        Warn 'Node.js not found. Frontend build will be skipped unless -SkipNpm is set. Install Node LTS 20.19+ (https://nodejs.org)'
    }
    else {
        Info "Using Node: v$($nodeVer.ToString())"
        # Note: [version] maps 20.19.0 => Major=20, Minor=19, Build=0
        if (($nodeVer.Major -lt 20) -or ($nodeVer.Major -eq 20 -and $nodeVer.Minor -lt 19)) {
            Warn 'Vite requires Node 20.19+ (or 22.12+). Consider upgrading Node for a smoother DX.'
        }
    }

    Step 'Ensure .env exists'
    if (-not (Test-Path .env) -and (Test-Path .env.example)) { Copy-Item .env.example .env; Ok '.env created from .env.example' } else { Info '.env present' }

    Step 'Ensure APP_KEY is set'
    $envText = Get-Content .env -Raw
    if ($envText -notmatch '(?m)^APP_KEY=base64:') {
        try {
            & php artisan key:generate --ansi | Out-Null
            Ok 'APP_KEY generated'
        }
        catch {
            # Fallback: generate random base64 key without artisan
            $bytes = New-Object byte[] 32; [System.Security.Cryptography.RandomNumberGenerator]::Fill($bytes)
            $b64 = [Convert]::ToBase64String($bytes)
            ($envText -replace '(?m)^APP_KEY=.*$', "APP_KEY=base64:$b64") | Set-Content .env -NoNewline
            Ok 'APP_KEY set (fallback)'
        }
    }
    else { Info 'APP_KEY already set' }

    # Enforce sensible dev defaults for local setup (.env)
    Step 'Enforce local .env defaults for dev'
    Set-EnvVar -key 'APP_URL' -value "http://${BindHost}:${Port}" -envPath .env
    Set-EnvVar -key 'DB_CONNECTION' -value 'sqlite' -envPath .env
    Set-EnvVar -key 'DB_DATABASE' -value 'database/database.sqlite' -envPath .env
    Set-EnvVar -key 'SESSION_DRIVER' -value 'file' -envPath .env

    Step 'Ensure SQLite database file'
    $dbFile = Join-Path 'database' 'database.sqlite'
    if (-not (Test-Path $dbFile)) { New-Item -ItemType File $dbFile | Out-Null; Ok "Created $dbFile" } else { Info 'SQLite file exists' }

    if ($ResetCaches) {
        Step 'Reset Laravel caches (config/cache/route/view)'
        php artisan config:clear
        php artisan cache:clear
        php artisan route:clear
        php artisan view:clear
    }

    Step 'Composer install (if needed)'
    if (-not (Test-Path 'vendor')) {
        Invoke-Composer "install --no-interaction --no-progress"
    }
    else { Info 'vendor/ already present (skipping)' }

    if (-not $SkipNpm) {
        Step 'Node dependencies and build'
        if ((Test-Path 'package.json') -and -not (Test-Path 'node_modules')) {
            if (Get-Command npm -ErrorAction SilentlyContinue) { npm install } else { Warn 'npm not found; skipping npm install' }
        }
        else { Info 'node_modules/ present or no package.json (skipping install)' }
        if (Get-Command npm -ErrorAction SilentlyContinue) { npm run build } else { Warn 'npm not found; skipping build' }
    }
    else { Info 'SkipNpm specified; skipping npm install/build' }

    Step 'Run migrations'
    php artisan migrate --force

    if ($Seed) {
        Step 'Database seeding (DatabaseSeeder)'
        php artisan db:seed --class "Database\Seeders\DatabaseSeeder" --force
    }

    Step 'Storage link'
    php artisan storage:link | Out-Null

    Ok 'Setup complete'

    if ($Serve) {
        if (PortInUse -p $Port) {
            Warn "Requested port $Port is in use; attempting next available port"
            for ($p = $Port + 1; $p -le ($Port + 20); $p++) {
                if (-not (PortInUse -p $p)) { $Port = $p; break }
            }
        }
        Step "Starting dev server at http://${BindHost}:${Port}"
        php artisan serve --host $BindHost --port $Port
    }

    if ($Sweep -and -not $Serve) {
        Step 'Running endpoint sweep'
        & pwsh -NoProfile -ExecutionPolicy Bypass -File "scripts/emc-endpoint-sweep.ps1" -BaseUrl "http://${BindHost}:${Port}"
    }
}
catch {
    Fail $_
    exit 1
}
finally {
    Pop-Location | Out-Null
}
