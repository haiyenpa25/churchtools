#!/bin/bash
echo "======================================================="
echo "   TOOL PULL CODE VA CAP NHAT DATABASE (SERVER PULL)   "
echo "======================================================="
echo ""

# Bước 1: Pull Code từ GitHub
echo "1. Tu dong Pull Code tu Github..."
git pull origin main

# Bước 2: Tự động dò tìm đường dẫn PHP trên Cpanel/DirectAdmin
echo "2. Dang tim duong dan PHP vi ban bi loi Command 'php' not found..."

PHP_BIN="php" # Mặc định

if [ -f "/opt/cpanel/ea-php82/root/usr/bin/php" ]; then
    PHP_BIN="/opt/cpanel/ea-php82/root/usr/bin/php"
elif [ -f "/opt/alt/php82/usr/bin/php" ]; then
    PHP_BIN="/opt/alt/php82/usr/bin/php"
elif [ -f "/usr/local/bin/php" ]; then
    PHP_BIN="/usr/local/bin/php"
fi

echo "Su dung PHP tai: $PHP_BIN"

# Bước 3: Chạy Migrate
echo "3. Dang chay Database Migration..."
$PHP_BIN artisan migrate --force

# Bước 4: Chạy npm run build (Nếu hosting có nodejs)
echo "4. Dang build giao dien Hien dai..."
if command -v npm &> /dev/null
then
    npm run build
else
    echo "Loi: Khong tim thay lenh npm tren Server Hosting. Ban co the bo qua hoac Upload thu muc /public/build tu Local len!"
fi

echo ""
echo "DAY CODE HOAN TAT! CHUC MUNG ANH!"
