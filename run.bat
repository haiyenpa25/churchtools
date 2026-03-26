@echo off
title ChurchTool Development Server
color 0B

cd /d "%~dp0"

echo ==================================================
echo       ChurchTool - Khoi dong Server
echo ==================================================
echo.

rem 1. Tu dong them XAMPP PHP vao PATH
if exist "C:\xampp\php\php.exe" (
    echo [OK] Phat hien XAMPP PHP!
    set "PATH=C:\xampp\php;%PATH%"
) else (
    echo [Canh bao] Khong tim thay C:\xampp\php\php.exe.
)

rem 2. Kiem tra thu vien Frontend
if not exist "node_modules\" (
    echo [SYS] Chua co node_modules, dang tu dong chay npm install...
    call npm install
)

rem 3. Xoa cache Laravel
echo [SYS] Dang xoa cache Laravel...
php artisan optimize:clear

echo.
echo ==================================================
echo [SYS] Phat tan tien trinh (Server, Queue, Vite) ra cac cua so rieng...
echo ==================================================
echo.

rem 4. Chay cac tien trinh doc lap de tranh windows crash Pail (pcntl)
start "Laravel Web Server" php artisan serve
start "Laravel Queue Worker" php artisan queue:listen --tries=1 --timeout=0
start "Vite Frontend Server" npm run dev

echo [OK] 3 Cua so da duoc bat len thanh cong! Ban co the tat cua so den nay.
pause
