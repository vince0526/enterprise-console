param(
    [string]$PhpExe = 'php',
    [string]$BindHost = '127.0.0.1',
    [int]$Port = 8000,
    [switch]$NoBrowser
)

function Step($m) { Write-Host "[DEV] $m" -ForegroundColor Cyan }
function Info($m) { Write-Host "[INFO] $m" -ForegroundColor Gray }

$base = "http://${BindHost}:${Port}"

Step "Starting PHP server at $base"
$phpProc = Start-Process -FilePath $PhpExe -ArgumentList @('artisan', 'serve', '--host', $BindHost, '--port', $Port) -PassThru
Info "PHP PID: $($phpProc.Id)"

Step 'Starting Vite dev server'
$viteProc = Start-Process -FilePath 'npm' -ArgumentList @('run', 'dev') -PassThru
Info "Vite PID: $($viteProc.Id)"

if (-not $NoBrowser) {
    Start-Sleep -Seconds 2
    Step 'Opening browser'
    Start-Process $base
}

Write-Host "Press Ctrl+C to exit. PIDs: PHP=$($phpProc.Id) Vite=$($viteProc.Id)"
Wait-Process -Id $phpProc.Id, $viteProc.Id
