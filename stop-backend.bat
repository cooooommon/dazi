@echo off
title stop dazi backend
cd /d E:\project\dazi\backend\nginx\nginx-1.24.0
nginx.exe -s stop 2>nul
taskkill /F /IM php-cgi.exe >nul 2>&1
taskkill /F /IM nginx.exe >nul 2>&1
echo Backend stopped.
pause
