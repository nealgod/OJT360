# OJT360 - Complete Clone & Setup Guide for Windows

**Last Updated:** November 27, 2025

---

## 📋 Prerequisites (Install First)

### 1. **Git for Windows**
- Download: https://git-scm.com/download/win
- Install with default settings
- Verify: Open CMD and run `git --version`

### 2. **PHP 8.0.2+**
- Download: https://windows.php.net/downloads/releases/
- Choose **Thread Safe** version (e.g., `php-8.0.28-Win32-vs16-x64.zip`)
- Extract to `C:\php` (or your preferred location)
- Add PHP to Windows PATH:
  - Right-click "This PC" → Properties → Advanced system settings
  - Click "Environment Variables"
  - Under "System variables", click "New"
  - Variable name: `PATH`
  - Variable value: `C:\php` (or your PHP path)
  - Click OK
- Verify: Open CMD and run `php --version`

### 3. **Composer**
- Download: https://getcomposer.org/download/
- Run the installer (it will auto-detect PHP)
- Verify: Open CMD and run `composer --version`

### 4. **Node.js & NPM**
- Download: https://nodejs.org/ (LTS version recommended)
- Install with default settings
- Verify: Open CMD and run `node --version` and `npm --version`

### 5. **MySQL 5.7+ or MariaDB**
- Download MySQL: https://dev.mysql.com/downloads/mysql/
- Or MariaDB: https://mariadb.org/download/
- Install and remember your root password
- Verify: Open CMD and run `mysql --version`

---

## 🚀 Step-by-Step Clone & Setup

### Step 1: Clone the Repository

```bash
# Open CMD and navigate to where you want the project
cd C:\Users\YourUsername\Documents

# Clone the repository
git clone https://github.com/nealgod/OJT360.git

# Navigate into the project
cd OJT360
```

### Step 2: Install PHP Dependencies

```bash
# Install all PHP packages from composer.json
composer install

# This will take 2-5 minutes depending on internet speed
```

### Step 3: Install Node Dependencies

```bash
# Install all JavaScript packages from package.json
npm install

# This will take 1-3 minutes
```

### Step 4: Setup Environment File

```bash
# Copy the example environment file
copy .env.example .env

# Or manually:
# 1. Open .env.example
# 2. Save as .env in the same directory
```

### Step 5: Generate Application Key

```bash
# Generate a unique encryption key
php artisan key:generate

# You should see: "Application key set successfully."
```

### Step 6: Create Database

```bash
# Open MySQL command line or MySQL Workbench
# Create a new database
CREATE DATABASE ojt360 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Or use this command in CMD:
mysql -u root -p -e "CREATE DATABASE ojt360 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### Step 7: Configure .env File

Open `.env` file and update these values:

```env
APP_NAME=OJT360
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ojt360
DB_USERNAME=root
DB_PASSWORD=your_mysql_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_email@example.com
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@ojt360.local
MAIL_FROM_NAME="${APP_NAME}"
```

### Step 8: Run Database Migrations

```bash
# Run all migrations to create tables
php artisan migrate

# Seed the database with initial data
php artisan db:seed

# You should see: "Database seeding completed successfully."
```

### Step 9: Create Storage Link

```bash
# Create symbolic link for file uploads
php artisan storage:link

# You should see: "The [public/storage] directory has been successfully created."
```

### Step 10: Build Frontend Assets

```bash
# Build CSS and JavaScript
npm run build

# Or for development with hot reload:
npm run dev
```

### Step 11: Start the Development Server

```bash
# In one CMD window, start Laravel server
php artisan serve

