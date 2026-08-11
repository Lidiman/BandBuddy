#!/bin/bash
set -e

echo "=== Waiting for database and running migrations ==="
for i in $(seq 1 60); do
    if php artisan migrate --force --no-interaction; then
        migrations_ok=1
        break
    fi
    echo "  DB not ready, retrying in 2s... ($i/60)"
    sleep 2
done
if [ -z "${migrations_ok:-}" ]; then
    echo "Database not reachable after retries. Exiting." >&2
    exit 1
fi

echo "=== Storage link ==="
php artisan storage:link --force --no-interaction || true

echo "=== Caching config and routes ==="
php artisan config:cache --no-interaction || true
php artisan route:cache --no-interaction || true

echo "=== Starting: $@ ==="
exec "$@"