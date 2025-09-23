# Enterprise Management Console - Setup Instructions for Other Computers

## Current Version Information
- **Repository**: https://github.com/vince0526/enterprise-console
- **Main Branch Commit**: `220942eede37102a1ed67bc78f5adf1a5e54cc74`
- **Last Updated**: September 24, 2025
- **Status**: Production-ready Enterprise Management Console with complete Database Management module

## Prerequisites

### Required Software
1. **PHP** (version 8.1 or higher)
2. **Composer** (PHP dependency manager)
3. **Git** (for cloning the repository)
4. **Node.js** and **npm** (for frontend assets)
5. **MySQL/MariaDB** or **SQLite** (for database)

### Optional (for development)
- **Laragon**, **XAMPP**, or **MAMP** (local development environment)
- **VS Code** (recommended IDE)

## Step-by-Step Setup Instructions

### 1. Clone the Repository
```bash
# Navigate to your web server directory (e.g., C:\laragon\www\ or /var/www/)
cd C:\laragon\www\

# Clone the repository
git clone https://github.com/vince0526/enterprise-console.git

# Navigate to the project directory
cd enterprise-console

# Verify you're on the main branch with the latest code
git status
git log --oneline -3
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies (if package.json exists)
npm install

# Build frontend assets (if needed)
npm run build
```

### 3. Environment Configuration
```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Environment Variables
Edit the `.env` file and configure:

```env
# Application
APP_NAME="Enterprise Management Console"
APP_ENV=local
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=enterprise_console
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Development Features (optional)
DEV_AUTO_LOGIN=true
DEV_AUTO_LOGIN_USER_ID=1
```

### 5. Database Setup
```bash
# Create database (if using MySQL)
# Run this in your MySQL client:
# CREATE DATABASE enterprise_console;

# Or use SQLite for simpler setup
# Change DB_CONNECTION=sqlite in .env
# touch database/database.sqlite

# Run database migrations
php artisan migrate

# Seed the database (if seeders exist)
php artisan db:seed
```

### 6. Set Up File Permissions (Linux/Mac)
```bash
# Make storage and cache directories writable
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### 7. Run the Application
```bash
# Start the development server
php artisan serve

# Application will be available at: http://127.0.0.1:8000
```

## Verification Checklist

After setup, verify these features work:

- [ ] **Home Page**: Redirects to EMC Dashboard
- [ ] **EMC Dashboard**: Loads at `/emc` or `/emc/db`
- [ ] **Database Management**: All 5 submodules accessible:
  - [ ] Database Backup (`/emc/db` → Backup tab)
  - [ ] Database Connections (`/emc/db` → Connections tab)
  - [ ] Database Performance (`/emc/db` → Performance tab)
  - [ ] Database Query Tool (`/emc/db` → Query tab)
  - [ ] Database Replication (`/emc/db` → Replication tab)
- [ ] **Navigation**: Sidebar navigation works
- [ ] **Dev Features**: Auto-login works (if enabled)

## Key URLs to Test

1. **Main EMC**: `http://localhost:8000/emc`
2. **Database Module**: `http://localhost:8000/emc/db`
3. **Dashboard**: `http://localhost:8000/dashboard`
4. **Profile**: `http://localhost:8000/profile`
5. **Dev Flag Check**: `http://localhost:8000/dev-env-flag` (development only)

## Troubleshooting

### Common Issues

**1. Composer install fails**
```bash
# Clear composer cache
composer clear-cache
composer install --no-cache
```

**2. Permission errors (Linux/Mac)**
```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage/
sudo chmod -R 775 storage/
```

**3. Database connection errors**
- Verify database credentials in `.env`
- Ensure database server is running
- Check database exists

**4. 500 Internal Server Error**
```bash
# Check application logs
tail -f storage/logs/laravel.log

# Clear application cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## Code Quality Verification

This version passes all quality checks:
```bash
# Run code formatting check
./vendor/bin/pint

# Run static analysis
./vendor/bin/phpstan analyse

# Run tests
php artisan test
```

## Production Deployment Notes

For production deployment:
1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false`
3. Remove or disable `DEV_AUTO_LOGIN`
4. Configure proper database credentials
5. Set up SSL certificate
6. Configure web server (Apache/Nginx)

## Support

If you encounter issues:
1. Check the troubleshooting section above
2. Verify all prerequisites are installed
3. Ensure you're using the exact commit hash: `220942ee`
4. Check Laravel logs in `storage/logs/`

---

**Repository**: https://github.com/vince0526/enterprise-console  
**Branch**: main  
**Commit**: 220942eede37102a1ed67bc78f5adf1a5e54cc74  
**Date**: September 24, 2025