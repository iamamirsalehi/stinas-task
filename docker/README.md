# Docker Setup for Stinas Task

This project is containerized using Docker and Docker Compose.

## Prerequisites

- Docker Engine 20.10+
- Docker Compose 2.0+

## Quick Start

1. **Copy the environment file:**
   ```bash
   cp .env.docker.example .env
   ```

2. **Generate application key:**
   ```bash
   docker-compose run --rm app php artisan key:generate
   ```

3. **Start the services:**
   ```bash
   docker-compose up -d
   ```

4. **Run database migrations:**
   ```bash
   docker-compose exec app php artisan migrate
   ```

5. **Seed the database (optional):**
   ```bash
   docker-compose exec app php artisan db:seed
   ```

6. **Install npm dependencies and build assets:**
   ```bash
   docker-compose exec app npm install
   docker-compose exec app npm run build
   ```

## Services

- **app**: Laravel application (PHP 8.3) - http://localhost:4000
- **mysql**: MySQL 8 database - port 13306 (external), 3306 (internal)
- **phpmyadmin**: PHPMyAdmin interface - http://localhost:4002
- **queue**: Laravel queue worker (optional)
- **scheduler**: Laravel task scheduler (optional)

## Useful Commands

### View logs
```bash
docker-compose logs -f app
docker-compose logs -f mysql
```

### Execute Artisan commands
```bash
docker-compose exec app php artisan [command]
```

### Access container shell
```bash
docker-compose exec app sh
```

### Stop services
```bash
docker-compose down
```

### Stop and remove volumes (WARNING: deletes database data)
```bash
docker-compose down -v
```

### Rebuild containers
```bash
docker-compose build --no-cache
```

### Run tests
```bash
docker-compose exec app php artisan test
```

## Environment Variables

Key environment variables can be set in `.env` file:
- `APP_PORT`: Application port (default: 4000)
- `DB_PORT`: MySQL external port (default: 13306)
- `DB_DATABASE`: Database name
- `DB_USERNAME`: Database user
- `DB_PASSWORD`: Database password
- `DB_ROOT_PASSWORD`: MySQL root password
- `PMA_PORT`: PHPMyAdmin port (default: 4002)
- `PMA_USER`: PHPMyAdmin HTTP Basic Auth username (default: admin)
- `PMA_PASSWORD`: PHPMyAdmin HTTP Basic Auth password (default: admin123)

## Database Access

- **Host**: mysql (from within containers) or localhost (from host)
- **Port**: 3306 (from within containers) or 13306 (from host)
- **Username**: Set via `DB_USERNAME` (default: stinas_user)
- **Password**: Set via `DB_PASSWORD` (default: stinas_password)

## PHPMyAdmin Access

### HTTP Basic Authentication

When accessing PHPMyAdmin, you'll first be prompted for HTTP Basic Authentication:

- **URL**: http://localhost:4002
- **Username**: `admin` (set via `PMA_USER` in `.env`)
- **Password**: `admin123` (set via `PMA_PASSWORD` in `.env`)

### MySQL Login

After passing HTTP Basic Authentication, use these credentials to log into MySQL:

- **Server**: `mysql` (or leave as default)
- **Username**: Use `DB_USERNAME` (default: `stinas_user`) or `root` for full access
- **Password**: Use `DB_PASSWORD` or `DB_ROOT_PASSWORD` from `.env`

## Production Considerations

For production deployment:
1. Set `APP_ENV=production` and `APP_DEBUG=false`
2. Use `production` target in Dockerfile
3. Consider using a proper web server (Nginx/Apache) instead of PHP's built-in server
4. Set up proper SSL/TLS certificates
5. Use environment-specific secrets management
6. Configure proper backup strategies for MySQL data

