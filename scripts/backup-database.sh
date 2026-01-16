#!/bin/bash

# ====================================
# DATABASE BACKUP SCRIPT
# ====================================
# Backs up the database to the backups directory
# Can be run manually or via cron
# ====================================

set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Configuration
BACKUP_DIR="./backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
COMPOSE_FILE="${COMPOSE_FILE:-docker-compose.yml}"

# Load environment variables
if [ -f .env ]; then
    export $(cat .env | grep -v '#' | xargs)
fi

echo -e "${GREEN}=====================================${NC}"
echo -e "${GREEN}📦 DATABASE BACKUP${NC}"
echo -e "${GREEN}=====================================${NC}"

# Create backup directory
mkdir -p "$BACKUP_DIR"

# Backup filename
BACKUP_FILE="$BACKUP_DIR/db_backup_$TIMESTAMP.sql"

echo -e "\n${YELLOW}Backing up database...${NC}"
echo -e "Database: ${DB_DATABASE:-school_manager}"
echo -e "Timestamp: $TIMESTAMP"

# Perform backup
docker-compose -f $COMPOSE_FILE exec -T database mysqldump \
    -u${DB_USERNAME:-root} \
    -p${DB_PASSWORD} \
    ${DB_DATABASE:-school_manager} > "$BACKUP_FILE" 2>/dev/null || {
    echo -e "${RED}❌ Backup failed!${NC}"
    exit 1
}

# Get file size
SIZE=$(du -h "$BACKUP_FILE" | cut -f1)

echo -e "${GREEN}✅ Backup created: $BACKUP_FILE${NC}"
echo -e "${GREEN}   Size: $SIZE${NC}"

# Compress backup
echo -e "\n${YELLOW}Compressing backup...${NC}"
gzip "$BACKUP_FILE"

COMPRESSED_SIZE=$(du -h "${BACKUP_FILE}.gz" | cut -f1)

echo -e "${GREEN}✅ Backup compressed: ${BACKUP_FILE}.gz${NC}"
echo -e "${GREEN}   Compressed size: $COMPRESSED_SIZE${NC}"

# Clean up old backups (keep last 30 days)
echo -e "\n${YELLOW}Cleaning up old backups (>30 days)...${NC}"
find "$BACKUP_DIR" -name "db_backup_*.sql.gz" -type f -mtime +30 -delete

REMAINING=$(ls -1 "$BACKUP_DIR"/db_backup_*.sql.gz 2>/dev/null | wc -l)

echo -e "${GREEN}✅ Old backups cleaned${NC}"
echo -e "${GREEN}   Remaining backups: $REMAINING${NC}"

# Create latest symlink
rm -f "$BACKUP_DIR/latest.sql.gz"
ln -s "$(basename ${BACKUP_FILE}.gz)" "$BACKUP_DIR/latest.sql.gz"

echo -e "\n${GREEN}=====================================${NC}"
echo -e "${GREEN}✅ BACKUP COMPLETED${NC}"
echo -e "${GREEN}=====================================${NC}"
echo -e "File: ${BACKUP_FILE}.gz"
echo -e "Size: $COMPRESSED_SIZE"
echo -e ""
echo -e "${YELLOW}To restore this backup, run:${NC}"
echo -e "  ./scripts/restore-database.sh ${BACKUP_FILE}.gz"
