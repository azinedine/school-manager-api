# 🔒 DATA PERSISTENCE & DEPLOYMENT GUIDE

## 🎯 Overview

This guide ensures **ZERO DATA LOSS** across all environments. Follow these practices religiously to maintain data integrity in development, staging, and production.

---

## 🚨 CRITICAL FIXES APPLIED

### Issue #1: SQLite Default (FIXED ✅)
**Problem:** `config/database.php` defaulted to SQLite, causing production data loss  
**Fix:** Changed default to `mysql`  
**Impact:** Production now uses persistent MySQL by default

### Issue #2: Auto-Seeding (FIXED ✅)
**Problem:** Seeders ran on every container restart, overwriting data  
**Fix:** Removed auto-seeding from default `docker-compose.yml`  
**Impact:** Data no longer overwritten on container restarts

### Issue #3: No Production Guards (FIXED ✅)
**Problem:** Destructive commands (`migrate:fresh`, `db:wipe`) worked in production  
**Fix:** Added `AppServiceProvider` guards to block destructive commands  
**Impact:** Accidental data wipes now prevented

### Issue #4: SQLite Files in Repo (FIXED ✅)
**Problem:** `database.sqlite` tracked in git  
**Fix:** Added to `.gitignore`  
**Impact:** No more accidental SQLite usage

---

## 📋 ENVIRONMENT SETUP

### Development Environment

```bash
# Use the development compose file
docker-compose -f docker-compose.dev.yml up -d

# This will:
# ✅ Run migrations automatically
# ✅ Seed reference data
# ✅ Use mysql_data_dev volume
# ✅ Enable debug mode
```

**Development Volume:** `mysql_data_dev`  
**Data Persistence:** Survives container rebuilds  
**Auto-Seeding:** YES (reference data only)

### Production Environment

```bash
# Use the production compose file
docker-compose -f docker-compose.prod.yml up -d

# This will:
# ✅ Use production settings
# ❌ NOT run migrations automatically
# ❌ NOT seed data
# ✅ Use mysql_data_prod volume
```

**Production Volume:** `mysql_data_prod`  
**Data Persistence:** PERMANENT (unless manually deleted)  
**Auto-Seeding:** NO

---

## 🔐 DOCKER VOLUME ARCHITECTURE

### Named Volumes (Current Setup - ✅ CORRECT)

```yaml
volumes:
  mysql_data_prod:
    driver: local
```

**Characteristics:**
- Data stored in Docker-managed location
- Survives container deletion
- Survives image rebuilds
- Only deleted with `docker-compose down -v` or `docker volume rm`
- **RECOMMENDED** for production

### What We AVOID (Anonymous Volumes - ❌ WRONG)

```yaml
# NEVER DO THIS:
volumes:
  - /var/lib/mysql  # Anonymous volume
```

**Problems:**
- Gets new volume ID on each rebuild
- Data appears to be "lost"
- Hard to backup
- Hard to track

---

## 🚀 DEPLOYMENT PROCESS

### Production Deployment (SAFE METHOD)

```bash
# 1. Deploy with automatic backup and safety checks
./scripts/deploy-production.sh

# This script:
# ✅ Backs up database automatically
# ✅ Builds new image
# ✅ Stops old container (keeps database running!)
# ✅ Starts new container
# ✅ Runs migrations (SAFE - never destructive)
# ✅ Optimizes caches
# ✅ Performs health checks
# ✅ Rolls back on failure
```

### Manual Deployment Steps

```bash
# 1. Backup database first!
./scripts/backup-database.sh

# 2. Build new image
docker-compose -f docker-compose.prod.yml build api

# 3. Stop ONLY the API container (database keeps running!)
docker-compose -f docker-compose.prod.yml stop api

# 4. Start new API container
docker-compose -f docker-compose.prod.yml up -d api

# 5. Run migrations
docker-compose -f docker-compose.prod.yml exec api php artisan migrate --force

# 6. Clear caches
docker-compose -f docker-compose.prod.yml exec api php artisan config:cache
docker-compose -f docker-compose.prod.yml exec api php artisan route:cache
docker-compose -f docker-compose.prod.yml exec api php artisan view:cache
```

---

## ⚠️ WHAT NEVER TO DO

### 🚫 NEVER Run These Commands in Production

```bash
# ❌ CATASTROPHIC - Deletes all volumes (including database!)
docker-compose down -v

# ❌ WIPES DATABASE - Blocked by AppServiceProvider but still dangerous
php artisan migrate:fresh
php artisan migrate:reset
php artisan db:wipe

# ❌ Removes database container and creates new one (loses data)
docker-compose down database
docker-compose up -d database

# ❌ Removes named volume (PERMANENT DATA LOSS)
docker volume rm school-manager-api_mysql_data_prod
```

### ✅ SAFE Commands

```bash
# ✅ Safe - Stops containers but keeps volumes
docker-compose down

# ✅ Safe - Restarts containers with existing data
docker-compose restart

# ✅ Safe - Rebuilds containers without touching data
docker-compose up -d --build

# ✅ Safe - Forward-only migrations
docker-compose exec api php artisan migrate --force

# ✅ Safe - Check volume status
docker volume ls
docker volume inspect school-manager-api_mysql_data_prod
```

