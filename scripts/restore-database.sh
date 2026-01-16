#!/bin/bash

# ====================================
# DATABASE RESTORE SCRIPT
# ====================================
# Restores database from a backup file
# USE WITH EXTREME CAUTION!
# ====================================

set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

COMPOSE_FILE="${COMPOSE_FILE:-docker-compose.yml}"

# Check if backup file is provided
if [ -z "$1" ]; then
    echo -e "${RED}❌ ERROR: No backup file specified${NC}"
    echo -e "Usage: $0 <backup-file.sql.gz>"
    echo -e ""
    echo -e "Available backups:"
    ls -lh ./backups/db_backup_*.sql.gz 2>/dev/null || echo "  No backups found"
    exit 1
fi

BACKUP_FILE="$1"

# Check if file exists
if [ ! -f "$BACKUP_FILE" ]; then
    echo -e "${RED}❌ ERROR: Backup file not found: $BACKUP_FILE${NC}"
    exit 1
fi

# Load environment variables
if [ -f .env ]; then
    export $(cat .env | grep -v '#' | xargs)
fi

echo -e "${RED}=====================================${NC}"
echo -e "${RED}⚠️  DATABASE RESTORE${NC}"
echo -e "${RED}=====================================${NC}"
echo -e "${RED}WARNING: This will OVERWRITE your current database!${NC}"
echo -e "${RED}Backup file: $BACKUP_FILE${NC}"
echo -e "${RED}Database: ${DB_DATABASE:-school_manager}${NC}"
echo -e "${RED}=====================================${NC}"
echo -e ""

# Confirmation
read -p "Are you ABSOLUTELY sure? Type 'YES' to continue: " CONFIRMATION

if [ "$CONFIRMATION" != "YES" ]; then
    echo -e "${YELLOW}Restore cancelled${NC}"
    exit 0
fi

# Create a safety backup of current state
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
SAFETY_BACKUP="./backups/pre_restore_backup_$TIMESTAMP.sql"

echo -e "\n${YELLOW}Creating safety backup of current state...${NC}"

docker-compose -f $COMPOSE_FILE exec -T database mysqldump \
    -u${DB_USERNAME:-root} \
    -p${DB_PASSWORD} \
    ${DB_DATABASE:-school_manager} > "$SAFETY_BACKUP" 2>/dev/null

gzip "$SAFETY_BACKUP"

echo -e "${GREEN}✅ Safety backup created: ${SAFETY_BACKUP}.gz${NC}"

# Decompress if needed
RESTORE_FILE="$BACKUP_FILE"
if [[ $BACKUP_FILE == *.gz ]]; then
    echo -e "\n${YELLOW}Decompressing backup...${NC}"
    RESTORE_FILE="${BACKUP_FILE%.gz}"
    gunzip -k "$BACKUP_FILE"
    echo -e "${GREEN}✅ Backup decompressed${NC}"
fi

# Restore database
echo -e "\n${YELLOW}Restoring database...${NC}"

docker-compose -f $COMPOSE_FILE exec -T database mysql \
    -u${DB_USERNAME:-root} \
    -p${DB_PASSWORD} \
    ${DB_DATABASE:-school_manager} < "$RESTORE_FILE" || {
    echo -e "${RED}❌ RESTORE FAILED!${NC}"
    echo -e "${RED}Your database may be in an inconsistent state${NC}"
    echo -e "${RED}Safety backup available at: ${SAFETY_BACKUP}.gz${NC}"
    exit 1
}

# Clean up decompressed file
if [[ $BACKUP_FILE == *.gz ]]; then
    rm "$RESTORE_FILE"
fi

echo -e "\n${GREEN}=====================================${NC}"
echo -e "${GREEN}✅ DATABASE RESTORED${NC}"
echo -e "${GREEN}=====================================${NC}"
echo -e "From: $BACKUP_FILE"
echo -e "To: ${DB_DATABASE:-school_manager}"
echo -e "Safety backup: ${SAFETY_BACKUP}.gz"
echo -e "${GREEN}=====================================${NC}"

# Clear application caches
echo -e "\n${YELLOW}Clearing application caches...${NC}"
docker-compose -f $COMPOSE_FILE exec api php artisan cache:clear || true
docker-compose -f $COMPOSE_FILE exec api php artisan config:clear || true

echo -e "${GREEN}✅ Restoration complete!${NC}"
