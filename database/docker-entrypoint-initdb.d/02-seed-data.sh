#!/bin/bash
# Conditional seed data loader
# Only loads seed data if SEED_DATA environment variable is set to "true"

if [ "$SEED_DATA" = "true" ]; then
    echo "SEED_DATA=true detected. Loading seed data..."
    mysql -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE" < /docker-entrypoint-initdb.d/02-seed-data.sql
    echo "Seed data loaded successfully."
else
    echo "SEED_DATA not set or false. Skipping seed data."
fi