---

## 💾 BACKUP & RESTORE

### Automated Backups

```bash
# Manual backup
./scripts/backup-database.sh

# Setup automated daily backups (cron)
crontab -e

# Add this line for daily backups at 2 AM
0 2 * * * cd /path/to/school-manager-api && ./scripts/backup-database.sh >> /var/log/db-backup.log 2>&1
```

**Backup Location:** `./backups/`  
**Retention:** 30 days (automatic cleanup)  
**Format:** Compressed SQL dumps (`.sql.gz`)

### Restore from Backup

```bash
# List available backups
ls -lh ./backups/

# Restore specific backup
./scripts/restore-database.sh ./backups/db_backup_20260116_120000.sql.gz

# Restore latest backup
./scripts/restore-database.sh ./backups/latest.sql.gz
```

⚠️ **WARNING:** Restore will overwrite your current database!  
✅ **SAFETY:** Script creates a backup before restoring

---

## 🌱 SEEDING DATA

### Reference Data (Safe - Idempotent)

```bash
# Seed reference data (subjects, levels, wilayas)
./scripts/seed-reference-data.sh

# Or manually:
docker-compose exec api php artisan db:seed --class=SubjectSeeder --force
docker-compose exec api php artisan db:seed --class=LevelSeeder --force
docker-compose exec api php artisan db:seed --class=WilayaMunicipalitySeeder --force
```

