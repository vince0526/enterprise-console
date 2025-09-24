@echo off
REM Enterprise Management Console - Development Activity Logger (Batch Version)
REM Simple logging without PowerShell execution policy requirements

setlocal EnableDelayedExpansion

REM Get computer and user information
set COMPUTER_NAME=%COMPUTERNAME%
set USER_NAME=%USERNAME%
set CURRENT_DATE=%date% %time%

REM Create log directory if it doesn't exist
if not exist "logs\computer-profiles" mkdir "logs\computer-profiles"

REM Set file paths
set PROFILE_PATH=logs\computer-profiles\%COMPUTER_NAME%.md
set LOG_PATH=logs\computer-profiles\%COMPUTER_NAME%-activity.log
set SUMMARY_PATH=logs\activity-summary.md

echo.
echo 🔧 Enterprise Management Console - Development Logger
echo Computer: %COMPUTER_NAME% ^| User: %USER_NAME%
echo.

if "%1"=="--log" goto show_log
if "%1"=="--summary" goto show_summary

REM Prompt for activity if not provided
if "%1"=="" (
    echo Select activity type:
    echo 1. Feature Development
    echo 2. Bug Fix
    echo 3. Code Refactoring  
    echo 4. Testing
    echo 5. Deployment/Sync
    echo 6. Environment Setup
    echo 7. Documentation
    echo 8. Other
    echo.
    set /p choice="Enter choice (1-8): "
    
    if "!choice!"=="1" set ACTIVITY=FEATURE
    if "!choice!"=="2" set ACTIVITY=BUGFIX
    if "!choice!"=="3" set ACTIVITY=REFACTOR
    if "!choice!"=="4" set ACTIVITY=TESTING
    if "!choice!"=="5" set DEPLOYMENT=DEPLOYMENT
    if "!choice!"=="6" set ACTIVITY=SETUP
    if "!choice!"=="7" set ACTIVITY=DOCUMENTATION
    if "!choice!"=="8" set ACTIVITY=OTHER
    
    if "!ACTIVITY!"=="" set ACTIVITY=OTHER
    
    set /p DESCRIPTION="Enter description: "
) else (
    set ACTIVITY=%1
    set DESCRIPTION=%2
)

REM Create log entry
set LOG_ENTRY=[%CURRENT_DATE%] [%COMPUTER_NAME%] [%USER_NAME%] %ACTIVITY%
if not "%DESCRIPTION%"=="" set LOG_ENTRY=%LOG_ENTRY% - %DESCRIPTION%

REM Add to computer-specific log
echo %LOG_ENTRY% >> "%LOG_PATH%"

REM Add to consolidated log
echo %LOG_ENTRY% >> "%SUMMARY_PATH%"

echo ✅ Logged: %ACTIVITY%
echo.

:show_log
if exist "%LOG_PATH%" (
    echo 📋 Recent Activity on %COMPUTER_NAME%
    echo ═══════════════════════════════════════════════
    REM Show last 10 lines (simple version)
    type "%LOG_PATH%"
) else (
    echo No activity log found for %COMPUTER_NAME%
)
goto end

:show_summary
echo 📊 Development Summary Across All Computers  
echo ═══════════════════════════════════════════════
for %%f in (logs\computer-profiles\*.md) do (
    set comp_name=%%~nf
    echo.
    echo 🖥️  !comp_name!
    if exist "logs\computer-profiles\!comp_name!-activity.log" (
        for /f %%i in ('type "logs\computer-profiles\!comp_name!-activity.log" ^| find /c /v ""') do set count=%%i
        echo    Activities: !count!
    )
)
goto end

:end
echo.
echo 💡 Usage Examples:
echo dev-log-simple.bat FEATURE "Added new database module"
echo dev-log-simple.bat --log
echo dev-log-simple.bat --summary
echo.
pause