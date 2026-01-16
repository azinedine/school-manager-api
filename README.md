# School Manager API (Backend)

A robust Laravel-based REST API for school management system with clean architecture and comprehensive features.

## 🚀 Features

### Core Features
- **RESTful API** - Well-structured API endpoints
- **Authentication** - Laravel Sanctum token-based auth
- **Authorization** - Role-based access control (RBAC)
- **Soft Deletes** - Data integrity with soft deletion
- **Validation** - Comprehensive request validation
- **CORS Support** - Configured for frontend integration

### API Modules
- **User Management** - CRUD operations for users with roles
- **Institution Management** - Manage schools and educational institutions
- **Class Management** - Grade classes and student organization
- **Lesson Management** - Lesson tracking and preparation
- **Grade Management** - Student grades and performance tracking
- **Attendance** - Student attendance and tardiness records
- **Reports** - Student reports and analytics
- **Timetable** - Class schedule management

## 🛠️ Tech Stack

- **Framework:** Laravel 12
- **Language:** PHP 8.2+
- **Database:** MySQL 8.0
- **Authentication:** Laravel Sanctum
- **Testing:** PHPUnit
- **Architecture:** Clean Architecture with Repository Pattern

## 📋 Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL 8.0 or higher
- Node.js & npm (for asset compilation)

## 🚀 Getting Started

### 1. Install Dependencies

```bash
composer install
npm install
```

### 2. Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

Configure your database and other settings in `.env`:

```env
APP_NAME="School Manager"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_manager
DB_USERNAME=root
DB_PASSWORD=your_password

# CORS Configuration
CORS_ALLOWED_ORIGINS=http://localhost:5173,http://localhost:3000
SANCTUM_STATEFUL_DOMAINS=localhost:5173,localhost:3000
SESSION_DOMAIN=localhost
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Run Migrations

```bash
# Run migrations
php artisan migrate

# Run migrations with seeders
php artisan migrate:fresh --seed
```

### 5. Start Development Server

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## 🐳 Docker Deployment

### Build and Run with Docker

```bash
# Build and start containers
docker-compose up -d --build

# Run migrations
docker-compose exec api php artisan migrate --seed

# View logs
docker-compose logs -f api

# Stop containers
docker-compose down
```

The API will be available at `http://localhost:8000`

See [DOCKER_README.md](./DOCKER_README.md) for detailed Docker instructions.

## 📁 Project Structure

```
app/
├── Console/
│   └── Commands/          # Artisan commands
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   └── Api/V1/        # API v1 controllers
│   ├── Middleware/        # Custom middleware
│   ├── Requests/          # Form request validation
│   └── Resources/         # API resources (transformers)
├── Models/                # Eloquent models
├── Observers/             # Model observers
├── Policies/              # Authorization policies
├── Providers/             # Service providers
├── Repositories/          # Repository pattern implementation
│   ├── Contracts/         # Repository interfaces
│   └── Eloquent/          # Eloquent implementations
├── Services/              # Business logic layer
├── Traits/                # Reusable traits
└── UseCases/              # Use case implementations

database/
├── factories/             # Model factories
├── migrations/            # Database migrations
└── seeders/               # Database seeders

routes/
├── api.php               # API routes
├── web.php               # Web routes
└── console.php           # Console routes
```

## 🏗️ Architecture

The project follows **Clean Architecture** principles:

### Layers

1. **Controllers** (HTTP Layer)
   - Handle HTTP requests/responses
   - Validate input via Form Requests
   - Delegate to Services

2. **Services** (Business Logic Layer)
   - Contain business logic
   - Orchestrate operations
   - Use Repositories for data access

3. **Repositories** (Data Access Layer)
   - Abstract database operations
   - Implement Repository Pattern
   - Use Eloquent models

4. **Models** (Entity Layer)
   - Eloquent ORM models
   - Define relationships
   - Contain scopes and accessors

### Example Flow

```
Request → Controller → Service → Repository → Model → Database
                                      ↓
                                  Response
```

## 🔌 API Endpoints

### Authentication

```
POST   /api/register          # Register new user
POST   /api/login             # Login user
POST   /api/logout            # Logout user
GET    /api/user              # Get authenticated user
DELETE /api/user              # Delete user account
```

### Institutions

```
GET    /api/v1/institutions                    # List institutions
POST   /api/v1/institutions                    # Create institution
GET    /api/v1/institutions/{id}               # Get institution
PUT    /api/v1/institutions/{id}               # Update institution
DELETE /api/v1/institutions/{id}               # Delete institution
POST   /api/v1/institutions/{id}/restore       # Restore deleted
```

### Grade Classes

```
GET    /api/v1/grade-classes                   # List classes
POST   /api/v1/grade-classes                   # Create class
GET    /api/v1/grade-classes/{id}              # Get class
PUT    /api/v1/grade-classes/{id}              # Update class
DELETE /api/v1/grade-classes/{id}              # Delete class
DELETE /api/v1/grade-classes                   # Delete all classes
```

### Students

