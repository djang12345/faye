#!/bin/bash

# DPS System Startup Script
# This script will automatically set up the database and load sample data if needed

echo "🚀 Starting DPS System..."

# Check if .env file exists
if [ ! -f .env ]; then
    echo "❌ .env file not found. Please copy .env.example to .env and configure your database settings."
    exit 1
fi

# Install dependencies if node_modules doesn't exist
if [ ! -d "node_modules" ]; then
    echo "📦 Installing Node.js dependencies..."
    npm install
fi

# Install PHP dependencies if vendor doesn't exist
if [ ! -d "vendor" ]; then
    echo "📦 Installing PHP dependencies..."
    composer install
fi

# Generate application key if not set
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate
fi

# Run migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Load sample data if database is empty
echo "📊 Checking if sample data is needed..."
php artisan dps:load-sample-data

# Build frontend assets
echo "🔨 Building frontend assets..."
npm run build

# Start the development server
echo "🌐 Starting development server..."
echo ""
echo "✅ DPS System is ready!"
echo ""
echo "🔐 Login Credentials:"
echo "ADMIN USERS:"
echo "  • admin@dps.gov.ph / admin123"
echo "  • hr@dps.gov.ph / admin123"
echo ""
echo "EMPLOYEE USERS:"
echo "  • john.smith@dps.gov.ph / employee123"
echo "  • maria.garcia@dps.gov.ph / employee123"
echo "  • robert.johnson@dps.gov.ph / employee123"
echo ""
echo "🌍 Application will be available at: http://localhost:8000"
echo ""

php artisan serve --host=0.0.0.0 --port=8000
