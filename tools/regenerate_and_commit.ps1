<#
Runs the Python converter, commits regenerated DOCX if it changed, and pushes.
Use with care: this script adds a commit with [skip ci] to avoid triggering CI.
#>

param(
    [switch]$Push
)

$repoRoot = Resolve-Path -Path .
Set-Location $repoRoot

# Run converter
python tools\python-docx-sample\edit_docx.py

# Check git status
$changed = git status --porcelain
if (-not $changed) {
    Write-Host "No changes to commit."
    exit 0
}

# Stage specific files
git add docs/modules.docx docs/modules-edited.docx
$commitMsg = "Regenerate modules docx [skip ci]"
git commit -m $commitMsg
if ($Push) {
    git push
}
Write-Host "Committed regenerated docs."
