# Contributing

Thanks for your interest in contributing! This project uses Laravel 12 (PHP 8.3) with Vite/Tailwind.

## Quick Start

- Windows one-shot setup:
  - See README “Dev Quick Start (Windows)” for `setup-windows.ps1` and `dev-up.ps1`.
- Alternative (manual):
  - `composer install`, ensure `.env`, `php artisan key:generate`, create `database/database.sqlite`, `php artisan migrate`, `npm ci && npm run build`.

## Code Quality

- Format: Prettier for web assets, Pint for PHP.
- Static analysis: PHPStan.
- Tests: `php artisan test`.

## Commits and PRs

- Use feature branches and descriptive messages.
- Ensure CI is green: Pint, PHPStan, tests, and assets build.
- Include screenshots for UI changes when possible.

## Security

- Do not include secrets in commits or PR descriptions.
- See SECURITY.md for vulnerability reporting.
