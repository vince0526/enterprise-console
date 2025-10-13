param()
$ErrorActionPreference = 'Stop'
$cwd = Get-Location
$envPath = Join-Path -Path $cwd -ChildPath '.env'
$backupPath = Join-Path -Path $cwd -ChildPath '.env.orig.tmp'

# Capture original .env timestamps to avoid triggering file-watchers (e.g., Vite) unnecessarily
$hadEnv = Test-Path $envPath
$origTimes = $null
if ($hadEnv) {
    try {
        $origFile = Get-Item $envPath
        $origTimes = @{
            CreationTime     = $origFile.CreationTime
            LastWriteTime    = $origFile.LastWriteTime
            CreationTimeUtc  = $origFile.CreationTimeUtc
            LastWriteTimeUtc = $origFile.LastWriteTimeUtc
        }
    } catch { }
}

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
        # Restore original .env content and timestamps when possible to avoid watcher restarts
        if (Test-Path $envPath) { Remove-Item -Force $envPath }
        Move-Item -Force $backupPath $envPath
        if ($origTimes) {
            try {
                $envFile = Get-Item $envPath
                $envFile.CreationTime     = $origTimes.CreationTime
                $envFile.LastWriteTime    = $origTimes.LastWriteTime
                $envFile.CreationTimeUtc  = $origTimes.CreationTimeUtc
                $envFile.LastWriteTimeUtc = $origTimes.LastWriteTimeUtc
            } catch { }
        }
    }
    else {
        # If no backup, remove the minimal .env to avoid polluting environment
        if (Test-Path $envPath) { Remove-Item -Force $envPath }
    }
}
