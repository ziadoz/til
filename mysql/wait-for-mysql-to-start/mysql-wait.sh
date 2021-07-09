#!/usr/bin/env bash

# Load dot environment file:
export $(cat .env | xargs)

# Wait for MySQL service to start (this could be on the host or in a Docker container):
until mysql -h "$MYSQL_HOST" -P "$MYSQL_PORT" -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" -e '\q'; do
  >&2 echo "MySQL container is unavailable - sleeping"
  sleep 1
done