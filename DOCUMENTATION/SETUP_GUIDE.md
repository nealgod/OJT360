# OJT360 - Setup Guide

---

## Requirements
- PHP 8.0+, MySQL 5.7+, Composer, Node.js 14+
- Required PHP extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, GD

---

# Local Setup (Testing)

### 1. Extract & Navigate
```bash
# Extract to: C:\xampp\htdocs\ojt360
cd C:\xampp\htdocs\ojt360
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```
DB_DATABASE=ojt360
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Database
1. Create database `ojt360` in phpMyAdmin
2. Import `ojt360.sql`

### 5. Finalize
```bash
php artisan storage:link
npm run build
php artisan serve
```

Access: `http://localhost:8000`

---

# Production Deployment

### 1. Upload Files
- Upload `OJT360_Deploy.zip` to `public_html/`
- Extract all files

### 2. Create Database
1. cPanel → MySQL Databases
2. Create database: `username_ojt360`
3. Create user with strong password
4. Assign user to database (All Privileges)
5. **Save credentials**

### 3. Import Database
1. cPanel → phpMyAdmin
2. Select database → Import
3. Upload `ojt360.sql`

### 4. Configure Environment
Create `.env` file:
```
APP_NAME=OJT360
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_HOST=localhost
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_pass

MAIL_MAILER=smtp
MAIL_HOST=mail.your-domain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@your-domain.com
MAIL_PASSWORD=your_mail_pass
```

### 5. Generate App Key
**With SSH:**
```bash
php artisan key:generate
```

**Without SSH:**
Generate locally and copy to `.env`:
```bash
php artisan key:generate --show
```

### 6. Storage Link
**With SSH:**
```bash
php artisan storage:link
```

**Without SSH:**
Create `link.php` in public_html:
```php
<?php symlink('../storage/app/public', './storage'); echo 'Done!'; ?>
```
Visit once, then delete.

### 7. Optimize (Important!)
**With SSH:**
```bash
composer dump-autoload --optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Without SSH:**
Contact hosting to run these commands.

### 8. Set Permissions
Set to 755:
- `storage/` (all subfolders)
- `bootstrap/cache/`

### 9. Force HTTPS
Add to `.htaccess`:
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 10. Test
- Visit your domain
- Login with admin credentials
- Check all pages load

---

## Admin Login
- Email: `ojt3604dmin@gmail.com`
- Password: `12345678`
- **Change immediately after login**

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| 500 Error | Check `.env` exists, verify DB credentials |
| Blank Page | Check PHP 8.0+, run `composer dump-autoload` |
| CSS Missing | Ensure `public/build/` uploaded, `npm run build` |
| Images Not Showing | Verify storage link created |
| DB Error | Verify credentials in `.env` |
| Class Not Found | Run `composer dump-autoload` |

**Check logs:** `storage/logs/laravel.log`

---

## Post-Deployment

1. **Change admin password**
2. **Test email** sending
3. **Create coordinator** account
4. **Upload class list**
5. **Setup backups** (weekly database export)

---

## Source Code
GitHub: https://github.com/nealgod/OJT360
