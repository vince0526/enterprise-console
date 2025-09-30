# Enterprise Management Console

![Development Status](https://img.shields.io/badge/Status-Active%20Development-brightgreen)
![Platform](https://img.shields.io/badge/Platform-Multi--Computer-blue)
![Tracking](https://img.shields.io/badge/Tracking-Enabled-success)

A comprehensive Laravel-based enterprise management system with advanced database management capabilities.

## 🚀 Quick Start

### Option 1: Automated Setup (Recommended)

**Windows:**

```cmd
git clone https://github.com/vince0526/enterprise-console.git
cd enterprise-console
setup.bat
```

**Linux/Mac:**

```bash
git clone https://github.com/vince0526/enterprise-console.git
cd enterprise-console
chmod +x setup.sh
./setup.sh
```

### Option 2: Manual Setup

See [SETUP_INSTRUCTIONS.md](SETUP_INSTRUCTIONS.md) for detailed manual setup instructions.

## ✨ Features

### 🗄️ Database Management Module
- **Database Backup**: Complete backup and restore functionality
- **Database Connections**: Connection management and monitoring
- **Database Performance**: Performance metrics and optimization tools
- **Database Query Tool**: Interactive SQL query interface
- **Database Replication**: Replication setup and monitoring

### 🔧 Core Features
- Modern Laravel-based architecture
- Responsive dashboard interface
- User authentication and authorization
- Development auto-login for testing
- Code quality assurance (Pint, PHPStan)
- Comprehensive error handling

## 🌐 Access URLs

After running `php artisan serve`:

- Note: The Enterprise Console now defaults to the Core Databases module.
- **Default Entry (Core Databases)**: <http://localhost:8000> → redirects to <http://localhost:8000/emc/core>
- **EMC Index**: <http://localhost:8000/emc> → redirects to <http://localhost:8000/emc/core>
- **Database Module**: <http://localhost:8000/emc/db>
- **User Profile**: <http://localhost:8000/profile>

## 📋 Requirements

- PHP 8.1+
- Composer
- MySQL/MariaDB or SQLite
### MySQL .env Quick Start

Set your local `.env` for MySQL (Laragon/XAMPP/WAMP):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=enterprise_console
DB_USERNAME=root
DB_PASSWORD=
```

Then bootstrap the schema and demo data:

```bash
php artisan optimize:clear
php artisan migrate --force
php artisan db:seed --class="Database\\Seeders\\CoreDatabaseSeeder" --force
```

- Node.js & npm (optional, for frontend assets)

## 🔧 Development

### Code Quality
```bash
# Format code
./vendor/bin/pint

# Static analysis
./vendor/bin/phpstan analyse

# Run tests
php artisan test
```

### Development Features
- Set `DEV_AUTO_LOGIN=true` in `.env` for automatic authentication
- Access `/dev-env-flag` to check development settings
- View `/dev-users` to see available test users

## 📚 Documentation

- [Complete Setup Instructions](SETUP_INSTRUCTIONS.md)
- [Laravel Documentation](https://laravel.com/docs)

## 🤝 Support

If you encounter issues:
1. Check the [troubleshooting section](SETUP_INSTRUCTIONS.md#troubleshooting) in setup instructions
2. Verify all requirements are met
3. Check Laravel logs in `storage/logs/`

## 📈 Version Info

- **Current Version**: Production-ready EMC with Database Management
- **Last Updated**: September 24, 2025
- **Commit Hash**: `220942eede37102a1ed67bc78f5adf1a5e54cc74`

---

**Repository**: <https://github.com/vince0526/enterprise-console>  
**License**: MIT  
**Maintainer**: vince0526

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Project Environment Setup

For the Enterprise Console specific development workstation provisioning guide (PHP 8.3, Laravel 12, Node/Vite, QA tooling) see: [docs/environment-setup.md](docs/environment-setup.md). A PowerShell bootstrap script is available in `scripts/bootstrap-dev-environment.ps1` and a DOCX version can be generated with `python scripts/env_setup_md_to_docx.py` (requires `python-docx`).

### Quick Commands

Common tasks are available via Makefile (macOS/Linux) or PowerShell script (Windows).

Makefile examples:

```
make install      # composer install + npm install
make dev          # concurrent dev (serve, queue, logs, vite)
make qa-full      # pint + phpstan + tests
make docs         # regenerate DOCX docs
```

PowerShell (Windows):

```
./scripts/dev-tasks.ps1 install
./scripts/dev-tasks.ps1 dev
./scripts/dev-tasks.ps1 qa-full
./scripts/dev-tasks.ps1 docs
```

### Temporary Dev Auto-Login

To quickly preview the application UI without the login flow you can enable a development-only automatic login middleware.

1. Add to your local `.env` (never commit this enabled):
	```env
	DEV_AUTO_LOGIN=true
	DEV_AUTO_LOGIN_USER_ID=1   # adjust to an existing user id
	```
2. Clear config & cache (if previously cached):
	```bash
	php artisan optimize:clear
	```
3. Visit any protected route (e.g. /dashboard); you should be auto-authenticated as that user.

Safety:
- Middleware is skipped automatically in `production` environment.
- Disable by removing the vars or setting `DEV_AUTO_LOGIN=false`.
- Use only on local/dev; never push an enabled flag to shared environments.


### DOCX Automation

Workflows regenerate DOCX outputs on markdown changes:

-   `regenerate-env-setup-docx.yml`
-   `regenerate-docx.yml`
-   `regenerate-all-docx.yml` (aggregate)

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

-   **[Vehikl](https://vehikl.com)**
-   **[Tighten Co.](https://tighten.co)**
-   **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
-   **[64 Robots](https://64robots.com)**
-   **[Curotec](https://www.curotec.com/services/technologies/laravel)**
-   **[DevSquad](https://devsquad.com/hire-laravel-developers)**
-   **[Redberry](https://redberry.international/laravel-development)**
-   **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
