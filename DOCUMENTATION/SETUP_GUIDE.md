# OJT360 - Setup Guide

This guide provides instructions for both local testing and production deployment.

---

## Installation Source
The application is deployed directly from the official repository:
- **Repository**: [https://github.com/nealgod/OJT360](https://github.com/nealgod/OJT360)
- **Branch**: `main` (or the specific release tag)

*Note: This guide assumes you are pulling the latest code directly from GitHub.*

---

##  Technical Stack & Requirements

### **Core Technologies**
- **Backend Framework**: [Laravel 9.x](https://laravel.com/) (PHP)
- **Frontend Engine**: [Vite](https://vitejs.dev/)
- **UI Framework**: [Tailwind CSS 3.x](https://tailwindcss.com/) & [Alpine.js 3.x](https://alpinejs.dev/)
- **Database**: MySQL 5.7+ / MariaDB 10.3+

### **Server Requirements**
- **PHP Engine**: ^8.0.2 (Optimized for 8.1+)
- **Required Extensions**: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, GD Library (for profile photos).

### **Primary Libraries & Integrations**
- **PDF Processing**: `barryvdh/laravel-dompdf`, `tecnickcom/tcpdf`, `setasign/fpdi` (Used for MOA generation, Evaluations, and Certificates).
- **Excel/Data Exports**: `maatwebsite/excel` & `phpoffice/phpspreadsheet`.
- **API & Requests**: `guzzlehttp/guzzle` & `axios`.
- **API Endpoints**: Includes internal API routes (e.g., `/api/attendance/{date}`) used for dynamic UI updates via Axios.
- **Frontend Logic**: `alpinejs` & `lodash`.

---

##  Local Setup (Development)

### 1. Clone & Navigate
```bash
git clone https://github.com/nealgod/OJT360.git
cd OJT360
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment & Key
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup
1. Create a database named `ojt360` in phpMyAdmin.
2. **Option A (Preferred)**: Run `php artisan migrate --seed`. This builds the latest schema and initial data automatically.
3. **Option B (Manual)**: Import the provided `ojt360.sql`.

### 5. Finalize
```bash
php artisan storage:link
npm run dev # or npm run build
php artisan serve
```

---

##  Production Deployment (Hosting)

### 1. Get the Code
- **Clone from GitHub**:
  ```bash
  git clone https://github.com/nealgod/OJT360.git
  ```
- **Document Root**: **IMPORTANT:** Point your domain's Document Root to the `/public` directory (e.g., `/home/user/public_html/OJT360/public`).

### 2. Database Creation
- Create a MySQL Database via your Control Panel (cPanel/Namecheap).
- Create a Database User and assign it to the database with all privileges.

### 3. Database Migration
- Run the standard migration command to build the schema:
  ```bash
  php artisan migrate --seed
  ```
- *Note: This will automatically create all 29 tables and active the system.*

### 4. Environment Configuration
Edit the `.env` file with your production details:

```ini
APP_NAME=OJT360
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_DATABASE=your_production_db
DB_USERNAME=your_production_user
DB_PASSWORD=your_production_password

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=mail.your-domain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@your-domain.com
MAIL_PASSWORD=your_mail_pass
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 5. Post-Installation Optimization
Run these commands via SSH to ensure the system is locked down and optimized:
```bash
# Generate the application key (if not already set)
php artisan key:generate

# Set Directory Permissions (Important for Shared Hosting)
chmod -R 775 storage bootstrap/cache

# Link the storage directory
php artisan storage:link

# Optimization & Caching
php artisan config:cache
php artisan route:cache
php artisan view:cache

```

### 6. Robust Migration Execution
The system includes "Safety-Wrapped" migrations. If you need to update the database schema later, simply run:
```bash
php artisan migrate
```
*These migrations are fortified with try-catch blocks to ensure they handle foreign key conflicts gracefully on production environments.*

### 7. Force HTTPS
Add the following lines to the top of your `.htaccess` file (inside the `public` directory) to ensure all traffic is secured:
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

##  Admin Security
- **Initial Login**: `ojt3604dmin@gmail.com` | `12345678`
- **Important**: Refer to the **Administrator Management & Security** guide to secure the account immediately after your first login.

---

## 🛠️ Troubleshooting

| Issue | Cause | Solution |
|-------|-------|----------|
| 500 Internals | Permission or .env | Ensure `storage` and `bootstrap/cache` are 775. Check DB credentials. |
| Migration Error | Production DB State | Safety wrappers handle most cases. Run `migrate` again. |
| Missing Photos | Storage Link | Re-run `php artisan storage:link`. |
| CSS/JS Not Loading | Build Missing | Ensure `public/build` exists or run `npm run build`. |

---

##  Official Repository
For the latest updates and version history:
[https://github.com/nealgod/OJT360](https://github.com/nealgod/OJT360)