# You should see: "Laravel development server started on http://127.0.0.1:8000"
```

### Step 12: Access the Application

Open your browser and go to: **http://localhost:8000**

---

## 📦 All Libraries & Dependencies Used

### **Backend (PHP/Laravel)**

| Package | Version | Purpose |
|---------|---------|---------|
| Laravel Framework | ^9.19 | Web framework |
| PHP | ^8.0.2 | Server-side language |
| Laravel Sanctum | ^3.0 | API authentication |
| Laravel Tinker | ^2.7 | Interactive shell |
| Doctrine DBAL | ^3.10 | Database abstraction |
| Guzzle HTTP | ^7.2 | HTTP client |

### **PDF Generation**

| Package | Version | Purpose |
|---------|---------|---------|
| barryvdh/laravel-dompdf | ^2.2 | HTML to PDF conversion |
| FPDF | ^1.8 | Basic PDF creation |
| FPDI | ^2.6 | PDF manipulation |
| TCPDF | ^6.10 | Advanced PDF features |

### **Excel/Spreadsheet**

| Package | Version | Purpose |
|---------|---------|---------|
| Maatwebsite Excel | 3.1.50 | Excel import/export |
| PHPOffice Spreadsheet | 1.29 | Spreadsheet manipulation |

### **Frontend (JavaScript/CSS)**

| Package | Version | Purpose |
|---------|---------|---------|
| Vite | ^4.0.0 | Build tool |
| Tailwind CSS | ^3.1.0 | CSS framework |
| Alpine.js | ^3.4.2 | JavaScript framework |
| PostCSS | ^8.4.6 | CSS processing |
| Autoprefixer | ^10.4.2 | CSS vendor prefixes |
| Axios | ^1.1.2 | HTTP client |
| Lodash | ^4.17.19 | Utility library |
| pdf-lib | ^1.17.1 | PDF manipulation |
| Tailwind Forms | ^0.5.2 | Form styling |
| Laravel Vite Plugin | ^0.7.2 | Laravel integration |

### **Development Tools**

| Package | Version | Purpose |
|---------|---------|---------|
| PHPUnit | ^9.5.10 | Unit testing |
| Faker | ^1.9.1 | Fake data generation |
| Mockery | ^1.4.4 | Mocking library |
| Laravel Pint | ^1.0 | Code style fixer |
| Laravel Sail | ^1.0.1 | Docker environment |
| Collision | ^6.1 | Error display |
| Laravel Ignition | ^1.0 | Error debugging |
| Laravel Breeze | ^1.19 | Authentication scaffolding |

---

## 🔧 Common Commands

### Development

```bash
# Start development server
php artisan serve

# Build frontend assets (one-time)
npm run build

# Watch frontend assets (auto-rebuild on changes)
npm run dev

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Clear cache
php artisan cache:clear

# Clear all caches
php artisan optimize:clear
```

### Database

```bash
# Create new migration
php artisan make:migration create_table_name

# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Refresh database (rollback + migrate)
php artisan migrate:refresh

# Seed database
php artisan db:seed
```

### Code Quality

```bash
# Run tests
php artisan test

# Fix code style
./vendor/bin/pint

# Check code style
./vendor/bin/pint --test
```

---

## 🐛 Troubleshooting

### Issue: "PHP not found"
**Solution:** Add PHP to PATH environment variable and restart CMD

### Issue: "Composer not found"
**Solution:** Restart CMD after installing Composer

### Issue: "npm not found"
**Solution:** Restart CMD after installing Node.js

### Issue: "SQLSTATE[HY000]: General error: 1030 Got error"
**Solution:** Check MySQL is running and database exists

### Issue: "Class not found" errors
**Solution:** Run `composer dump-autoload`

### Issue: "npm ERR! code ERESOLVE"
**Solution:** Run `npm install --legacy-peer-deps`

### Issue: "Port 8000 already in use"
**Solution:** Run on different port: `php artisan serve --port=8001`

---

## 📝 Project Structure

```
OJT360/
├── app/                    # Application code
│   ├── Http/Controllers/   # Route controllers
│   ├── Models/             # Eloquent models
│   ├── Services/           # Business logic
│   └── Support/            # Helper classes
├── database/
│   ├── migrations/         # Database migrations
│   └── seeders/            # Database seeders
├── resources/
│   ├── views/              # Blade templates
│   ├── css/                # Stylesheets
│   └── js/                 # JavaScript files
├── routes/
│   ├── web.php             # Web routes
│   └── api.php             # API routes
├── public/                 # Public assets
├── storage/                # File uploads
├── tests/                  # Test files
├── vendor/                 # PHP dependencies
├── node_modules/           # JavaScript dependencies
├── .env                    # Environment variables
├── composer.json           # PHP dependencies
└── package.json            # JavaScript dependencies
```

---

## ✅ Verification Checklist

After setup, verify everything works:

- [ ] `php --version` shows 8.0.2+
- [ ] `composer --version` works
- [ ] `node --version` shows v14+
- [ ] `npm --version` works
- [ ] MySQL is running
- [ ] `.env` file exists and configured
- [ ] `php artisan serve` starts without errors
- [ ] http://localhost:8000 loads in browser
- [ ] Database tables created (check with `php artisan tinker`)
- [ ] Can login with default credentials

---

## 🚀 Next Steps

1. **Create Admin User:**
   ```bash
   php artisan tinker
   > $user = App\Models\User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password'), 'role' => 'admin']);
   > exit
   ```

2. **Login:** Use `admin@example.com` / `password`

3. **Change Password:** Update on first login

4. **Start Development:** Begin building features!

---

## 📞 Support

For issues:
1. Check the troubleshooting section above
2. Review Laravel docs: https://laravel.com/docs/9.x
3. Check GitHub issues: https://github.com/nealgod/OJT360/issues

---

**Happy coding! 🎉**
