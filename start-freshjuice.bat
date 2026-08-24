@echo off
title FreshJuice Launcher
setlocal

rem ================================================================
rem  FreshJuice Factory Management System - one-click launcher
rem  1. If the site is not responding, starts XAMPP (Apache + MySQL)
rem  2. Waits until the app answers (max ~60 seconds)
rem  3. Opens the system's default web browser on the login page
rem ================================================================

set "URL=http://localhost/freshjuice/public/"
set "XAMPP_DIR=C:\xampp"

rem -- Already running? Skip straight to the browser. --
powershell -NoProfile -Command "try { $null = Invoke-WebRequest -Uri '%URL%' -UseBasicParsing -TimeoutSec 3 } catch { exit 1 }" >nul 2>&1
if not errorlevel 1 goto :open

echo Starting XAMPP (Apache + MySQL)...
start "" /MIN "%XAMPP_DIR%\xampp_start.exe"

set /a TRIES=0
:waitloop
timeout /t 2 /nobreak >nul
powershell -NoProfile -Command "try { $null = Invoke-WebRequest -Uri '%URL%' -UseBasicParsing -TimeoutSec 3 } catch { exit 1 }" >nul 2>&1
if not errorlevel 1 goto :open
set /a TRIES+=1
if %TRIES% LSS 30 goto :waitloop

echo.
echo XAMPP did not respond within 60 seconds.
echo Opening the XAMPP Control Panel so you can check Apache/MySQL...
start "" "%XAMPP_DIR%\xampp-control.exe"

:open
echo FreshJuice is ready. Opening your browser...
start "" "%URL%"
endlocal
exit /b 0
