@echo off
REM Enterprise Management Console - Quick Setup Script for Windows
REM Run this script after cloning the repository

echo 🚀 Setting up Enterprise Management Console...

REM Check if we're in the right directory
if not exist "composer.json" (
    echo ❌ Error: Please run this script from the enterprise-console directory
    pause
    exit /b 1
)

echo 📦 Installing PHP dependencies...
composer install

REM Check if .env exists
if not exist ".env" (
    echo 📝 Creating environment file...
    copy .env.example .env
    
    echo 🔑 Generating application key...
    php artisan key:generate
) else (
    echo ✅ Environment file already exists
)

REM Check if node_modules exists
if exist "package.json" if not exist "node_modules" (
    echo 📦 Installing Node.js dependencies...
    npm install
    
    echo 🏗️ Building frontend assets...
    npm run build
)

echo 🗄️ Setting up database...
REM Run migrations
php artisan migrate --force

echo.
echo ✅ Setup complete!
echo.
echo 🌟 Your Enterprise Management Console is ready!
echo.
echo To start the application:
echo   php artisan serve
echo.
echo Then visit: http://127.0.0.1:8000
echo.
echo 📚 For detailed setup instructions, see: SETUP_INSTRUCTIONS.md
echo.
pause