**Safe to run multiple times:** YES (idempotent)  
**When to run:** Initial setup only  
**Production:** Safe (adds reference data, doesn't overwrite user data)

### Full Database Seeder (DANGEROUS in Production)

```bash
# ⚠️ ONLY FOR DEVELOPMENT
docker-compose -f docker-compose.dev.yml exec api php artisan db:seed
```

**Safe to run multiple times:** NO (creates duplicate data)  
**When to run:** Development setup only  
**Production:** ❌ NEVER (blocked by environment check)

---

## 🔍 VERIFICATION & MONITORING

### Check Database Volume

```bash
# List all volumes
docker volume ls

# Inspect production volume
docker volume inspect school-manager-api_mysql_data_prod

# Check volume size
docker system df -v
```

### Check Database Connection

```bash
# Test database connection
docker-compose exec api php artisan db:show

# Check migrations status
docker-compose exec api php artisan migrate:status

# Access MySQL directly
docker-compose exec database mysql -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE}
```

### Check Data Integrity

```bash
# Count records in key tables
docker-compose exec database mysql -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE} -e "
SELECT 
    'users' as table_name, COUNT(*) as count FROM users
UNION ALL
SELECT 'institutions', COUNT(*) FROM institutions
UNION ALL
SELECT 'subjects', COUNT(*) FROM subjects
UNION ALL
SELECT 'levels', COUNT(*) FROM levels;
"
```

---

## 🏗️ DOCKER COMPOSE FILES

### `docker-compose.yml` (Safe Default)
- NO auto-migrations
- NO auto-seeding
- Safest option
- Use when unsure

### `docker-compose.dev.yml` (Development)
- Auto-runs migrations
- Auto-seeds reference data
- Debug mode enabled
- Hot reload support

### `docker-compose.prod.yml` (Production)
- NO auto-operations
- Production optimizations
- Resource limits
- Health checks
- Use with `deploy-production.sh`

---

## 🔐 DATABASE CONFIGURATION

### Current Setup (✅ CORRECT)

```php
// config/database.php
'default' => env('DB_CONNECTION', 'mysql'),  // ✅ MySQL default
```

### .env Configuration

```env
# ✅ CORRECT - MySQL with named volume
DB_CONNECTION=mysql
DB_HOST=database
DB_PORT=3306
DB_DATABASE=school_manager
DB_USERNAME=school_user
DB_PASSWORD=your-strong-password
```

### ❌ NEVER Use SQLite in Production

```env
# ❌ WRONG - SQLite loses data on container rebuild
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite
```

---

## 🛡️ PRODUCTION SAFETY GUARDS

### Destructive Commands Blocked

The `AppServiceProvider` now blocks these commands in production:

```php
- migrate:fresh   // ❌ Blocked
- migrate:refresh // ❌ Blocked
- migrate:reset   // ❌ Blocked
- db:wipe         // ❌ Blocked
```

**If you need to run them:**
1. Change `APP_ENV=local` in `.env` (temporarily)
2. Run the command
3. Change back to `APP_ENV=production`

⚠️ **Only do this if you know what you're doing!**

---

## 📊 DATA FLOW DIAGRAM

```
┌─────────────────────────────────────────────────────────────┐
│                     DOCKER HOST                              │
│                                                              │
│  ┌──────────────────┐         ┌──────────────────┐         │
│  │  API Container   │────────▶│ Database Container│         │
│  │  (Ephemeral)     │         │   (Ephemeral)     │         │
│  │                  │         │                   │         │
│  │  - Application   │         │  - MySQL Server   │         │
│  │  - Code          │         │                   │         │
│  └──────────────────┘         └─────────┬─────────┘         │
│           │                             │                   │
│           │                             │                   │
│           ▼                             ▼                   │
│  ┌──────────────────┐         ┌──────────────────┐         │
│  │ storage/ (bind)  │         │ mysql_data       │         │
│  │ (Persistent)     │         │ (Named Volume)   │         │
│  │                  │         │ ✅ PERSISTENT    │         │
│  │ - Logs           │         │ ✅ SURVIVES      │         │
│  │ - Uploads        │         │    REBUILDS      │         │
│  └──────────────────┘         └──────────────────┘         │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

**Key Points:**
- Containers are ephemeral (can be destroyed and recreated)
- Named volumes persist data
- Bind mounts sync with host filesystem
- Database volume is the critical component

---

## 🆘 TROUBLESHOOTING

### Problem: Data disappeared after rebuild

**Cause:** Ran `docker-compose down -v`  
**Solution:** Restore from backup:
```bash
./scripts/restore-database.sh ./backups/latest.sql.gz
```

### Problem: "Database does not exist" error

**Cause:** Database volume deleted  
**Solution:**
```bash
# Recreate database
docker-compose up -d database
docker-compose exec api php artisan migrate --force
./scripts/seed-reference-data.sh
```

### Problem: Migrations run automatically in production

**Cause:** Using wrong compose file  
**Solution:** Use `docker-compose.prod.yml` which doesn't auto-migrate

### Problem: Seeder overwrites data

**Cause:** Seeder ran in production  
**Solution:**
```bash
# Restore from backup
./scripts/restore-database.sh ./backups/latest.sql.gz
```

---

## ✅ CHECKLIST

### Initial Setup
- [ ] Copy `.env.template` to `.env`
- [ ] Set strong `DB_PASSWORD`
- [ ] Generate `APP_KEY`: `php artisan key:generate`
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Start containers: `docker-compose -f docker-compose.prod.yml up -d`
- [ ] Run migrations: `docker-compose exec api php artisan migrate --force`
- [ ] Seed reference data: `./scripts/seed-reference-data.sh`
- [ ] Create first backup: `./scripts/backup-database.sh`

### Regular Maintenance
- [ ] Backup database daily (automate with cron)
- [ ] Monitor volume size: `docker system df -v`
- [ ] Check logs: `docker-compose logs -f api`
- [ ] Verify backups work: Test restore in dev environment
- [ ] Keep backup retention at 30 days

### Before Deployment
- [ ] Backup database: `./scripts/backup-database.sh`
- [ ] Test in staging first
- [ ] Use deployment script: `./scripts/deploy-production.sh`
- [ ] Verify health check passes
- [ ] Monitor logs for errors

### After Deployment
- [ ] Verify application works
- [ ] Check database connection
- [ ] Run health checks
- [ ] Create post-deployment backup

---

## 📚 QUICK REFERENCE

### Common Commands

```bash
# Development
docker-compose -f docker-compose.dev.yml up -d
docker-compose -f docker-compose.dev.yml down

# Production  
docker-compose -f docker-compose.prod.yml up -d
docker-compose -f docker-compose.prod.yml down

# Backup
./scripts/backup-database.sh

# Restore
./scripts/restore-database.sh ./backups/db_backup_TIMESTAMP.sql.gz

# Deploy
./scripts/deploy-production.sh

# Seed reference data
./scripts/seed-reference-data.sh

# Migrations
docker-compose exec api php artisan migrate --force
docker-compose exec api php artisan migrate:status

# Logs
docker-compose logs -f api
docker-compose logs -f database

# Shell access
docker-compose exec api bash
docker-compose exec database mysql -u${DB_USERNAME} -p

# Volume management
docker volume ls
docker volume inspect mysql_data_prod
```

---

## 🎓 BEST PRACTICES

1. **Always backup before deployments**
2. **Never use `-v` flag with `docker-compose down`**
3. **Use named volumes, never anonymous volumes**
4. **Keep backups for at least 30 days**
5. **Test restores regularly**
6. **Monitor volume disk space**
7. **Use separate volumes for dev/staging/prod**
8. **Document any manual database changes**
9. **Use migrations for schema changes**
10. **Keep `.env` files secure and never commit them**

---

## 🏆 SUCCESS CRITERIA

You've achieved data persistence when:

- ✅ Database survives `docker-compose down` && `docker-compose up -d`
- ✅ Database survives `docker-compose down` && `docker-compose build` && `docker-compose up -d`
- ✅ Data remains after code changes and redeploys
- ✅ Destructive commands are blocked in production
- ✅ Automated backups are running
- ✅ You can successfully restore from backup
- ✅ No seeders run automatically in production
- ✅ Named volumes are used for all persistent data
- ✅ `.env` is properly configured and secured
- ✅ Deployment scripts work without data loss

---

## 📞 SUPPORT

If data loss occurs:
1. **DON'T PANIC**
2. **DON'T run any more commands**
3. **DON'T restart containers**
4. Check `./backups/` directory
5. Restore from latest backup
6. Review this guide to prevent future issues

---

**Last Updated:** January 16, 2026  
**Version:** 1.0  
**Status:** Production-Ready ✅
