#!/bin/bash
set -e

echo "Deploying Getembe News application to production..."

# 1. Enter maintenance mode
echo "Entering maintenance mode..."
php artisan down || true

# 2. Git pull latest main branch code
echo "Pulling latest code from GitHub..."
git pull origin main

# 3. Install composer dependencies (optimized, no-dev)
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# 4. Install npm packages and build production assets
echo "Building assets with Vite..."
npm ci
npm run build

# 5. Run database migrations and setup storage symlink
echo "Running database migrations..."
php artisan migrate --force
echo "Ensuring storage symlink exists..."
php artisan storage:link || true


# 6. Optimize and cache configurations, routes, and views
echo "Optimizing application cache..."
php artisan optimize
php artisan view:cache
php artisan event:cache

# 7. Bring application out of maintenance mode
echo "Exiting maintenance mode..."
php artisan up

echo "Deployment complete! Website is live and optimized."
