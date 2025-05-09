#!/bin/bash
# docker/php/docker-entrypoint.sh

set -e

# Wait for MariaDB to be ready
echo "Waiting for MariaDB..."
until mysql -h database -u root -p"${MYSQL_ROOT_PASSWORD:-!ChangeMe!}" -e "SELECT 1" &> /dev/null; do
    echo -n "."
    sleep 1
done

# Wait an additional few seconds for user setup to complete
sleep 5

# Verify the app user has proper access
echo "Verifying database credentials..."
if ! mysql -h database -u ${MARIADB_USER:-app} -p${MARIADB_PASSWORD:-app_password} -e "SELECT 1" &> /dev/null; then
    echo "User credentials not working. Trying to create the user manually..."
    mysql -h database -u root -p${MYSQL_ROOT_PASSWORD:-rootpassword} -e "CREATE USER IF NOT EXISTS '${MARIADB_USER:-app}'@'%' IDENTIFIED BY '${MARIADB_PASSWORD:-app_password}';"
    mysql -h database -u root -p${MYSQL_ROOT_PASSWORD:-rootpassword} -e "GRANT ALL PRIVILEGES ON ${MARIADB_DB:-symfony_api}.* TO '${MARIADB_USER:-app}'@'%';"
    mysql -h database -u root -p${MYSQL_ROOT_PASSWORD:-rootpassword} -e "FLUSH PRIVILEGES;"
    echo "User created successfully."
fi

echo "Database is ready!"

if [ "${APP_ENV}" = "dev" ]; then
    echo "Running in development mode"
    # Enable xdebug for development
    if [ -f /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini ]; then
        echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
        echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
    fi
fi

# First arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
    set -- php-fpm "$@"
fi

echo "Testing Composer installation..."
composer --version || { echo "Composer not found or not working properly"; exit 1; }
echo "Running composer..."
composer install

# Run migrations if the database exists and schema is not up to date
if [ -f bin/console ]; then
    echo "Running database migrations..."
    bin/console doctrine:migrations:migrate --no-interaction || true
fi

exec "$@"