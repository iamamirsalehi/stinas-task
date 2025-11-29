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
APP_URL=http://localhost:4000
APP_PORT=4000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=stinas_task
DB_USERNAME=stinas_user
DB_PASSWORD=stinas_password
DB_ROOT_PASSWORD=root_password

PMA_PORT=4002
PMA_USER=admin
PMA_PASSWORD=admin123
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
# Read DB_ROOT_PASSWORD from .env file
DB_ROOT_PASSWORD=$(grep "^DB_ROOT_PASSWORD=" .env 2>/dev/null | cut -d '=' -f2 | tr -d '"' || echo "root_password")
max_attempts=60
attempt=0
mysql_ready=false

# First, wait for MySQL container to be healthy
while [ $attempt -lt $max_attempts ]; do
    health_status=$(docker inspect --format='{{.State.Health.Status}}' stinas-task-mysql 2>/dev/null || echo "unknown")
    if [ "$health_status" = "healthy" ]; then
        echo "✅ MySQL container is healthy!"
        mysql_ready=true
        break
    fi
    attempt=$((attempt + 1))
    echo "   Waiting for MySQL health check... ($attempt/$max_attempts)"
    sleep 2
done

# Then verify MySQL is actually accepting connections
if [ "$mysql_ready" = "true" ]; then
    attempt=0
    echo "   Verifying MySQL connection..."
    while [ $attempt -lt 10 ]; do
        if docker-compose exec -T mysql mysqladmin ping -h localhost -u root -p"$DB_ROOT_PASSWORD" --silent 2>/dev/null; then
            echo "✅ MySQL is accepting connections!"
            sleep 2  # Give it a moment to fully initialize
            break
        fi
        attempt=$((attempt + 1))
        echo "   Verifying connection... ($attempt/10)"
        sleep 1
    done
fi

if [ "$mysql_ready" != "true" ] || [ $attempt -eq 10 ]; then
    echo "⚠️  MySQL did not become ready in time. Continuing anyway..."
fi

# Ensure database exists and user has proper permissions
if [ "$mysql_ready" = "true" ]; then
    echo "🔧 Ensuring database and permissions are set up..."
    DB_DATABASE=$(grep "^DB_DATABASE=" .env 2>/dev/null | cut -d '=' -f2 | tr -d '"' || echo "stinas_task")
    DB_USERNAME=$(grep "^DB_USERNAME=" .env 2>/dev/null | cut -d '=' -f2 | tr -d '"' || echo "stinas_user")
    
    docker-compose exec -T mysql mysql -u root -p"$DB_ROOT_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS \`$DB_DATABASE\`;" 2>/dev/null || true
    docker-compose exec -T mysql mysql -u root -p"$DB_ROOT_PASSWORD" -e "GRANT ALL PRIVILEGES ON \`$DB_DATABASE\`.* TO '$DB_USERNAME'@'%';" 2>/dev/null || true
    docker-compose exec -T mysql mysql -u root -p"$DB_ROOT_PASSWORD" -e "FLUSH PRIVILEGES;" 2>/dev/null || true
    echo "✅ Database and permissions configured!"
fi

# Wait for app container to be ready
echo "⏳ Waiting for app container to be ready..."
attempt=0
while [ $attempt -lt 30 ]; do
    if docker-compose exec -T app php -r "exit(0);" 2>/dev/null; then
        echo "✅ App container is ready!"
        break
    fi
    attempt=$((attempt + 1))
    echo "   Waiting for app container... ($attempt/30)"
    sleep 1
done

# Clear Laravel config cache to ensure fresh .env values
echo "🔄 Clearing Laravel configuration cache..."
docker-compose exec -T app php artisan config:clear 2>/dev/null || true

# Give MySQL a moment to fully initialize after health check
echo "⏳ Giving MySQL a moment to fully initialize..."
sleep 3

# Test database connection from app container
echo "🔍 Testing database connection from app container..."
attempt=0
db_connected=false
while [ $attempt -lt 15 ]; do
    # Test using environment variables from docker-compose
    if docker-compose exec -T app php -r "\$host = getenv('DB_HOST') ?: 'mysql'; \$db = getenv('DB_DATABASE') ?: 'stinas_task'; \$user = getenv('DB_USERNAME') ?: 'stinas_user'; \$pass = getenv('DB_PASSWORD') ?: 'stinas_password'; try { \$pdo = new PDO(\"mysql:host=\$host;port=3306;dbname=\$db\", \$user, \$pass); echo 'OK'; } catch (Exception \$e) { exit(1); }" 2>/dev/null | grep -q "OK"; then
        db_connected=true
        echo "✅ Database connection successful!"
        break
    fi
    attempt=$((attempt + 1))
    echo "   Testing connection... ($attempt/15)"
    sleep 2
done

if [ "$db_connected" != "true" ]; then
    echo "⚠️  Could not establish database connection. Continuing anyway..."
fi

# Create phpMyAdmin .htpasswd file if PMA_USER and PMA_PASSWORD are set
if [ -f .env ]; then
    PMA_USER=$(grep "^PMA_USER=" .env 2>/dev/null | cut -d '=' -f2 | tr -d '"' || echo "admin")
    PMA_PASSWORD=$(grep "^PMA_PASSWORD=" .env 2>/dev/null | cut -d '=' -f2 | tr -d '"' || echo "admin123")
    if [ -n "$PMA_USER" ] && [ -n "$PMA_PASSWORD" ]; then
        echo "🔐 Setting up phpMyAdmin HTTP Basic Authentication..."
        mkdir -p docker/phpmyadmin
        # Generate .htpasswd file using htpasswd from httpd image
        docker run --rm httpd:2.4 htpasswd -nbB "$PMA_USER" "$PMA_PASSWORD" > docker/phpmyadmin/.htpasswd 2>/dev/null || \
        echo "$PMA_USER:$(openssl passwd -apr1 "$PMA_PASSWORD" 2>/dev/null)" > docker/phpmyadmin/.htpasswd
        echo "✅ phpMyAdmin authentication configured!"
    fi
fi

# Run migrations
echo "📊 Running database migrations..."
docker-compose exec -T app php artisan migrate --force

# Install npm dependencies if package.json exists
if [ -f package.json ]; then
    echo "📦 Installing npm dependencies..."
    if docker-compose exec -T app which npm >/dev/null 2>&1; then
        docker-compose exec -T app npm install
        
        echo "🔨 Building assets..."
        docker-compose exec -T app npm run build
    else
        echo "⚠️  npm is not available in the container. Skipping npm steps."
        echo "   To install npm, add Node.js to your Dockerfile."
    fi
fi

echo "✅ Setup complete!"
echo ""
echo "🌐 Application: http://localhost:4000"
echo "🗄️  PHPMyAdmin: http://localhost:4002"
PMA_USER=$(grep "^PMA_USER=" .env 2>/dev/null | cut -d '=' -f2 | tr -d '"' || echo "admin")
PMA_PASSWORD=$(grep "^PMA_PASSWORD=" .env 2>/dev/null | cut -d '=' -f2 | tr -d '"' || echo "admin123")
echo "   Username: $PMA_USER"
echo "   Password: $PMA_PASSWORD"
echo ""
echo "Use 'docker-compose logs -f' to view logs"
echo "Use 'docker-compose down' to stop services"

