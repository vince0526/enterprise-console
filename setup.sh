#!/bin/bash

# Enterprise Management Console - Quick Setup Script
# Run this script after cloning the repository

echo "🚀 Setting up Enterprise Management Console..."

# Check if we're in the right directory
if [ ! -f "composer.json" ]; then
    echo "❌ Error: Please run this script from the enterprise-console directory"
    exit 1
fi

echo "📦 Installing PHP dependencies..."
composer install

# Check if .env exists
if [ ! -f ".env" ]; then
    echo "📝 Creating environment file..."
    cp .env.example .env
    
    echo "🔑 Generating application key..."
    php artisan key:generate
else
    echo "✅ Environment file already exists"
fi

# Check if node_modules exists
if [ -f "package.json" ] && [ ! -d "node_modules" ]; then
    echo "📦 Installing Node.js dependencies..."
    npm install
    
    echo "🏗️ Building frontend assets..."
    npm run build
fi

echo "🗄️ Setting up database..."
# Check if migrations need to be run
php artisan migrate:status > /dev/null 2>&1
if [ $? -ne 0 ]; then
    echo "Running database migrations..."
    php artisan migrate --force
else
    echo "✅ Database already migrated"
fi

# Set permissions (if on Linux/Mac)
if [ "$(uname)" != "Darwin" ] && [ "$(expr substr $(uname -s) 1 5)" != "MINGW" ]; then
    echo "🔒 Setting file permissions..."
    chmod -R 775 storage/ bootstrap/cache/
fi

echo ""
echo "✅ Setup complete!"
echo ""
echo "🌟 Your Enterprise Management Console is ready!"
echo ""
echo "To start the application:"
echo "  php artisan serve"
echo ""
echo "Then visit: http://127.0.0.1:8000"
echo ""
echo "📚 For detailed setup instructions, see: SETUP_INSTRUCTIONS.md"