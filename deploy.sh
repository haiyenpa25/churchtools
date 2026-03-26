#!/bin/bash
# ============================================================
#  ChurchTool - Deploy Script for Ubuntu + CyberPanel
#  Usage: bash deploy.sh
#  Run this after: git pull origin main
# ============================================================

set -e  # Exit on error

echo "🚀 [1/7] Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

echo "📦 [2/7] Installing & Building JS assets..."
npm ci
npm run build

echo "🔑 [3/7] Setting up environment..."
if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate
    echo "⚠️  .env created. Please fill in DB credentials then re-run."
    exit 1
fi

echo "🗄️  [4/7] Running database migrations..."
php artisan migrate --force

echo "🌱 [5/7] Running seeders..."
php artisan db:seed --force

echo "⚡ [6/7] Caching configs for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "🔒 [7/7] Setting permissions..."
chmod -R 755 storage bootstrap/cache
# CyberPanel / OpenLiteSpeed thường chạy dưới user nobody hoặc tên domain
# Thay 'nobody' bằng user web server của bạn nếu cần
chown -R $(whoami):$(whoami) storage bootstrap/cache

echo ""
echo "✅ Deploy complete! Site should be live."
echo ""
echo "📂 Bible data check:"
if [ -d "trinh-chieu/kinh thanh" ]; then
    COUNT=$(ls "trinh-chieu/kinh thanh/" | wc -l)
    echo "   ✓ Kinh Thánh: $COUNT files"
else
    echo "   ⚠️  Thư mục 'trinh-chieu/kinh thanh' không tồn tại!"
fi

if [ -d "trinh-chieu/giai nghia kinh thanh" ]; then
    echo "   ✓ Giải Nghĩa: $(ls 'trinh-chieu/giai nghia kinh thanh/' | wc -l) files"
else
    echo "   ⚠️  Thư mục 'trinh-chieu/giai nghia kinh thanh' không tồn tại!"
fi
