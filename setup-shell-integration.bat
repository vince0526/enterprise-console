@echo off
setlocal enabledelayedexpansion

echo.
echo EMC Shell Integration Installer (Windows)
echo Setting up enhanced command detection and shell integration...
echo.

REM Check if we're in EMC project
if not exist "composer.json" goto :not_emc_dir
if not exist "artisan" goto :not_emc_dir

REM Get current directory
set "EMC_DIR=%CD%"
set "INTEGRATION_SCRIPT=%EMC_DIR%\shell-integration.ps1"

REM Check PowerShell execution policy
echo Checking PowerShell execution policy...
powershell -Command "Get-ExecutionPolicy" > temp_policy.txt 2>nul
set /p CURRENT_POLICY=<temp_policy.txt
del temp_policy.txt 2>nul

echo Current execution policy: %CURRENT_POLICY%

if "%CURRENT_POLICY%"=="Restricted" (
    echo.
    echo WARNING: PowerShell execution policy is Restricted
    echo Would you like to set it to RemoteSigned for this user? ^(y/n^)
    set /p CHANGE_POLICY=
    if /i "!CHANGE_POLICY!"=="y" (
        echo Changing execution policy...
        powershell -Command "Set-ExecutionPolicy RemoteSigned -Scope CurrentUser -Force" 2>nul
        if !errorlevel! equ 0 (
            echo SUCCESS: Execution policy changed to RemoteSigned
        ) else (
            echo ERROR: Failed to change execution policy
        )
    ) else (
        echo WARNING: Keeping current policy. Integration may not work properly.
    )
)

REM Get PowerShell profile path
echo.
echo Detecting PowerShell profile location...
for /f "tokens=*" %%i in ('powershell -Command "$PROFILE" 2^>nul') do set "PS_PROFILE=%%i"

if "!PS_PROFILE!"=="" (
    echo ERROR: Could not detect PowerShell profile location
    goto :end
)

echo PowerShell Profile: !PS_PROFILE!

REM Create profile directory if it doesn't exist
for %%F in ("!PS_PROFILE!") do set "PS_PROFILE_DIR=%%~dpF"
if not exist "!PS_PROFILE_DIR!" (
    echo Creating PowerShell profile directory...
    mkdir "!PS_PROFILE_DIR!" 2>nul
)

REM Check if integration already added
set ALREADY_ADDED=0
if exist "!PS_PROFILE!" (
    findstr /c:"shell-integration.ps1" "!PS_PROFILE!" > nul 2>&1
    if !errorlevel! equ 0 set ALREADY_ADDED=1
)

if !ALREADY_ADDED! equ 1 (
    echo WARNING: Integration already added to PowerShell profile
    goto :test_integration
)

REM Add integration to profile
echo Adding integration to PowerShell profile...
echo. >> "!PS_PROFILE!" 2>nul
echo # EMC Shell Integration >> "!PS_PROFILE!" 2>nul
echo . "!INTEGRATION_SCRIPT!" >> "!PS_PROFILE!" 2>nul

if !errorlevel! equ 0 (
    echo SUCCESS: Added integration to PowerShell profile
) else (
    echo ERROR: Failed to add integration to PowerShell profile
    goto :end
)

:test_integration
REM Test the integration
echo.
echo Testing integration...
powershell -Command ". '!INTEGRATION_SCRIPT!'; Write-Host 'Integration loaded successfully'" 2>nul

if !errorlevel! equ 0 (
    echo SUCCESS: Shell integration loaded successfully!
) else (
    echo WARNING: Error loading shell integration - check the script manually
)

echo.
echo ============================================
echo   Shell Integration Installation Complete!
echo ============================================
echo.
echo What has been configured:
echo   - Enhanced command detection
echo   - EMC-specific aliases (emc-serve, emc-test, etc.)
echo   - Automatic activity logging
echo   - Enhanced prompt with EMC/Git status
echo   - Command completion improvements
echo.
echo To activate immediately, restart PowerShell or run:
echo   . "!INTEGRATION_SCRIPT!"
echo.
echo Available EMC commands:
echo   emc-serve     - Start development server
echo   emc-test      - Run tests  
echo   emc-quality   - Run code quality checks
echo   emc-deploy    - Deploy changes to GitHub
echo   emc-activity  - Log development activity
echo.
goto :end

:not_emc_dir
echo ERROR: This doesn't appear to be an EMC project directory
echo Please run this script from the enterprise-console directory
echo.

:end
pause