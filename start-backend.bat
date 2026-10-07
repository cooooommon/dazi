@echo off
title dazi backend
rem Stop leftovers, then relaunch everything HIDDEN via VBS (no console windows).
taskkill /F /IM php-cgi.exe >nul 2>&1
taskkill /F /IM nginx.exe >nul 2>&1
wscript.exe "E:\project\dazi\backend\start-hidden.vbs"
echo Backend starting at http://127.0.0.1:8080 (runs hidden; stop with stop-backend.bat)
