#!/bin/sh
# Initializes an empty data directory, then runs the server in the foreground.
#   MARIADB_ROOT_PASSWORD  root password (required on first start), valid from any host
#   MARIADB_DATABASE       database to create on first start (optional)
set -eu

DATADIR=/var/lib/mysql

if [ ! -d "$DATADIR/mysql" ]; then
    : "${MARIADB_ROOT_PASSWORD:?MARIADB_ROOT_PASSWORD must be set to initialize the database}"

    echo "Initializing MariaDB data directory in $DATADIR"
    mariadb-install-db --datadir="$DATADIR" --skip-test-db \
        --auth-root-authentication-method=normal > /dev/null

    # Bootstrap mode reads SQL from stdin without opening a socket or port
    password=$(printf '%s' "$MARIADB_ROOT_PASSWORD" | sed "s/\\\\/\\\\\\\\/g; s/'/''/g")
    {
        # Drop the passwordless root@127.0.0.1, root@::1 and root@<hostname> accounts
        echo "DELETE FROM mysql.global_priv WHERE User = 'root' AND Host <> 'localhost';"
        echo "DELETE FROM mysql.proxies_priv WHERE User = 'root' AND Host <> 'localhost';"
        echo "FLUSH PRIVILEGES;"
        echo "ALTER USER 'root'@'localhost' IDENTIFIED BY '$password';"
        echo "CREATE USER 'root'@'%' IDENTIFIED BY '$password';"
        echo "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;"
        if [ -n "${MARIADB_DATABASE:-}" ]; then
            echo "CREATE DATABASE IF NOT EXISTS \`$MARIADB_DATABASE\`;"
        fi
        echo "FLUSH PRIVILEGES;"
    } | mariadbd --datadir="$DATADIR" --bootstrap
fi

exec mariadbd --datadir="$DATADIR" "$@"
