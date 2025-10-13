param()
$ErrorActionPreference = 'Stop'
$cwd = Get-Location
$envPath = Join-Path -Path $cwd -ChildPath '.env'
$backupPath = Join-Path -Path $cwd -ChildPath '.env.orig.tmp'

# Minimal safe .env content to satisfy phpdotenv while keeping app side-effects minimal
$minimalEnv = @(
    'APP_NAME=Laravel',
    'APP_ENV=local',
    'APP_KEY=',
    'APP_DEBUG=true',
    'APP_URL=http://localhost'
) -join [Environment]::NewLine

try {
    if (Test-Path $envPath) {
        Copy-Item -Force $envPath $backupPath
    }
    # Write minimal .env without BOM to satisfy phpdotenv
    $utf8NoBom = New-Object System.Text.UTF8Encoding($false)
    $sw = New-Object System.IO.StreamWriter($envPath, $false, $utf8NoBom)
    try { $sw.Write($minimalEnv) } finally { $sw.Dispose() }

    & php artisan ide-helper:meta
}
finally {
    if (Test-Path $backupPath) {
        Move-Item -Force $backupPath $envPath
    }
    else {
        # If no backup, remove the minimal .env to avoid polluting environment
        if (Test-Path $envPath) { Remove-Item -Force $envPath }
    }
}
