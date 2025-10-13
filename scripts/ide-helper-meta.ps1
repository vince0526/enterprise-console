param()
$ErrorActionPreference = 'Stop'
$envPath = Join-Path -Path (Get-Location) -ChildPath '.env'
$tmpPath = Join-Path -Path (Get-Location) -ChildPath '.env.tmp'
try {
    if (Test-Path $envPath) {
        Move-Item -Force $envPath $tmpPath
    }
    & php artisan ide-helper:meta
}
finally {
    if (Test-Path $tmpPath) {
        Move-Item -Force $tmpPath $envPath
    }
}
