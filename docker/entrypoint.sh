#!/bin/sh

# Exit on error
set -e

# Optimize Laravel
php artisan optimize

# Run migrations (only in production or as needed)
# php artisan migrate --force

# Start PHP-FPM in the background
php-fpm -D

# Start Nginx in the foreground
nginx -g "daemon off;"
