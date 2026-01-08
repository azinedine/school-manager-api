# Git Branching Strategy for Production & Development

This guide shows how to maintain separate branches for production (stable) and development (experimental) code.

---

## **Branch Strategy**

```
main (production)  ← Stable code, deployed to users
  ↑
  | merge when ready
  |
develop (development) ← Active development, new features
  ↑
  | merge features
  |
feature/* ← Individual feature branches
```

---

## **Initial Setup**

### **Step 1: Check Current Branch**

```bash
cd school-manager-api
git branch
# You should see: * main or * master
```

### **Step 2: Create Development Branch**

```bash
# Create and switch to develop branch
git checkout -b develop

# Push to remote (if using GitHub/GitLab)
git push -u origin develop
```

### **Step 3: Do the Same for Frontend**

```bash
cd ../school-admin-panel
git checkout -b develop
git push -u origin develop
```

---

## **Daily Workflow**

### **Working on New Features (Development)**

```bash
# 1. Switch to develop branch
git checkout develop

# 2. Make sure you have latest code
git pull origin develop

# 3. Start dev servers
php artisan serve  # Backend
npm run dev        # Frontend

# 4. Make your changes...

# 5. Commit when ready
git add .
git commit -m "feat: add new feature"
git push origin develop
```

### **Using Stable Production Code**

```bash
# 1. Switch to main branch
git checkout main

# 2. Use Docker for production
docker-compose up -d

# 3. Your stable app is now running!
```

---

## **Complete Workflow Example**

### **Monday Morning - Start Development**

```bash
# Backend
cd school-manager-api
git checkout develop
git pull
php artisan serve

# Frontend
cd school-admin-panel
git checkout develop
git pull
npm run dev

# Code all week on develop branch...
```

### **Friday - Deploy to Production**

```bash
# 1. Test development code
git checkout develop
docker-compose up -d --build
# Test everything works...

# 2. Merge to production
git checkout main
git merge develop
git push origin main

# 3. Deploy production
docker-compose up -d --build
```

---

## **Advanced: Feature Branches**

For larger features, create separate branches:

```bash
# Create feature branch from develop
git checkout develop
git checkout -b feature/student-grading

# Work on feature...
git add .
git commit -m "feat: add student grading system"

# When done, merge back to develop
git checkout develop
git merge feature/student-grading

# Delete feature branch
git branch -d feature/student-grading
```

---

## **Quick Reference Commands**

### **Switching Branches**

```bash
# Switch to development
git checkout develop

# Switch to production
git checkout main

# See all branches
git branch -a

# See current branch
git branch --show-current
```

### **Creating Branches**

```bash
# Create new branch
git checkout -b branch-name

# Create from specific branch
git checkout -b feature/xyz develop
```

### **Merging Branches**

```bash
# Merge develop into main
git checkout main
git merge develop

# Merge with no fast-forward (preserves history)
git merge --no-ff develop
```

### **Syncing with Remote**

```bash
# Push current branch
git push origin branch-name

# Pull latest changes
git pull origin branch-name

# Push all branches
git push --all origin
```

---

## **Recommended Setup**

### **Branch Protection (if using GitHub/GitLab)**

**main branch:**
- ✅ Require pull request reviews
- ✅ Require status checks to pass
- ✅ No direct pushes

**develop branch:**
- ✅ Allow direct pushes
- ⚠️ Optional: Require tests to pass

---

## **Environment-Specific Files**

### **.env Files per Branch**

Keep different environment settings:

**main branch (.env):**
```env
APP_ENV=production
APP_DEBUG=false
DB_DATABASE=school_manager_prod
```

**develop branch (.env):**
```env
APP_ENV=local
APP_DEBUG=true
DB_DATABASE=school_manager_dev
```

**Note:** `.env` files are gitignored, so create them per branch manually.

---

## **Practical Example: Your Daily Routine**

### **Scenario 1: Regular Development**

```bash
# Morning: Start coding
git checkout develop
php artisan serve  # or docker-compose up -d
npm run dev

# Afternoon: Commit progress
git add .
git commit -m "feat: improve user dashboard"
git push origin develop

# Evening: Switch to stable version for actual work
git checkout main
docker-compose down  # stop dev if using docker
docker-compose up -d  # start production
```

### **Scenario 2: Release to Production**

```bash
# 1. Finish feature on develop
git checkout develop
git add .
git commit -m "feat: complete new feature"
git push origin develop

# 2. Test production build
docker-compose up -d --build
# Test thoroughly...

# 3. Merge to production
git checkout main
git merge develop
git push origin main

# 4. Tag release (optional)
git tag -a v1.2.0 -m "Release version 1.2.0"
git push origin v1.2.0

# 5. Deploy
docker-compose up -d --build
```

### **Scenario 3: Hotfix on Production**

```bash
# 1. Create hotfix branch from main
git checkout main
git checkout -b hotfix/critical-bug

# 2. Fix the bug
# ... make changes ...
git commit -m "fix: critical bug in login"

# 3. Merge to main
git checkout main
git merge hotfix/critical-bug

# 4. Also merge to develop to keep in sync
git checkout develop
git merge hotfix/critical-bug

# 5. Delete hotfix branch
git branch -d hotfix/critical-bug
```

---

## **Pro Tips**

### **1. Never Commit .env Files**

Ensure `.env` is in `.gitignore`:
```bash
echo ".env" >> .gitignore
git add .gitignore
git commit -m "chore: ensure .env is ignored"
```

### **2. Use Different Databases per Branch**

```bash
# develop branch .env
DB_DATABASE=school_manager_dev

# main branch .env  
DB_DATABASE=school_manager_prod
```

### **3. Automate with Git Hooks**

Create `.git/hooks/post-checkout`:
```bash
#!/bin/bash
# Auto-run composer/npm install after checkout

branch=$(git rev-parse --abbrev-ref HEAD)

if [ "$branch" = "develop" ]; then
    echo "📦 Running composer install..."
    composer install
fi
```

Make it executable:
```bash
chmod +x .git/hooks/post-checkout
```

---

## **Visual Workflow**

```
You code here ↓

feature/new-ui ──────┐
                     ├──→ develop ────────────┐
feature/api-fix ─────┘      ↑                 │
                           you test here      │
                                             you merge when ready
                                              │
                                              ↓
                                            main (production)
                                              ↑
                                         deployed here
```

---

## **Summary**

| Branch | Purpose | Docker? | Commands |
|--------|---------|---------|----------|
| **main** | Production, stable | ✅ Yes | `docker-compose up -d` |
| **develop** | Active development | ⚡ Dev servers | `npm run dev`, `php artisan serve` |
| **feature/*** | New features | ⚡ Dev servers | Merge to develop when done |

---

## **Getting Started Now**

```bash
# Backend
cd school-manager-api
git checkout -b develop
git push -u origin develop

# Frontend
cd school-admin-panel
git checkout -b develop  
git push -u origin develop

# You're ready! Start coding on develop branch
```

---

**Remember:**
- 🟢 **develop** = Your playground, experiment freely!
- 🔴 **main** = Production code, only merge tested features
- 🔵 **feature/*** = Individual features, merge to develop when done
