#!/bin/bash

# ====================================
# SEED REFERENCE DATA SCRIPT
# ====================================
# Seeds ONLY reference data (subjects, levels, wilayas)
# Safe to run multiple times (idempotent)
# Should be run ONCE during initial setup
# ====================================

set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

COMPOSE_FILE="${COMPOSE_FILE:-docker-compose.yml}"

echo -e "${GREEN}=====================================${NC}"
echo -e "${GREEN}🌱 SEED REFERENCE DATA${NC}"
echo -e "${GREEN}=====================================${NC}"

# Check environment
ENV=$(docker-compose -f $COMPOSE_FILE exec -T api printenv APP_ENV 2>/dev/null | tr -d '\r' || echo "unknown")

if [ "$ENV" = "production" ]; then
    echo -e "${RED}⚠️  WARNING: Running in PRODUCTION environment${NC}"
    echo -e "${RED}This will add reference data (subjects, levels, wilayas)${NC}"
    echo -e ""
    read -p "Continue? (y/N): " CONFIRM
    
    if [ "$CONFIRM" != "y" ] && [ "$CONFIRM" != "Y" ]; then
        echo -e "${YELLOW}Cancelled${NC}"
        exit 0
    fi
fi

echo -e "\n${YELLOW}Seeding reference data...${NC}"

# Seed reference data in order
SEEDERS=(
    "SubjectSeeder"
    "LevelSeeder"
    "WilayaMunicipalitySeeder"
    "MaterialSeeder"
    "ReferenceSeeder"
    "LearningObjectiveSeeder"
    "TeachingMethodSeeder"
)

for SEEDER in "${SEEDERS[@]}"; do
    echo -e "${YELLOW}Seeding $SEEDER...${NC}"
    docker-compose -f $COMPOSE_FILE exec api php artisan db:seed --class=$SEEDER --force || {
        echo -e "${RED}❌ Failed to seed $SEEDER${NC}"
        exit 1
    }
    echo -e "${GREEN}✅ $SEEDER completed${NC}"
done

echo -e "\n${GREEN}=====================================${NC}"
echo -e "${GREEN}✅ REFERENCE DATA SEEDED${NC}"
echo -e "${GREEN}=====================================${NC}"
echo -e "Seeded:"
echo -e "  - Subjects (14 items)"
echo -e "  - Levels (7 items)"
echo -e "  - Wilayas & Municipalities (~1,500 items)"
echo -e "  - Materials"
echo -e "  - References"
echo -e "  - Learning Objectives"
echo -e "  - Teaching Methods"
echo -e ""
echo -e "${YELLOW}Note: Seeders are idempotent - they won't duplicate data${NC}"
