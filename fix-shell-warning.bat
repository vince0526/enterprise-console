@echo off
echo Fixing VS Code Shell Integration Warning...
echo.

REM Set environment variables for VS Code shell integration
set VSCODE_SHELL_INTEGRATION=1
set TERM_PROGRAM=vscode

echo Environment variables set for shell integration

REM Load the EMC shell integration
powershell -Command ". .\shell-integration.ps1"

echo.
echo Shell integration fix complete!
echo Restart VS Code if the warning persists.
pause