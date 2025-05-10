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

echo "Setting up credentials ..."
mysql -h database -u root -p${MYSQL_ROOT_PASSWORD:-rootpassword} -e "CREATE USER IF NOT EXISTS '${MARIADB_USER:-app}'@'%' IDENTIFIED BY '${MARIADB_PASSWORD:-app_password}';"
mysql -h database -u root -p${MYSQL_ROOT_PASSWORD:-rootpassword} -e "GRANT ALL PRIVILEGES ON ${MARIADB_DB:-symfony_api}.* TO '${MARIADB_USER:-app}'@'%';"
mysql -h database -u root -p${MYSQL_ROOT_PASSWORD:-rootpassword} -e "GRANT ALL PRIVILEGES ON ${MARIADB_DB_TEST:-symfony_api_test}.* TO '${MARIADB_USER:-app}'@'%';"
mysql -h database -u root -p${MYSQL_ROOT_PASSWORD:-rootpassword} -e "FLUSH PRIVILEGES;"
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
    table_count=$(mysql -h database -u root -p"${MYSQL_ROOT_PASSWORD:-!ChangeMe!}" -e "SELECT COUNT(*) FROM ${MARIADB_DB:-symfony_api}.note" --skip-column-names)
    if [ "$table_count" -eq 0 ]; then
      bin/console doctrine:fixtures:load -n
    fi
fi

git config --global --add safe.directory /var/www/html



exec "$@"