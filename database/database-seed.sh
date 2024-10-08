#!/bin/bash

# Configuration
MAX_RETRIES=20
SLEEP_INTERVAL=1
INITIAL_SQL_SCRIPT=/sql-scripts/01-database-init.sql
SEED_SQL_SCRIPTS_PATH=/sql-scripts/seed

# Wait for MySQL Server to be ready
echo "Waiting for MySQL Server to be ready..."

for ((i=1; i<=MAX_RETRIES; i++)); do
    mysqladmin ping -h"$MYSQL_HOST" -u"$MYSQL_ROOT" -p"$MYSQL_ROOT_PASSWORD" -P"$MYSQL_PORT" > /dev/null 2>&1
    if [ $? -eq 0 ]; then
        echo "MySQL Server is ready."
        break
    else
        echo "MySQL Server is not ready yet... retrying in $SLEEP_INTERVAL seconds."
        sleep $SLEEP_INTERVAL
    fi
done

if [ $i -gt $MAX_RETRIES ]; then
    echo "MySQL Server did not become ready in time. Exiting."
    exit 1
fi

echo "Setting time zone to : $TMZN"
mysql -h"$MYSQL_HOST" -u"$MYSQL_ROOT" -p"$MYSQL_ROOT_PASSWORD" -P"$MYSQL_PORT" -e "SET GLOBAL time_zone = '$TMZN';"

echo "Creating database '$DATABASE_NAME'"
mysql -h"$MYSQL_HOST" -u"$MYSQL_ROOT" -p"$MYSQL_ROOT_PASSWORD" -P"$MYSQL_PORT" -e "
CREATE DATABASE $DATABASE_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run the initial SQL script to create the database and tables
echo "Running the initial SQL script..."
mysql -h"$MYSQL_HOST" -u"$MYSQL_ROOT" -p"$MYSQL_ROOT_PASSWORD" -P"$MYSQL_PORT" "$DATABASE_NAME" < "$INITIAL_SQL_SCRIPT"
if [ $? -eq 0 ]; then
    echo "Initial SQL script executed successfully."
else
    echo "Failed to execute initial SQL script. Path: $INITIAL_SQL_SCRIPT"
    exit 1
fi

# Create a new MySQL user and grant privileges
echo "Creating a new MySQL user and granting privileges..."
mysql -h"$MYSQL_HOST" -u"$MYSQL_ROOT" -p"$MYSQL_ROOT_PASSWORD" -P"$MYSQL_PORT" "$DATABASE_NAME" -e "
CREATE USER '$MYSQL_USER'@'%' IDENTIFIED BY '$USER_PASSWORD';
GRANT INSERT, UPDATE, DELETE, SELECT ON $DATABASE_NAME.* TO '$MYSQL_USER'@'%';"
if [ $? -eq 0 ]; then
    echo "User created and privileges granted successfully."
else
    echo "Failed to create user or grant privileges."
    exit 1
fi

# Seed the database with initial data
echo "Seeding the database with initial data..."
for sql_file in "$SEED_SQL_SCRIPTS_PATH"/*.sql; do
    # Check if there are any .sql files
    if [ -e "$sql_file" ]; then
        # Print the name of the file
        echo "Processing file: $sql_file"

        # Run a command on the file (e.g., execute the SQL script)
        # Replace the following line with the command you want to run
        mysql -h"$MYSQL_HOST" -u"$MYSQL_ROOT" -p"$MYSQL_ROOT_PASSWORD" -P"$MYSQL_PORT" "$DATABASE_NAME" < "$sql_file"
        if [ $? -eq 0 ]; then
            echo "Successfully processed $sql_file"
        else
            echo "Error processing $sql_file"
        fi
    else
        echo "No .sql files found in the directory."
    fi
done
