#!/bin/bash

# Ensure the script stops on the first error
set -e

# Generate application key
php artisan key:generate

# Function to check if a specific table (e.g., users) is empty
check_if_table_is_empty() {
  COUNT=$(php artisan tinker --execute="DB::table('users')->count();")
  if [ "$COUNT" -eq "0" ]; then
    return 0  # Table is empty
  else
    return 1  # Table is not empty
  fi
}

# Run migrations and seed database only if the specific table is empty
if check_if_table_is_empty; then
  php artisan migrate --seed
else
  echo "Database already contains data. Skipping migration and seeding."
fi

# Install npm dependencies and build assets
npm install
npm run dev &

# Start the Laravel server
php artisan serve --host=0.0.0.0 --port=8000