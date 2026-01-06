@echo off
echo ==========================================
echo   Starting Click P2P Development Environment
echo ==========================================
echo.

echo [1/2] Launching Queue Worker (for emails & notifications)...
echo This will open in a new window. Do not close it!
start "Laravel Queue Worker" "D:\laragon\bin\php\php-8.3.26-Win32-vs16-x64\php.exe" artisan queue:work

echo.
echo [2/2] Starting Web Server...
echo You can access the site at: http://127.0.0.1:8000
echo.
"D:\laragon\bin\php\php-8.3.26-Win32-vs16-x64\php.exe" artisan serve
