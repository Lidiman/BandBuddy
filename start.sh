#!/bin/bash

echo "=== Activating Python venv ==="
source /app/.venv/bin/activate

echo "=== Creating .env file ==="
cat > /app/.env <<'ENVEOF'
APP_NAME=BandBuddy
APP_ENV=production
APP_KEY=base64:rWn7AjR0MbokquITYsGMZSeEdqiYTcsi4WJoCYiojPo=
APP_DEBUG=false
APP_URL=https://bandbuddy-production.up.railway.app
LOG_CHANNEL=stack
LOG_LEVEL=info

DB_CONNECTION=mysql
DB_HOST=mysql.railway.internal
DB_PORT=3306
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=EMiJAFEPEuIBeaCQncQdfovKmSPthMFJ

SESSION_DRIVER=file
SESSION_LIFETIME=120

CACHE_STORE=file
BROADCAST_DRIVER=log
QUEUE_CONNECTION=database

PYTHON_BIN=/app/.venv/bin/python3
ENVEOF

echo "=== Generating app key (if missing) ==="
php artisan key:generate --force 2>/dev/null || true

echo "=== Waiting for database ==="
for i in {1..30}; do
    php artisan migrate --force --no-interaction && break
    echo "  DB not ready, retrying in 2s... ($i/30)"
    sleep 2
done

php artisan storage:link --force 2>/dev/null || true

echo "=== Clearing config and route caches ==="
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true

echo "=== Starting PHP server on port $PORT ==="
php artisan serve --host=0.0.0.0 --port=$PORT
