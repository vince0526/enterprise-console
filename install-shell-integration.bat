@echo off
setlocal enabledelayedexpansion

REM Enterprise Management Console - Shell Integration Installer (Windows)
REM Automatically configures PowerShell shell integration for improved command detection

echo.
echo 🔧 EMC Shell Integration Installer (Windows)
echo Setting up enhanced command detection and shell integration...
echo.

REM Check if we're in EMC project
if not exist "composer.json" (
    echo ❌ This doesn't appear to be an EMC project directory
    echo Please run this script from the enterprise-console directory
    pause
    exit /b 1
)

if not exist "artisan" (
    echo ❌ This doesn't appear to be an EMC project directory
    echo Please run this script from the enterprise-console directory
    pause
    exit /b 1
)

REM Get current directory
set "EMC_DIR=%CD%"
set "INTEGRATION_SCRIPT=%EMC_DIR%\shell-integration.ps1"

REM Check PowerShell execution policy
echo Checking PowerShell execution policy...
powershell -Command "Get-ExecutionPolicy" > temp_policy.txt
set /p CURRENT_POLICY=<temp_policy.txt
del temp_policy.txt

echo Current execution policy: %CURRENT_POLICY%

if "%CURRENT_POLICY%"=="Restricted" (
    echo ⚠️ PowerShell execution policy is Restricted
    echo Would you like to set it to RemoteSigned for this user? (y/n)
    set /p CHANGE_POLICY=
    if /i "!CHANGE_POLICY!"=="y" (
        echo Changing execution policy...
        powershell -Command "Set-ExecutionPolicy RemoteSigned -Scope CurrentUser -Force"
        echo ✅ Execution policy changed to RemoteSigned
    ) else (
        echo ⚠️ Keeping current policy. Integration may not work properly.
    )
)

REM Get PowerShell profile path
echo [94mDetecting PowerShell profile location...[0m
for /f "tokens=*" %%i in ('powershell -Command "$PROFILE"') do set "PS_PROFILE=%%i"

echo PowerShell Profile: %PS_PROFILE%

REM Create profile directory if it doesn't exist
for %%F in ("%PS_PROFILE%") do set "PS_PROFILE_DIR=%%~dpF"
if not exist "%PS_PROFILE_DIR%" (
    echo [94mCreating PowerShell profile directory...[0m
    mkdir "%PS_PROFILE_DIR%"
)

REM Check if integration already added
if exist "%PS_PROFILE%" (
    findstr /c:"%INTEGRATION_SCRIPT%" "%PS_PROFILE%" > nul
    if !errorlevel! equ 0 (
        echo [93m⚠️ Integration already added to PowerShell profile[0m
        goto :test_integration
    )
)

REM Add integration to profile
echo [94mAdding integration to PowerShell profile...[0m
echo. >> "%PS_PROFILE%"
echo # EMC Shell Integration >> "%PS_PROFILE%"
echo . "%INTEGRATION_SCRIPT%" >> "%PS_PROFILE%"
echo [92m✅ Added integration to PowerShell profile[0m

:test_integration
REM Test the integration
echo [94mTesting integration...[0m
powershell -Command ". '%INTEGRATION_SCRIPT%'; Write-Host 'Integration loaded successfully' -ForegroundColor Green"

if !errorlevel! equ 0 (
    echo [92m✅ Shell integration loaded successfully![0m
) else (
    echo [91m❌ Error loading shell integration[0m
    pause
    exit /b 1
)

echo.
echo [92m🎉 Shell Integration Installation Complete![0m
echo.
echo [96mWhat's been configured:[0m
echo [90m  ✅ Enhanced command detection[0m
echo [90m  ✅ EMC-specific aliases (emc-serve, emc-test, etc.)[0m
echo [90m  ✅ Automatic activity logging[0m
echo [90m  ✅ Enhanced prompt with EMC/Git status[0m
echo [90m  ✅ Command completion improvements[0m
echo.
echo [93mTo activate immediately, run:[0m
echo [94m  powershell -Command ". '%INTEGRATION_SCRIPT%'"[0m
echo.
echo [93mOr restart PowerShell to load automatically.[0m
echo.
echo [96mAvailable EMC commands:[0m
echo [90m  emc-serve     - Start development server[0m
echo [90m  emc-test      - Run tests[0m
echo [90m  emc-quality   - Run code quality checks[0m
echo [90m  emc-deploy    - Deploy changes to GitHub[0m
echo [90m  emc-activity  - Log development activity[0m
echo.
pause