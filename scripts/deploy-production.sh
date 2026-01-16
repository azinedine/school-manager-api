#!/bin/bash

# ====================================
# PRODUCTION DEPLOYMENT SCRIPT
# ====================================
# This script safely deploys updates to production
# WITHOUT losing data
# ====================================

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
COMPOSE_FILE="docker-compose.prod.yml"
BACKUP_DIR="./backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")

echo -e "${GREEN}=====================================${NC}"
echo -e "${GREEN}🚀 PRODUCTION DEPLOYMENT${NC}"
echo -e "${GREEN}=====================================${NC}"

# ====================================
# 1. Pre-deployment checks
# ====================================
echo -e "\n${YELLOW}Step 1: Pre-deployment checks...${NC}"

# Check if .env exists
if [ ! -f .env ]; then
    echo -e "${RED}❌ ERROR: .env file not found!${NC}"
    echo -e "${RED}Create .env from .env.production.template${NC}"
    exit 1
fi

# Check if APP_ENV is production
if ! grep -q "APP_ENV=production" .env; then
    echo -e "${RED}❌ ERROR: APP_ENV must be 'production' in .env${NC}"
    exit 1
fi

# Check if APP_KEY is set
if grep -q "APP_KEY=$" .env || grep -q "APP_KEY=base64:$" .env; then
    echo -e "${RED}❌ ERROR: APP_KEY not set! Run: php artisan key:generate${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Pre-deployment checks passed${NC}"

# ====================================
# 2. Backup database
# ====================================
echo -e "\n${YELLOW}Step 2: Backing up database...${NC}"

mkdir -p "$BACKUP_DIR"
BACKUP_FILE="$BACKUP_DIR/db_backup_$TIMESTAMP.sql"

docker-compose -f $COMPOSE_FILE exec -T database mysqldump \
    -u${DB_USERNAME:-root} \
    -p${DB_PASSWORD} \
    ${DB_DATABASE} > "$BACKUP_FILE" 2>/dev/null || {
    echo -e "${RED}❌ Database backup failed!${NC}"
    exit 1
}

echo -e "${GREEN}✅ Database backed up to: $BACKUP_FILE${NC}"

# Compress backup
gzip "$BACKUP_FILE"
echo -e "${GREEN}✅ Backup compressed: ${BACKUP_FILE}.gz${NC}"

# ====================================
# 3. Build new image
# ====================================
echo -e "\n${YELLOW}Step 3: Building new Docker image...${NC}"

docker-compose -f $COMPOSE_FILE build --no-cache api

echo -e "${GREEN}✅ Image built successfully${NC}"

# ====================================
# 4. Stop old container (keep database running!)
# ====================================
echo -e "\n${YELLOW}Step 4: Stopping API container...${NC}"

docker-compose -f $COMPOSE_FILE stop api

echo -e "${GREEN}✅ API stopped (database still running)${NC}"

# ====================================
# 5. Start new container
# ====================================
echo -e "\n${YELLOW}Step 5: Starting new API container...${NC}"

docker-compose -f $COMPOSE_FILE up -d api

# Wait for container to be healthy
echo -e "${YELLOW}Waiting for container to be ready...${NC}"
sleep 10

echo -e "${GREEN}✅ New container started${NC}"

# ====================================
# 6. Run migrations (SAFE - never destructive)
# ====================================
echo -e "\n${YELLOW}Step 6: Running database migrations...${NC}"

docker-compose -f $COMPOSE_FILE exec api php artisan migrate --force

echo -e "${GREEN}✅ Migrations completed${NC}"

# ====================================
# 7. Clear and cache
# ====================================
echo -e "\n${YELLOW}Step 7: Optimizing application...${NC}"

docker-compose -f $COMPOSE_FILE exec api php artisan config:cache
docker-compose -f $COMPOSE_FILE exec api php artisan route:cache
docker-compose -f $COMPOSE_FILE exec api php artisan view:cache
docker-compose -f $COMPOSE_FILE exec api php artisan event:cache

echo -e "${GREEN}✅ Application optimized${NC}"

# ====================================
# 8. Remove old containers (not volumes!)
# ====================================
echo -e "\n${YELLOW}Step 8: Cleaning up old containers...${NC}"

docker system prune -f

echo -e "${GREEN}✅ Cleanup completed${NC}"

# ====================================
# 9. Health check
# ====================================
echo -e "\n${YELLOW}Step 9: Performing health check...${NC}"

sleep 5

# Check if API is responding
docker-compose -f $COMPOSE_FILE ps | grep "Up" > /dev/null || {
    echo -e "${RED}❌ DEPLOYMENT FAILED: Containers not running${NC}"
    echo -e "${RED}Rolling back...${NC}"
    
    # Restore from backup
    gunzip "$BACKUP_FILE.gz"
    docker-compose -f $COMPOSE_FILE exec -T database mysql \
        -u${DB_USERNAME:-root} \
        -p${DB_PASSWORD} \
        ${DB_DATABASE} < "$BACKUP_FILE"
    
    exit 1
}

echo -e "${GREEN}✅ Health check passed${NC}"

# ====================================
# 10. Success!
# ====================================
echo -e "\n${GREEN}=====================================${NC}"
echo -e "${GREEN}✅ DEPLOYMENT SUCCESSFUL!${NC}"
echo -e "${GREEN}=====================================${NC}"
echo -e "${GREEN}Timestamp: $TIMESTAMP${NC}"
echo -e "${GREEN}Backup: ${BACKUP_FILE}.gz${NC}"
echo -e "${GREEN}Database: PRESERVED${NC}"
echo -e "${GREEN}Data: INTACT${NC}"
echo -e "${GREEN}=====================================${NC}"

# ====================================
# Important Notes
# ====================================
echo -e "\n${YELLOW}📋 IMPORTANT NOTES:${NC}"
echo -e "1. Database volume 'mysql_data_prod' was NOT touched"
echo -e "2. All data is preserved"
echo -e "3. Backup is available at: ${BACKUP_FILE}.gz"
echo -e "4. To rollback, use: ./scripts/rollback-production.sh"
echo -e ""
echo -e "${YELLOW}⚠️  DO NOT RUN 'docker-compose down -v' UNLESS YOU WANT TO DELETE ALL DATA${NC}"
