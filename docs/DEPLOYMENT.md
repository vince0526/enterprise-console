# Deployment Guide

This guide outlines environment setup and the sequence for building, optimizing, and deploying the Enterprise Management Console.

## Environments

- PHP 8.2+ (8.3 recommended)
- Web server: Nginx or Apache (or `php-fpm` behind reverse proxy)
- Database: MySQL/MariaDB or SQLite
- Node 20.19+ or 22.12+ (for building assets)

## .env Checklist

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your.domain`
- Database credentials (`DB_*`)
- Session/cache drivers (`SESSION_DRIVER`, `CACHE_DRIVER`)
- Queue connection if used

## First-time Provisioning

1. Install dependencies
   - `composer install --no-dev --prefer-dist --optimize-autoloader`
   - `npm ci` (or `npm install`)
2. Generate key: `php artisan key:generate --ansi`
3. Run migrations and seed (if needed): `php artisan migrate --force`

## Build Frontend Assets

- `npm run build`
  - Outputs versioned assets in `public/build`

## Optimize Application

Run these in CI/CD or deployment step:

- `php artisan optimize:clear`
- `php artisan config:cache`
- `php artisan route:cache`
- `php artisan view:cache`

If you use queues and schedules, ensure services are configured:

- `php artisan queue:work --daemon` (as a service)
- `* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1`

## Zero-downtime Tips

- Use atomic deploys (symlink current -> new release)
- Run `php artisan down --render="errors::503"` during maintenance windows, then `php artisan up`
- Warm caches on new release before swapping symlink

## Smoke Test

- Hit `/` and `/emc/core` and verify assets load and the Saved Views list responds
- Run `php artisan test --filter=Health` if tests are available on the target

## Rollback

- Keep the previous `public/build` and a DB backup
- Restore symlink to previous release; run `php artisan optimize:clear`
