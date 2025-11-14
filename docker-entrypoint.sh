#!/usr/bin/env sh
set -e

mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/app/public
mkdir -p bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

if [ ! -d "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction
fi

if ! grep -q '^APP_KEY=' .env || grep -q '^APP_KEY=$' .env; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

if [ -n "$DB_HOST" ]; then
    echo "Running database migrations..."
    php artisan migrate --seed --force || true
fi

exec "$@"
