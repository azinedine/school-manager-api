# 🚀 Quick Reference - Data Persistence

## ⚡ Common Commands

### Development

```bash
# Start development environment (auto-migrates, auto-seeds)
docker-compose -f docker-compose.dev.yml up -d

# View logs
docker-compose -f docker-compose.dev.yml logs -f api

# Stop (keeps data)
docker-compose -f docker-compose.dev.yml down

# Rebuild
docker-compose -f docker-compose.dev.yml up -d --build
```

### Production

```bash
# Deploy safely with automatic backup
./scripts/deploy-production.sh

# Manual deployment steps
./scripts/backup-database.sh
docker-compose -f docker-compose.prod.yml build api
docker-compose -f docker-compose.prod.yml stop api
docker-compose -f docker-compose.prod.yml up -d api
docker-compose -f docker-compose.prod.yml exec api php artisan migrate --force
```

### Database Operations

```bash
# Backup database
./scripts/backup-database.sh

# Restore from backup
./scripts/restore-database.sh ./backups/db_backup_TIMESTAMP.sql.gz

# Seed reference data (one-time)
./scripts/seed-reference-data.sh

# Run migrations
docker-compose exec api php artisan migrate --force

# Check migration status
docker-compose exec api php artisan migrate:status
```

### Volume Management

```bash
# List volumes
docker volume ls

# Inspect volume
docker volume inspect school-manager-api_mysql_data_prod

# Check disk usage
docker system df -v
```

## ⚠️ NEVER DO

```bash
# ❌ CATASTROPHIC - Deletes all volumes including database
docker-compose down -v

# ❌ DATA LOSS - Deletes database volume
docker volume rm school-manager-api_mysql_data_prod

# ❌ BLOCKED - Destructive migrations
php artisan migrate:fresh
php artisan migrate:reset
php artisan db:wipe
```

## ✅ SAFE Commands

```bash
# ✅ Safe - Stops containers, keeps volumes
docker-compose down

# ✅ Safe - Restarts containers
docker-compose restart

# ✅ Safe - Rebuilds without touching data
docker-compose up -d --build

# ✅ Safe - Forward-only migrations
php artisan migrate --force
```

## 📂 File Structure

```
school-manager-api/
├── docker-compose.yml         # Safe default (no auto-operations)
├── docker-compose.dev.yml     # Development (auto-migrates, auto-seeds)
├── docker-compose.prod.yml    # Production (manual operations)
├── scripts/
│   ├── deploy-production.sh   # Automated deployment
│   ├── backup-database.sh     # Backup script
│   ├── restore-database.sh    # Restore script
│   └── seed-reference-data.sh # One-time seeding
├── DATA_PERSISTENCE_GUIDE.md  # Full documentation
├── DEPLOYMENT_FIXES_APPLIED.md # Summary of fixes
└── QUICK_REFERENCE.md         # This file
```

## 🔐 Environment Files

```bash
# Development
cp .env.template .env
# Edit: APP_ENV=local, APP_DEBUG=true

# Production
cp .env.production.template .env
# Edit: APP_ENV=production, APP_DEBUG=false, strong passwords
```

## 📊 Health Checks

```bash
# Check if containers are running
docker-compose ps

# Check database connection
docker-compose exec api php artisan db:show

# Check application health
curl http://localhost:8000/api/health

# View application logs
docker-compose logs -f api

# View database logs
docker-compose logs -f database
```

## 🆘 Emergency Recovery

```bash
# 1. DON'T PANIC
# 2. DON'T restart anything
# 3. Check if data still exists
docker volume ls | grep mysql_data

# 4. If volume exists, restore from it
docker-compose up -d

# 5. If volume lost, restore from backup
./scripts/restore-database.sh ./backups/latest.sql.gz
```

## 📞 Documentation

- **Full Guide:** `DATA_PERSISTENCE_GUIDE.md`
- **Fixes Applied:** `DEPLOYMENT_FIXES_APPLIED.md`
- **This Reference:** `QUICK_REFERENCE.md`

---

**Last Updated:** January 16, 2026  
**Status:** ✅ Production Ready
