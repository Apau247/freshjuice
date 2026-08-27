@echo off
title Promotora Foods - Propine Fruity
setlocal

rem ================================================================
rem  Propine Fruity Factory Management System - one-click launcher
rem  1. If the site is not responding, starts XAMPP (Apache + MySQL)
rem  2. Waits until the app answers (max ~60 seconds)
rem  3. Opens the system's default web browser fullscreen
rem  4. Creates a desktop shortcut with the PF pineapple icon
rem ================================================================

set "URL=http://localhost/freshjuice/public/"
set "XAMPP_DIR=C:\xampp"
set "APP_DIR=%~dp0"
set "ICO=%APP_DIR%pf-logo.ico"
set "SHORTCUT=%USERPROFILE%\Desktop\Promotora Foods.lnk"

rem -- Create desktop shortcut with icon (first run or if missing) --
if not exist "%SHORTCUT%" (
    powershell -NoProfile -ExecutionPolicy Bypass -Command ^
        "$s=(New-Object -COM WScript.Shell).CreateShortcut('%SHORTCUT%'); ^
         $s.TargetPath='%~f0'; ^
         $s.WorkingDirectory='%APP_DIR%'; ^
         $s.IconLocation='%ICO%,0'; ^
         $s.Description='Promotora Foods - Propine Fruity Factory Management'; ^
         $s.Save()"
    echo Desktop shortcut created with PF icon.
)

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
echo Promotora Foods is ready. Opening browser fullscreen...
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0open-in-browser.ps1" -Url "%URL%"
endlocal
exit /b 0
