#!/bin/sh

set -e

# If DATABASE_URL is provided (Render PostgreSQL), map it to DB_URL for Laravel
if [ -n "$DATABASE_URL" ]; then
    export DB_URL="$DATABASE_URL"
fi

echo "==> Running database migrations..."
php artisan migrate --force

echo "==> Caching configuration..."
php artisan config:cache

echo "==> Caching routes..."
php artisan route:cache

echo "==> Caching views..."
php artisan view:cache

echo "==> Deployment tasks complete."
