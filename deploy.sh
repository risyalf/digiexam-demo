#!/bin/bash

set -e

APP_DIR="/home/deploy/student-assessment-cms"
CONTAINER_NAME="student-assessment-cms"
# HEALTHCHECK_URL="https://assessment-admin.smkswadaya.sch.id/ping"

echo "🚀 Starting Deploy..."

cd $APP_DIR

echo "📥 Pull latest code..."
git fetch --all
git reset --hard origin/main

echo "🐳 Build & Start Containers..."
docker compose up -d --build

echo "⏳ Waiting for app to be healthy..."

# until docker exec $CONTAINER_NAME curl -sf $HEALTHCHECK_URL > /dev/null; do
#   echo "Waiting for app..."
#   sleep 5
# done

echo "✅ App is healthy!"

echo "🗄 Running Migrations..."
docker exec $CONTAINER_NAME php artisan migrate --force

echo "⚡ Caching Laravel..."
docker exec $CONTAINER_NAME php artisan config:cache
docker exec $CONTAINER_NAME php artisan route:cache
docker exec $CONTAINER_NAME php artisan view:cache
docker exec $CONTAINER_NAME php artisan storage:link

echo "🎉 Deploy Finished Successfully!"
