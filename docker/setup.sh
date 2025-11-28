#!/bin/bash

# Docker setup script for Stinas Task

set -e

echo "🚀 Setting up Docker environment for Stinas Task..."

# Check if .env exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file from example..."
    if [ -f .env.example ]; then
        cp .env.example .env
    else
        echo "⚠️  .env.example not found. Creating basic .env file..."
        cat > .env << EOF
APP_NAME="Stinas Task"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_PORT=8000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=stinas_task
DB_USERNAME=stinas_user
DB_PASSWORD=stinas_password
DB_ROOT_PASSWORD=root_password

PMA_PORT=8080
EOF
    fi
fi

# Generate application key if not set
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo "🔑 Generating application key..."
    docker-compose run --rm app php artisan key:generate
fi

# Build and start containers
echo "🏗️  Building Docker images..."
docker-compose build

echo "🚀 Starting containers..."
docker-compose up -d

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
sleep 10

# Run migrations
echo "📊 Running database migrations..."
docker-compose exec -T app php artisan migrate --force

# Install npm dependencies if package.json exists
if [ -f package.json ]; then
    echo "📦 Installing npm dependencies..."
    docker-compose exec -T app npm install
    
    echo "🔨 Building assets..."
    docker-compose exec -T app npm run build
fi

echo "✅ Setup complete!"
echo ""
echo "🌐 Application: http://localhost:8000"
echo "🗄️  PHPMyAdmin: http://localhost:8080"
echo ""
echo "Use 'docker-compose logs -f' to view logs"
echo "Use 'docker-compose down' to stop services"

