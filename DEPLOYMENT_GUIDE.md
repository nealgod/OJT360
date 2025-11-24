# Deployment Guide

**Date:** November 24, 2025  
**Application:** OJT Management System  
**Version:** 1.0 Production Ready

---

## 📋 Pre-Deployment Checklist

### Code Verification
- ✅ All code committed to git
- ✅ All tests passing
- ✅ No debug statements
- ✅ No TODO markers
- ✅ All migrations ready

### Environment Setup
- ✅ Production .env file prepared
- ✅ Database credentials secured
- ✅ API keys configured
- ✅ Mail service configured
- ✅ File storage configured

### Infrastructure
- ✅ Server provisioned
- ✅ PHP 8.1+ installed
- ✅ MySQL 5.7+ installed
- ✅ Composer installed
- ✅ Git installed

---

## 🚀 Deployment Steps

### Step 1: Clone Repository

```bash
cd /var/www
git clone https://github.com/your-org/ojt-system.git
cd ojt-system
```

### Step 2: Install Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

### Step 3: Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Edit .env with production values
nano .env
```

**Required .env Variables:**
```
APP_NAME="OJT Management System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ojt_system
DB_USERNAME=ojt_user
DB_PASSWORD=secure_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@your-domain.com

FILESYSTEM_DISK=local
```

### Step 4: Generate Application Key

```bash
php artisan key:generate
```

### Step 5: Create Database

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE ojt_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Create user
mysql -u root -p -e "CREATE USER 'ojt_user'@'localhost' IDENTIFIED BY 'secure_password';"

# Grant privileges
mysql -u root -p -e "GRANT ALL PRIVILEGES ON ojt_system.* TO 'ojt_user'@'localhost';"
mysql -u root -p -e "FLUSH PRIVILEGES;"
```

### Step 6: Run Migrations

```bash
php artisan migrate --force
```

### Step 7: Seed Database (Optional)

```bash
php artisan db:seed --class=DocumentRequirementsSeeder
```

### Step 8: Set Permissions

```bash
# Set directory permissions
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Set ownership
chown -R www-data:www-data /var/www/ojt-system
```

### Step 9: Optimize for Production

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### Step 10: Configure Web Server

#### Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/ojt-system/public;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### Apache Configuration

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/ojt-system/public

    <Directory /var/www/ojt-system>
        AllowOverride All
        Require all granted
    </Directory>

    <FilesMatch \.php$>
        SetHandler "proxy:unix:/var/run/php/php8.2-fpm.sock|fcgi://localhost"
    </FilesMatch>

    ErrorLog ${APACHE_LOG_DIR}/ojt-system-error.log
    CustomLog ${APACHE_LOG_DIR}/ojt-system-access.log combined
</VirtualHost>
```

### Step 11: Enable HTTPS (SSL)

```bash
# Using Let's Encrypt with Certbot
sudo certbot certonly --webroot -w /var/www/ojt-system/public -d your-domain.com

# Update Nginx/Apache to use SSL
# (Add SSL certificate paths to your web server config)
```

### Step 12: Set Up Cron Job

```bash
# Edit crontab
crontab -e

# Add Laravel scheduler
* * * * * cd /var/www/ojt-system && php artisan schedule:run >> /dev/null 2>&1
```

### Step 13: Configure Logging

```bash
# Create log directory
mkdir -p /var/log/ojt-system
chown www-data:www-data /var/log/ojt-system

# Update .env
LOG_CHANNEL=stack
LOG_PATH=/var/log/ojt-system
```

### Step 14: Set Up Monitoring

```bash
# Install monitoring tools (optional)
# Examples: New Relic, Datadog, Scout, etc.

# Configure error tracking (optional)
# Examples: Sentry, Rollbar, Bugsnag, etc.
```

### Step 15: Verify Installation

```bash
# Check Laravel installation
php artisan tinker

# Run migrations status
php artisan migrate:status

# Check routes
php artisan route:list

# Test email
php artisan tinker
>>> Mail::raw('Test', function($message) { $message->to('test@example.com'); });
```

---

## 🔍 Post-Deployment Verification

### 1. Check Application Status

```bash
# Visit application
curl https://your-domain.com

# Check status page
curl https://your-domain.com/health
```

### 2. Verify Database

```bash
# Check migrations
php artisan migrate:status

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

### 3. Test Authentication

