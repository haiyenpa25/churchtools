#!/bin/bash
# ============================================================
#  ChurchTool - GitHub Webhook Auto Deploy Script
#  Fast, non-blocking execution for CI/CD Pipeline
# ============================================================

echo "🚀 [1/3] Navigating to repository and pulling latest code..."
cd "$(dirname "$0")"

# Xóa các thay đổi rác trên server để tránh xung đột Merge
git reset --hard HEAD
git pull origin main

echo "🗄️  [2/3] Running database migrations..."
php artisan migrate --force

echo "⚡ [3/3] Clearing & Caching for production..."
php artisan optimize:clear

echo "✅ Webhook Deploy complete!"
