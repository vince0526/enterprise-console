# EMC Endpoint Sweep - PowerShell
# Purpose: Hit core page, assets, and 10 module routes; output concise PASS/FAIL lines and a summary.

param(
    [string]$BaseUrl = 'http://127.0.0.1:8000'
)

$ErrorActionPreference = 'SilentlyContinue'

function Test-Ready {
    param([string]$Url, [int]$Attempts = 30, [int]$DelayMs = 250)
    for ($i = 0; $i -lt $Attempts; $i++) {
        try {
            $r = Invoke-WebRequest -Uri $Url -UseBasicParsing -TimeoutSec 2
            if ($r.StatusCode -ge 200) { return $true }
        }
        catch {}
        Start-Sleep -Milliseconds $DelayMs
    }
    return $false
}

$paths = @(
    '/health',
    '/emc',
    '/css/emc.css',
    '/js/emc.js',
    '/emc/db',
    '/emc/tables',
    '/emc/files',
    '/emc/users',
    '/emc/reports',
    '/emc/ai',
    '/emc/comms',
    '/emc/settings',
    '/emc/activity',
    '/emc/about'
)

Write-Output "Waiting for server at $BaseUrl ..."
if (-not (Test-Ready -Url "$BaseUrl/health")) {
    Write-Output "WARN server not ready; proceeding anyway"
}

$pass = 0
$fail = 0

foreach ($p in $paths) {
    $u = "$BaseUrl$p"
    try {
        $r = Invoke-WebRequest -Uri $u -UseBasicParsing -TimeoutSec 8
        $code = if ($r.StatusCode) { [int]$r.StatusCode } else { 200 }
        if ($p -eq '/css/emc.css' -or $p -eq '/js/emc.js') {
            $len = if ($r.Headers['Content-Length']) { [int]$r.Headers['Content-Length'] } else { [int]$r.RawContentLength }
            Write-Output ("PASS {0} {1} size={2}" -f $p, $code, $len)
        }
        else {
            Write-Output ("PASS {0} {1}" -f $p, $code)
        }
        $pass++
    }
    catch {
        $resp = $_.Exception.Response
        $code = if ($resp) { [int]$resp.StatusCode } else { 0 }
        Write-Output ("FAIL {0} {1}" -f $p, $code)
        $fail++
    }
}

Write-Output ("SUMMARY pass={0} fail={1} total={2}" -f $pass, $fail, ($pass + $fail))
