#!/bin/bash
set -e

APP_DIR="/home/deploy/student-assessment-cms"

echo "🚀🚀 Starting Deploy..."
cd $APP_DIR

echo "📥 Pull latest code..."
git fetch --all
git reset --hard origin/main

echo "📦 Install PHP dependencies..."
composer install --no-dev --optimize-autoloader

echo "🏗 Build frontend assets..."
npm install
npm run build

echo "🗄 Running Migrations..."
php artisan migrate --force

echo "⚡ Caching Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link

echo "🔒 Fix permissions..."
sudo chown -R deploy:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

echo "🔄 Restart PHP-FPM & Queue..."
sudo systemctl restart php8.4-fpm
sudo systemctl restart laravel-queue

echo "🎉 Deploy Finished Successfully!"
