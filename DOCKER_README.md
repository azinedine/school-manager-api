# School Manager API (Backend) - Docker Setup

Laravel API backend with MySQL database using Docker.

## Prerequisites

- Docker Desktop installed ([Download here](https://www.docker.com/products/docker-desktop))
- Docker Compose (included with Docker Desktop)

## Quick Start

1. **Navigate to the API directory**:
   ```bash
   cd school-manager-api
   ```

2. **Set up environment variables**:
   ```bash
   cp .env.docker .env
   # Edit .env and update values, especially APP_KEY
   ```

3. **Generate Laravel application key**:
   ```bash
   # Option 1: If you have PHP locally
   php artisan key:generate
   
   # Option 2: Generate key and add to .env manually
   # Generate a base64 encoded 32-character string
   ```

4. **Build and start all services**:
   ```bash
   docker-compose up -d --build
   ```

5. **Run initial database migrations**:
   ```bash
   docker-compose exec api php artisan migrate:fresh --seed
   ```

6. **Access the application**:
   - Backend API: http://localhost:8000
   - Database: localhost:3306

## Services

### 1. Database (MySQL 8.0)
- Container: `school-manager-db`
- Port: `3306`
- Default credentials:
  - Database: `school_manager`
  - Username: `school_user`
  - Password: `secret` (⚠️ change in production!)

### 2. API (Laravel)
- Container: `school-manager-api`
- Port: `8000`
- PHP 8.2 + Apache

## Configuration

### Environment Variables

Key variables in `.env`:

```env
# Database
DB_DATABASE=school_manager
DB_USERNAME=school_user
DB_PASSWORD=secret_password_change_me

# Laravel
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_KEY_HERE
APP_URL=http://localhost:8000

# CORS & Sanctum (for frontend)
SANCTUM_STATEFUL_DOMAINS=localhost:3000
SESSION_DOMAIN=localhost
CORS_ALLOWED_ORIGINS=http://localhost:3000
```

### Port Configuration

To change ports, modify `docker-compose.yml`:

```yaml
# API port
api:
  ports:
    - "8080:80"  # Change 8000 to 8080

# Database port  
database:
  ports:
    - "3307:3306"  # Change 3306 to 3307
```

## Common Commands

### Start services
```bash
docker-compose up -d
```

### Stop services
```bash
docker-compose down
```

### View logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f api
docker-compose logs -f database
```

### Rebuild after code changes
```bash
docker-compose up -d --build api
```

### Laravel Artisan commands
```bash
# Run migrations
docker-compose exec api php artisan migrate

# Seed database
docker-compose exec api php artisan db:seed

# Clear cache
docker-compose exec api php artisan config:clear
docker-compose exec api php artisan cache:clear
docker-compose exec api php artisan route:clear

# Create new migration
docker-compose exec api php artisan make:migration create_table_name

# Access Laravel Tinker
docker-compose exec api php artisan tinker
```

### Database access
```bash
# Access MySQL CLI
docker-compose exec database mysql -u school_user -p school_manager

# Backup database
docker-compose exec database mysqldump -u school_user -p school_manager > backup.sql

# Restore database
docker-compose exec -T database mysql -u school_user -p school_manager < backup.sql
```

### Container access
```bash
# Access API container bash
docker-compose exec api bash

# Run composer commands
docker-compose exec api composer install
docker-compose exec api composer update
```

### Clean up
```bash
# Stop and remove containers
docker-compose down

# Also remove volumes (⚠️ deletes all data!)
docker-compose down -v
```

## File Structure

```
school-manager-api/
├── Dockerfile                  # Backend Docker configuration
├── docker-compose.yml          # Orchestration (API + Database)
├── docker/
│   └── apache/
│       └── 000-default.conf   # Apache virtual host
├── .dockerignore              # Files to exclude from build
├── .env.docker               # Environment template
└── DOCKER_README.md          # This file
```

## Connecting with Frontend

The backend is configured to accept requests from `http://localhost:3000` by default.

**To run both backend and frontend:**

1. Start backend:
   ```bash
   cd school-manager-api
   docker-compose up -d
   ```

2. Start frontend (in separate terminal):
   ```bash
   cd school-admin-panel
   docker-compose up -d
   ```

## Troubleshooting

### Port already in use
```bash
# Find what's using the port
lsof -i :8000
# or
netstat -an | grep 8000

# Change port in docker-compose.yml or stop the conflicting service
```

### Database connection refused
```bash
# Check database is healthy
docker-compose ps

# View database logs
docker-compose logs database

# Restart database
docker-compose restart database
```

### Permission issues
```bash
# Fix storage permissions
docker-compose exec api chmod -R 775 storage bootstrap/cache
docker-compose exec api chown -R www-data:www-data storage bootstrap/cache
```

### Migration errors
```bash
# Reset database (⚠️ deletes all data)
docker-compose exec api php artisan migrate:fresh --seed

# Rollback last migration
docker-compose exec api php artisan migrate:rollback
```

### CORS errors from frontend
1. Verify `CORS_ALLOWED_ORIGINS` in `.env`
2. Check `config/cors.php`
3. Clear config cache:
   ```bash
   docker-compose exec api php artisan config:clear
   ```

## Security for Production

⚠️ **Important**:
- Change all default passwords
- Generate strong `APP_KEY`
- Set `APP_DEBUG=false`
- Configure proper CORS origins
- Use HTTPS (add reverse proxy)
- Don't expose database port publicly
- Use strong database passwords
- Enable rate limiting
- Keep dependencies updated

## Development vs Production

### Development Mode
To run in development with hot-reloading, consider using volume mounts:

```yaml
volumes:
  - ./:/var/www/html
```

### Production Mode
Current setup is production-optimized:
- Code is copied into image
- Composer optimized autoloader
- Config and route caching enabled
- No dev dependencies

## Support

- [Laravel Documentation](https://laravel.com/docs)
- [Docker Documentation](https://docs.docker.com/)
- [MySQL Documentation](https://dev.mysql.com/doc/)

## Prerequisites

- Docker Desktop installed ([Download here](https://www.docker.com/products/docker-desktop))
- Docker Compose (included with Docker Desktop)

## Quick Start

1. **Clone the repository and navigate to the project root**:
   ```bash
   cd school-manager-project
   ```

2. **Set up environment variables**:
   ```bash
   # Copy the example environment file
   cp school-manager-api/.env.docker school-manager-api/.env
   
   # Generate Laravel application key
   cd school-manager-api
   php artisan key:generate
   # Or if you don't have PHP locally, you'll do this after first build
   ```

3. **Build and start all services**:
   ```bash
   docker-compose up -d --build
   ```

4. **Run initial database migrations** (first time only):
   ```bash
   docker-compose exec api php artisan migrate:fresh --seed
   ```

5. **Access the applications**:
   - Frontend: http://localhost:3000
   - Backend API: http://localhost:8000
   - Database: localhost:3306

## Services

The Docker setup includes three services:

### 1. Database (MySQL 8.0)
- Container: `school-manager-db`
- Port: `3306`
- Default credentials:
  - Database: `school_manager`
  - Username: `school_user`
  - Password: `secret` (change in production!)

### 2. API (Laravel)
- Container: `school-manager-api`
- Port: `8000`
- Built from: `./school-manager-api/Dockerfile`
- Apache + PHP 8.2

### 3. Frontend (React/Vite)
- Container: `school-admin-panel`
- Port: `3000`
- Built from: `./school-admin-panel/Dockerfile`
- Nginx serving static build

## Common Commands

### Start all services
```bash
docker-compose up -d
```

### Stop all services
```bash
docker-compose down
```

### View logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f api
docker-compose logs -f frontend
docker-compose logs -f database
```

### Rebuild services
```bash
# Rebuild all
docker-compose up -d --build

# Rebuild specific service
docker-compose up -d --build api
```

### Execute commands in containers
```bash
# Laravel Artisan commands
docker-compose exec api php artisan migrate
docker-compose exec api php artisan db:seed
docker-compose exec api php artisan config:clear

# Access bash in API container
docker-compose exec api bash

# Access MySQL
docker-compose exec database mysql -u school_user -p school_manager
```

### Clean up
```bash
# Stop and remove containers, networks
docker-compose down

# Also remove volumes (WARNING: deletes all data!)
docker-compose down -v
```

## Development vs Production

### Development
For development, you might want to use volume mounts to enable hot-reloading:

1. Use `docker-compose.dev.yml` (create separately)
2. Mount source code as volumes
3. Run dev servers instead of production builds

### Production
The current setup is optimized for production:
- Multi-stage builds for smaller images
- Optimized configurations
- Static file serving via Nginx
- Caching enabled

## Troubleshooting

### Port already in use
If ports 3000, 8000, or 3306 are already in use, modify the ports in `docker-compose.yml`:
```yaml
ports:
  - "3001:80"  # Change 3000 to 3001
```

### Database connection issues
1. Ensure database service is healthy:
   ```bash
   docker-compose ps
   ```
2. Check database logs:
   ```bash
   docker-compose logs database
   ```

### Permission issues
If you encounter permission issues with Laravel storage:
```bash
docker-compose exec api chmod -R 775 storage bootstrap/cache
docker-compose exec api chown -R www-data:www-data storage bootstrap/cache
```

### Frontend can't reach API
1. Verify API is running: http://localhost:8000
2. Check CORS settings in Laravel (`config/cors.php`)
3. Ensure `VITE_API_URL` environment variable is set correctly

## File Structure

```
school-manager-project/
├── school-admin-panel/
│   ├── Dockerfile              # Frontend Docker configuration
│   ├── nginx.conf             # Nginx configuration for serving build
│   └── ... (React/Vite source)
├── school-manager-api/
│   ├── Dockerfile             # Backend Docker configuration
│   ├── docker/
│   │   └── apache/
│   │       └── 000-default.conf  # Apache vhost configuration
│   ├── .env.docker           # Example environment file
│   └── ... (Laravel source)
└── docker-compose.yml        # Docker Compose orchestration
```

## Security Notes

⚠️ **Important for Production**:
1. Change all default passwords
2. Set strong `APP_KEY` in Laravel
3. Configure proper CORS settings
4. Use HTTPS (add reverse proxy like Traefik/Nginx)
5. Don't expose database port (3306) to public
6. Set `APP_DEBUG=false` in production
7. Use environment-specific `.env` files

## Support

For issues or questions, please refer to:
- [Docker Documentation](https://docs.docker.com/)
- [Laravel Docker Configuration](https://laravel.com/docs/deployment)
- [Nginx Configuration](https://nginx.org/en/docs/)