1. Visit login page
2. Create test user
3. Test login/logout
4. Verify session

### 4. Test Key Features

- [ ] Admin dashboard loads
- [ ] Department management works
- [ ] Reports display correctly
- [ ] Audit logs record actions
- [ ] Email notifications send
- [ ] File uploads work
- [ ] PDF generation works
- [ ] Attendance tracking works

### 5. Monitor Logs

```bash
# Watch error logs
tail -f /var/log/ojt-system/laravel.log

# Watch web server logs
tail -f /var/log/nginx/ojt-system-error.log
```

---

## 🛡️ Security Hardening

### 1. Update Security Headers

```bash
# In .env
APP_DEBUG=false
SESSION_SECURE_COOKIES=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict
```

### 2. Configure Firewall

```bash
# Allow only necessary ports
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### 3. Set Up Fail2Ban

```bash
# Install
sudo apt-get install fail2ban

# Configure
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local
sudo systemctl restart fail2ban
```

### 4. Regular Backups

```bash
# Create backup script
#!/bin/bash
BACKUP_DIR="/backups/ojt-system"
DATE=$(date +%Y%m%d_%H%M%S)

# Backup database
mysqldump -u ojt_user -p ojt_system > $BACKUP_DIR/db_$DATE.sql

# Backup files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/ojt-system

# Upload to cloud storage (optional)
# aws s3 cp $BACKUP_DIR s3://your-bucket/backups/
```

---

## 📊 Monitoring & Maintenance

### Daily Tasks
- [ ] Check error logs
- [ ] Monitor disk space
- [ ] Monitor database size
- [ ] Check backup status

### Weekly Tasks
- [ ] Review audit logs
- [ ] Check performance metrics
- [ ] Review user feedback
- [ ] Update dependencies (if needed)

### Monthly Tasks
- [ ] Security audit
- [ ] Performance optimization
- [ ] Database maintenance
- [ ] Backup verification

### Quarterly Tasks
- [ ] Full security review
- [ ] Code review
- [ ] Update dependencies
- [ ] Disaster recovery test

---

## 🚨 Troubleshooting

### Application Won't Start

```bash
# Check permissions
ls -la /var/www/ojt-system/storage
ls -la /var/www/ojt-system/bootstrap/cache

# Check logs
tail -f /var/log/ojt-system/laravel.log

# Clear cache
php artisan cache:clear
php artisan config:clear
```

### Database Connection Error

```bash
# Test connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check credentials in .env
cat .env | grep DB_

# Verify database exists
mysql -u ojt_user -p -e "SHOW DATABASES;"
```

### Email Not Sending

```bash
# Test mail configuration
php artisan tinker
>>> Mail::raw('Test', function($message) { $message->to('test@example.com'); });

# Check mail logs
tail -f /var/log/ojt-system/laravel.log | grep -i mail
```

### High Memory Usage

```bash
# Check running processes
ps aux | grep php

# Clear caches
php artisan cache:clear
php artisan view:clear

# Optimize autoloader
composer dump-autoload --optimize
```

---

## 📞 Support & Escalation

### Level 1: Self-Service
1. Check logs
2. Review documentation
3. Check status page
4. Restart services

### Level 2: Team Support
1. Contact development team
2. Provide error logs
3. Provide reproduction steps
4. Provide system info

### Level 3: Emergency
1. Contact senior developer
2. Prepare rollback plan
3. Notify stakeholders
4. Document incident

---

## ✅ Deployment Checklist

- [ ] Code committed and pushed
- [ ] Dependencies installed
- [ ] Environment configured
- [ ] Database created and migrated
- [ ] Permissions set correctly
- [ ] Web server configured
- [ ] SSL certificate installed
- [ ] Cron job configured
- [ ] Logging configured
- [ ] Monitoring set up
- [ ] Backups configured
- [ ] Security hardened
- [ ] All tests passing
- [ ] Documentation updated
- [ ] Team notified

---

## 🎉 Deployment Complete!

Your OJT Management System is now live in production.

**Important Reminders:**
- Monitor logs regularly
- Keep backups updated
- Update dependencies periodically
- Review security regularly
- Gather user feedback
- Plan maintenance windows

---

**Deployment Date:** November 24, 2025  
**Status:** ✅ READY FOR PRODUCTION  
**Support:** Available 24/7
