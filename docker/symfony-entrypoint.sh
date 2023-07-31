#!/bin/sh
set -e

# If dependencies are missing, install them
# (should happen only in DEV environnement)
if [ ! -f /app/vendor/autoload.php ]; then
  symfony server:ca:install
  composer install --no-interaction --optimize-autoloader
fi

# Run migrations
php bin/console app:sync-migrate

echo "$@"
exec "$@"
