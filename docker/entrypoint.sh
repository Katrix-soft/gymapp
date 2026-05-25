#!/bin/sh
set -e

echo "Starting deployment entrypoint script..."

# Ensure storage and bootstrap cache are writable
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# Wait for database connection if host is set
if [ -n "$DB_HOST" ]; then
    echo "Waiting for database on $DB_HOST..."
    until nc -z -v -w3 "$DB_HOST" "${DB_PORT:-3306}"; do
        echo "Database connection unavailable, sleeping..."
        sleep 2
    done
    echo "Database is up!"
fi

# Run migrations
echo "Running migrations..."
php /var/www/html/artisan migrate --force

# Optimize Laravel installation
echo "Caching Laravel configuration, routes, and views..."
php /var/www/html/artisan config:cache
php /var/www/html/artisan route:cache
php /var/www/html/artisan view:cache

echo "Starting Supervisor process manager..."
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
