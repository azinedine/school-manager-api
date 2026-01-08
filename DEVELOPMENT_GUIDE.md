# Development & Production Workflows

This guide explains the difference between **Development** and **Production** modes, and when to use each.

---

## **Development Mode (Local Development)**

Use this when you're actively coding and want instant feedback with hot-reloading.

### **What You're Currently Using** ✅

You have development servers running:
- Frontend: `npm run dev` on port 5173
- Backend: `php artisan serve` on port 8000

### **When to Use Development Mode**

- ✅ Writing new code
- ✅ Testing features quickly
- ✅ Debugging issues
- ✅ Hot-reload on file changes (instant updates)
- ✅ Full error messages and debugging tools
- ✅ Faster startup time

### **Development Workflow**

#### **Backend (Laravel API)**

```bash
cd school-manager-api

# 1. Install dependencies (first time only)
composer install
npm install

# 2. Set up environment
cp .env.example .env
php artisan key:generate

# 3. Set up database (use MySQL via Docker or local)
# Option A: Use Docker MySQL only
docker run -d \
  --name school-db \
  -p 3306:3306 \
  -e MYSQL_DATABASE=school_manager \
  -e MYSQL_ROOT_PASSWORD=secret \
  mysql:8.0

# 4. Update .env for local development
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=school_manager
# DB_USERNAME=root
# DB_PASSWORD=your_password

# 5. Run migrations
php artisan migrate:fresh --seed

# 6. Start development server
php artisan serve
# API available at http://localhost:8000
```

#### **Frontend (React/Vite)**

```bash
cd school-admin-panel

# 1. Install dependencies (first time only)
npm install

# 2. Create .env.local for development
cat > .env.local << EOF
VITE_API_URL=http://localhost:8000
EOF

# 3. Start development server
npm run dev
# App available at http://localhost:5173 (with hot-reload!)
```

---

## **Production Mode (Docker)**

Use this for deployment or testing the production build locally.

### **When to Use Production Mode**

- ✅ Deploying to a server
- ✅ Testing production builds
- ✅ Performance testing
- ✅ Sharing with stakeholders
- ✅ CI/CD pipelines
- ✅ Isolated, reproducible environments

### **Production Workflow**

#### **Backend (Laravel API with Docker)**

```bash
cd school-manager-api

# 1. Set up environment
cp .env.docker .env
# Edit .env and set secure values

# 2. Build and start containers
docker-compose up -d --build

# 3. Run migrations
docker-compose exec api php artisan migrate:fresh --seed

# 4. Optimize for production
docker-compose exec api php artisan config:cache
docker-compose exec api php artisan route:cache

# API available at http://localhost:8000
```

#### **Frontend (React with Docker)**

```bash
cd school-admin-panel

# Build and start
docker-compose up -d --build

# App available at http://localhost:3000
```

---

## **Comparison: Dev vs Production**

| Feature | Development | Production (Docker) |
|---------|------------|---------------------|
| **Startup Speed** | ⚡ Fast | 🐢 Slower (build time) |
| **Code Changes** | 🔄 Instant hot-reload | 🔁 Need rebuild |
| **Debugging** | 🐛 Full error details | 🔒 Limited errors |
| **Performance** | 🏃 Good enough | 🚀 Optimized |
| **Environment** | 💻 Your machine | 🐳 Isolated containers |
| **Database** | 📦 Local or Docker | 🐳 Docker MySQL |

---

## **Switching Between Modes**

### **From Development → Production**

```bash
# Stop dev servers (Ctrl+C in terminals)

# Start Docker
cd school-manager-api && docker-compose up -d
cd school-admin-panel && docker-compose up -d
```

### **From Production → Development**

```bash
# Stop Docker
cd school-manager-api && docker-compose down
cd school-admin-panel && docker-compose down

# Start dev servers
cd school-manager-api && php artisan serve
cd school-admin-panel && npm run dev
```

---

## **Recommended Daily Workflow**

1. **Development:** Use `npm run dev` + `php artisan serve` for coding
2. **Testing:** Use Docker before committing to test production build
3. **Deployment:** Use Docker on your server

---

**Remember:** 
- Use **Development** for coding (faster, hot-reload)
- Use **Production** for deployment (optimized, isolated)
