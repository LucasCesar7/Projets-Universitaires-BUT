#!/bin/sh
set -eu

mysql --protocol=socket \
  --user="root" \
  --password="${MYSQL_ROOT_PASSWORD}" \
  --database="${MYSQL_DATABASE}" \
  < /docker-entrypoint-schema/schema.sql
