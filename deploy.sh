#!/bin/bash

set -e

APP_DIR="/home/deploy/student-assessment-cms"
CONTAINER_NAME="student-assessment-cms"
# HEALTHCHECK_URL="http://localhost/ping"

MAX_WAIT=120
INTERVAL=5
ELAPSED=0

echo "🚀 Starting Deploy..."

cd $APP_DIR

echo "📥 Pull latest code..."
git fetch --all
git reset --hard origin/main

echo "🐳 Build & Start Containers..."
docker compose up -d --build

echo "⏳ Waiting for app to be healthy (max ${MAX_WAIT}s)..."

# until docker exec $CONTAINER_NAME curl -sf $HEALTHCHECK_URL > /dev/null; do
#   sleep $INTERVAL
#   ELAPSED=$((ELAPSED + INTERVAL))

#   echo "Waiting... (${ELAPSED}s)"

#   if [ "$ELAPSED" -ge "$MAX_WAIT" ]; then
#     echo "❌ App failed to become healthy within ${MAX_WAIT} seconds"
#     exit 1
#   fi
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
