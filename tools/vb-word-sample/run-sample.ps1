# Run the VB sample if dotnet SDK is available, otherwise run the Python fallback.
param(
    [string]$InputDoc = $null
)

# Determine repository root relative to this script file
$scriptDir = Split-Path -Path $MyInvocation.MyCommand.Path -Parent
$repoRoot = Resolve-Path -Path (Join-Path $scriptDir "..\..")
Set-Location $repoRoot

if (-not $InputDoc) { $InputDoc = Join-Path $repoRoot 'docs\modules.docx' }

# Ensure sample.docx exists
$sample = Join-Path -Path (Get-Location) -ChildPath "tools\vb-word-sample\sample.docx"
Copy-Item -Path $InputDoc -Destination $sample -Force

# Check for dotnet
$dotnet = Get-Command dotnet -ErrorAction SilentlyContinue
if ($dotnet) {
    Write-Host "dotnet SDK found; attempting to run VB sample..."
    try {
        dotnet restore tools\vb-word-sample\VbWordSample.vbproj
        dotnet run --project tools\vb-word-sample\VbWordSample.vbproj
        Exit 0
    } catch {
        Write-Host "VB sample failed; falling back to Python script. Error: $_"
    }
}

# Fallback to Python
Write-Host "Running Python fallback..."
$py = Get-Command python -ErrorAction SilentlyContinue
if (-not $py) { $py = Get-Command python3 -ErrorAction SilentlyContinue }
if (-not $py) { Write-Error "Python not found. Please install Python or .NET SDK."; Exit 2 }
& $py.Path (Join-Path (Get-Location) 'tools\python-docx-sample\edit_docx.py')