```
GET    /api/v1/grade-classes/{id}/students     # List students in class
POST   /api/v1/grade-classes/{id}/students     # Add student to class
POST   /api/v1/grade-classes/{id}/students/batch  # Batch add students
PUT    /api/v1/grade-students/{id}             # Update student
DELETE /api/v1/grade-students/{id}             # Delete student
POST   /api/v1/grade-students/{id}/move        # Move student to another class
```

### Lesson Preparation

```
GET    /api/v1/lesson-preparations             # List preparations
POST   /api/v1/lesson-preparations             # Create preparation
GET    /api/v1/lesson-preparations/{id}        # Get preparation
PUT    /api/v1/lesson-preparations/{id}        # Update preparation
DELETE /api/v1/lesson-preparations/{id}        # Delete preparation
GET    /api/v1/lesson-preparations/statistics/summary  # Get statistics
```

### Lessons

```
GET    /api/v1/lessons                         # List lessons
POST   /api/v1/lessons                         # Create lesson
GET    /api/v1/lessons/{id}                    # Get lesson
PUT    /api/v1/lessons/{id}                    # Update lesson
DELETE /api/v1/lessons/{id}                    # Delete lesson
GET    /api/v1/lessons/statistics/summary      # Get statistics
```

### Public Endpoints

```
GET    /api/v1/wilayas                         # List wilayas (provinces)
GET    /api/v1/wilayas/{id}/municipalities     # List municipalities
GET    /api/v1/subjects                        # List subjects
GET    /api/v1/levels                          # List education levels
GET    /api/v1/materials                       # List materials
GET    /api/v1/references                      # List references
GET    /api/v1/learning-objectives             # List learning objectives
GET    /api/v1/teaching-methods                # List teaching methods
```

## 🔒 Authentication & Authorization

### Authentication

The API uses Laravel Sanctum for token-based authentication:

```bash
# Login to get token
POST /api/login
{
  "email": "user@example.com",
  "password": "password"
}

# Response
{
  "token": "1|abc123...",
  "user": {...}
}

# Use token in subsequent requests
Authorization: Bearer 1|abc123...
```

### Authorization

Role-based access control:

- **Super Admin** - Full system access
- **Admin** - Institution-level management
- **Teacher** - Class and student management
- **Student** - Read-only access to own data

Policies are defined in `app/Policies/` and enforced at the controller level.

## 🧪 Testing

### Run Tests

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter TestName

# Run with coverage
php artisan test --coverage
```

### Test Structure

```
tests/
├── Feature/           # Feature tests (HTTP)
│   ├── AuthTest.php
│   └── InstitutionTest.php
├── Unit/              # Unit tests
└── TestCase.php       # Base test case
```

## 📊 Database

### Migrations

```bash
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Reset database
php artisan migrate:fresh

# Run with seeders
php artisan migrate:fresh --seed
```

### Seeders

```bash
# Run all seeders
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=UserSeeder
```

Available seeders:
- `DatabaseSeeder` - Main seeder
- `UserSeeder` - Create test users
- `InstitutionSeeder` - Create institutions
- `SubjectSeeder` - Seed subjects
- `LevelSeeder` - Seed education levels

## 🔧 Development

### Code Style

```bash
# Format code with Laravel Pint
./vendor/bin/pint

# Check code style
./vendor/bin/pint --test
```

### Artisan Commands

```bash
# List all routes
php artisan route:list

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Generate IDE helper files
php artisan ide-helper:generate
php artisan ide-helper:models
```

## 🚀 Deployment

### Production Checklist

1. Set environment to production:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

2. Optimize application:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   composer install --optimize-autoloader --no-dev
   ```

3. Set proper permissions:
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

4. Configure database backups

5. Set up queue workers:
   ```bash
   php artisan queue:work --daemon
   ```

6. Configure supervisor for queue workers

## 📝 API Response Format

All API responses follow a consistent format:

### Success Response

```json
{
  "data": {
    "id": 1,
    "name": "Example",
    ...
  }
}
```

### Error Response

```json
{
  "message": "Error message",
  "errors": {
    "field": ["Validation error"]
  }
}
```

### Pagination Response

```json
{
  "data": [...],
  "links": {...},
  "meta": {
    "current_page": 1,
    "total": 100,
    ...
  }
}
```

## 🔍 Debugging

### Enable Query Logging

Add to `AppServiceProvider`:

```php
\DB::listen(function($query) {
    \Log::info($query->sql, $query->bindings);
});
```

### Laravel Telescope (Optional)

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

Access at: `http://localhost:8000/telescope`

## 🤝 Contributing

1. Follow PSR-12 coding standards
2. Write tests for new features
3. Update documentation
4. Use meaningful commit messages
5. Follow the existing architecture patterns

## 📄 License

This project is proprietary and confidential.

## 🆘 Support

For issues or questions:
1. Check the [DEVELOPMENT_GUIDE.md](./DEVELOPMENT_GUIDE.md)
2. Review the [GIT_WORKFLOW.md](./GIT_WORKFLOW.md)
3. Check Laravel documentation: https://laravel.com/docs

## 🔗 Related Projects

- **Frontend:** `../school-admin-panel` - React/TypeScript frontend
- **Documentation:** See root-level `.md` files for detailed guides

---

**Built with ❤️ for educational excellence**
