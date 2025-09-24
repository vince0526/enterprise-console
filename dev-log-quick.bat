@echo off
REM Enterprise Management Console - Simple Development Logger

set COMPUTER_NAME=%COMPUTERNAME%
set USER_NAME=%USERNAME%

REM Create log directory
if not exist "logs\computer-profiles" mkdir "logs\computer-profiles"

REM Get current date/time (simple format)
for /f "tokens=1-3 delims=/" %%a in ("%date%") do set mydate=%%c-%%a-%%b
for /f "tokens=1-2 delims=:" %%a in ("%time%") do set mytime=%%a:%%b
set TIMESTAMP=%mydate% %mytime%

REM Set activity and description from parameters
set ACTIVITY=%1
set DESCRIPTION=%2

if "%ACTIVITY%"=="" set ACTIVITY=OTHER
if "%DESCRIPTION%"=="" set DESCRIPTION=Development activity

REM Create log entry
set LOG_ENTRY=[%TIMESTAMP%] [%COMPUTER_NAME%] [%USER_NAME%] %ACTIVITY% - %DESCRIPTION%

REM Log to file
echo %LOG_ENTRY% >> logs\computer-profiles\%COMPUTER_NAME%-activity.log

echo Logged: %ACTIVITY% on %COMPUTER_NAME%
echo Entry: %LOG_ENTRY%