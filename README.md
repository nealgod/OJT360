# OJT360

**On-the-Job Training Management System** - A comprehensive web-based platform for managing internship programs from pre-placement to completion, featuring automated workflows, real-time monitoring, and digital documentation.

[![Laravel](https://img.shields.io/badge/Laravel-9.52-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.0+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## 📋 Table of Contents

- [Overview](#overview)
- [Key Features](#key-features)
- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [User Roles & Workflows](#user-roles--workflows)
- [System Architecture](#system-architecture)
- [Technology Stack](#technology-stack)
- [Documentation](#documentation)

- [Support](#support)

---

## 🎯 Overview

OJT360 streamlines the entire OJT/internship lifecycle for educational institutions and companies. The system automates critical processes including:

- **Whitelist-based student activation** via class list uploads
- **Invitation-based coordinator onboarding**
- **Daily attendance tracking** with automatic hours calculation
- **Weekly progress reporting** with supervisor/coordinator feedback
- **Monthly and final evaluations** with PDF generation
- **Document management** with approval workflows
- **Real-time notifications** and internal messaging
- **MOA (Memorandum of Agreement) distribution** tracking

---

## ✨ Key Features

### 🔐 Authentication & Access Control
- **Whitelist-based student registration** - Coordinators upload class lists; students activate accounts via school email verification
- **Invitation-based coordinator registration** - Admins send secure invitations with token-based activation
- **Supervisor self-registration or invitation** - Flexible onboarding with email verification
- **Role-based access control (RBAC)** - 4 distinct roles: Admin, Coordinator, Supervisor, Student
- **Email verification** - Automatic verification during registration process
- **Password reset** - Secure password recovery via email

### 📝 Document Management
- **Pre-placement document submission** - Application letter, resume, PDS, clearances, medical certificate
- **Document approval workflow** - Coordinator review with approve/reject functionality
- **Acceptance letter generation** - Supervisor-issued acceptance with PDF export
- **Template-based PDF generation** - 13 program-specific acceptance letter templates
- **Secure file storage** - Documents stored in organized directories

### ⏰ Attendance System
- **Daily time in/out logging** - Simple one-click attendance tracking
- **Automatic hours calculation** - Break time deduction, overtime tracking
- **Attendance recovery** - Forgot to time out? Submit for supervisor approval
- **IP address verification** - Track location for attendance integrity
- **Progress tracking** - Real-time hours vs required hours comparison
- **Attendance history** - Detailed logs with date range filtering

### 📊 Reporting & Evaluation
- **Weekly reports** - Students document weekly accomplishments and learnings
- **Monthly evaluations** - Supervisors rate student performance (5 criteria)
- **Final evaluation** - Comprehensive end-of-OJT assessment (9 criteria)
- **PDF report generation** - Professional templates for all reports
- **Coordinator review workflow** - Review and approve submitted reports
- **Performance analytics** - Dashboard charts and progress indicators

### 🔔 Notifications & Communication
- **Real-time notifications** - In-app notifications for all key events
- **Email notifications** - Automatic emails for critical updates
- **MOA notification system** - Bulk notify students when MOAs are ready
- **Internal messaging** - Direct communication between users
- **Status updates** - Automatic notifications for status changes
- **Notification preferences** - Customizable notification settings

### 👥 User Management
- **Class list import** - Bulk upload students via Excel/CSV
- **Enrollment whitelist** - Pre-approved student roster with automatic validation
- **Supervisor assignment** - Link students to company supervisors
- **Company management** - Track active companies and supervisors
- **Department & program management** - Organize students by academic programs
- **Audit logging** - Track all system activities

---

## 🖥️ System Requirements

### Server Requirements
- **PHP**: >= 8.0.2
- **Composer**: Latest version
- **Node.js**: >= 14.x
- **NPM**: >= 6.x
- **MySQL**: >= 5.7 or **MariaDB**: >= 10.3
- **Web Server**: Apache or Nginx

### Required PHP Extensions
```
BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, 
Tokenizer, XML, GD (for image processing)
```

### Recommended Specifications
- **RAM**: 2GB minimum, 4GB recommended
- **Disk Space**: 1GB minimum (excluding user uploads)
- **SSL Certificate**: Required for production environments

---

## 📦 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/nealgod/OJT360.git
cd OJT360
```

### 2. Install Dependencies

**PHP Dependencies:**
```bash
composer install
```

**Node Dependencies:**
```bash
npm install
```

### 3. Environment Setup

Copy the environment file:
```bash
cp .env.example .env
```

Generate application key:
```bash
php artisan key:generate
```

### 4. Configure Environment

Edit `.env` file with your settings:

```env
APP_NAME=OJT360
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ojt360
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@evsu.edu.ph
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@ojt360.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 5. Database Setup

Create the database:
```sql
CREATE DATABASE ojt360 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Run migrations:
```bash
php artisan migrate
```

Seed the database:
```bash
php artisan db:seed
```

This creates:
- Default admin account
- Sample departments and programs
- Document requirements
- System configuration

### 6. Storage Setup

Create symbolic link for file storage:
```bash
php artisan storage:link
```

Set proper permissions:
```bash
chmod -R 775 storage bootstrap/cache
```

### 7. Build Frontend Assets

For development:
```bash
npm run dev
```

For production:
```bash
npm run build
```

### 8. Start Development Server

```bash
php artisan serve
```

Access the application at: `http://localhost:8000`

**Default Admin Credentials:**
- Email: `ojt3604dmin@gmail.com`
- Password: `12345678`

---

## ⚙️ Configuration

### Timezone Settings
Set your timezone in `config/app.php`:
```php
'timezone' => 'Asia/Manila',
```


### Email Configuration
For Gmail, create an app-specific password:
1. Enable 2FA on your Google account
2. Generate app password: https://myaccount.google.com/apppasswords
3. Use app password in `MAIL_PASSWORD`

### Break Time Settings
Default break time deduction is set per acceptance letter. Configure in supervisor acceptance form.

---

## 👥 User Roles & Workflows

### 1. **Admin**
**Responsibilities:**
- System-wide configuration
- User management (all roles)
- Department & program management
- Send coordinator invitations
- Monitor system analytics


**Key Actions:**
- Create coordinator invitations
- Manage departments and programs
- View audit logs
- Configure system settings

---

### 2. **Coordinator** (Program/Department Level)

**Registration:** Invitation-based (Admin sends invitation email)

**Responsibilities:**
- Upload class lists for student activation
- Review student documents
- Monitor student progress
- Review weekly reports
- Notify students about MOA collection
- Set program required hours
- Generate program reports

**Workflow:**
```
1. Receive invitation from Admin → Complete registration
2. Upload class list (Excel/CSV) → Students can now activate accounts
3. Students submit documents → Review
4. Students get acceptance letters → Notify about MOA
5. Activate student OJT status → Students log attendance
6. Monitor daily: review reports and student progress
7. End of OJT: Review final evaluations
```

**Critical First Step:**
⚠️ **Upload class list FIRST** - Students cannot activate accounts until coordinator uploads their class list!

---

### 3. **Supervisor** (Company Side)

**Registration:** 
- Option A: Self-registration with email verification
- Option B: Invitation from coordinator

**Responsibilities:**
- Issue acceptance letters to students
- Monitor student daily attendance
- Monitor attendance recovery requests
- Review weekly reports
- Submit monthly evaluations
- Submit final evaluation at OJT completion
- Provide feedback and guidance

**Workflow:**
```
1. Register/Accept invitation → Complete profile
2. Search for student → Issue acceptance letter
3. Student starts OJT → Monitor attendance
4. Weekly: Review student reports
5. Monthly: Submit performance evaluation
6. End of OJT: Submit final comprehensive evaluation
```

---

### 4. **Student (Intern)**

**Registration:** Whitelist-based activation

**Pre-requisite:** Coordinator must upload class list first!

**OJT Journey:**
```
┌─────────────────────────────────────┐
│ PHASE 1: PRE-PLACEMENT              │
└─────────────────────────────────────┘
1. Account Activation
   - Enter Student ID → Receive email → Complete registration

2. Submit Required Documents
   - Application Letter, Resume, PDS
   - Medical Certificate, Clearances
   - Parent's Consent
3. Get Acceptance Letter
   - Find company placement
   - Supervisor issues acceptance letter
   - Download acceptance letter PDF

4. MOA Collection
   - Wait for coordinator MOA notification
   - Collect MOA from coordinator

┌─────────────────────────────────────┐
│ PHASE 2: ACTIVE OJT                 │
└─────────────────────────────────────┘
5. Daily Attendance
   - Time In when you arrive
   - Time Out when you leave
   - System auto-calculates hours

6. Weekly Reports
   - Submit weekly accomplishments
   - Document learnings and challenges
   - Receive coordinator feedback

7. Monthly Evaluations
   - Supervisor submits monthly ratings
   - View performance feedback
   - Track improvement areas

┌─────────────────────────────────────┐
│ PHASE 3: COMPLETION                 │
└─────────────────────────────────────┘
8. Final Evaluation
   - Complete required hours
   - Supervisor submits final evaluation
   - Download completion certificates

9. OJT Status → Completed ✅
```

---

## 🏗️ System Architecture

### High-Level Architecture
```
┌───────────────────────────────────────────────────────┐
│              PRESENTATION LAYER                       │
│  ┌─────────┐ ┌──────────┐ ┌──────────┐ ┌─────────┐ │
│  │ Student │ │Coordinator│ │Supervisor│ │  Admin  │ │
│  │   UI    │ │    UI    │ │    UI    │ │   UI    │ │
│  └─────────┘ └──────────┘ └──────────┘ └─────────┘ │
└───────────────────────────────────────────────────────┘
                         ↓
┌───────────────────────────────────────────────────────┐
│              APPLICATION LAYER                        │
│  ┌──────────────┐        ┌──────────────┐           │
│  │ Controllers  │←──────→│  Middleware  │           │
│  └──────────────┘        └──────────────┘           │
│         ↓                        ↓                    │
│  ┌──────────────┐        ┌──────────────┐           │
│  │   Services   │        │Notifications │           │
│  └──────────────┘        └──────────────┘           │
└───────────────────────────────────────────────────────┘
                         ↓
┌───────────────────────────────────────────────────────┐
│                 DATA LAYER                            │
│  ┌──────────────┐        ┌──────────────┐           │
│  │  Eloquent    │←──────→│    MySQL     │           │
│  │     ORM      │        │   Database   │           │
│  └──────────────┘        └──────────────┘           │
│                                                       │
│  ┌──────────────┐                                    │
│  │ File Storage │ (PDFs, Documents, Images)         │
│  └──────────────┘                                    │
└───────────────────────────────────────────────────────┘
```

### Key Database Tables
- `users` - All system users
- `student_profiles` - Student-specific data
- `coordinator_profiles` - Coordinator-specific data
- `supervisor_profiles` - Supervisor-specific data
- `enrollment_whitelist` - Class list for student activation
- `coordinator_invitations` - Pending coordinator invitations
- `attendance_logs` - Daily time in/out records
- `weekly_reports` - Student weekly submissions
- `monthly_evaluations` - Monthly performance ratings
- `final_evaluations` - End-of-OJT assessments
- `acceptance_letters` - Supervisor-issued acceptances
- `documents` - File uploads and status
- `notifications` - System notifications

---

## 🛠️ Technology Stack

### Backend
- **Framework:** Laravel 9.52.21
- **Language:** PHP 8.0+
- **Database:** MySQL 8.0 / MariaDB 10.3+
- **Authentication:** Laravel Breeze (session-based)
- **ORM:** Eloquent

### Frontend
- **CSS Framework:** Tailwind CSS 3.x
- **JavaScript:** Alpine.js 3.x
- **Templating:** Blade
- **Build Tool:** Vite 3.x
- **Icons:** Heroicons

### PDF Generation
- **FPDF** 1.8 - Simple PDFs
- **FPDI** 2.6 - PDF templates & overlays
- **TCPDF** 6.10 - Advanced PDF features
- **DomPDF** 2.2 - HTML to PDF conversion

### Additional Libraries
- **Maatwebsite Excel** 3.1.50 - Excel import/export
- **Guzzle HTTP** 7.2 - HTTP client
- **Carbon** - Date/time manipulation
- **Laravel Notifications** - Multi-channel notifications

### Development Tools
- **Composer** - PHP dependency management
- **NPM** - JavaScript package management
- **Git** - Version control

---

## 📂 Project Structure

```
ojt360/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Application controllers
│   │   │   ├── Auth/             # Authentication controllers
│   │   │   ├── Admin/            # Admin controllers
│   │   │   ├── Coordinator/      # Coordinator controllers
│   │   │   ├── Supervisor/       # Supervisor controllers
│   │   │   └── Student/          # Student controllers
│   │   └── Middleware/           # Custom middleware
│   ├── Models/                   # Eloquent models
│   ├── Services/                 # Business logic services
│   │   └── WeeklyReportPdfService.php
│   ├── Notifications/            # Notification classes
│   └── Support/                  # Helper classes
│       └── ProgramCodeResolver.php
├── config/                       # Configuration files
├── database/
│   ├── migrations/               # Database migrations
│   └── seeders/                  # Database seeders
├── resources/
│   ├── views/                    # Blade templates
│   │   ├── admin/
│   │   ├── coordinator/
│   │   ├── supervisor/
│   │   ├── student/
│   │   └── layouts/
│   ├── templates/                # PDF templates (13 programs)
│   └── css/                      # Tailwind CSS
├── routes/
│   ├── web.php                   # Web routes
│   ├── api.php                   # API routes (if any)
│   └── auth.php                  # Authentication routes
├── storage/
│   ├── app/
│   │   ├── documents/            # Uploaded documents
│   │   ├── pdfs/                 # Generated PDFs
│   │   └── public/               # Publicly accessible files
│   └── logs/                     # Application logs
├── public/                       # Public assets
│   ├── css/
│   ├── js/
│   └── images/
├── tests/                        # PHPUnit tests
├── README.md                     # This file
├── DOCUMENTATION/             # System documentation
│   ├── SYSTEM_FLOW.md        # Detailed system workflows
│   ├── USER_MANUAL.md        # Complete user guide
│   ├── ADMIN_MANAGEMENT_GUIDE.md # Admin credentials guide
│   └── ATTENDANCE_STATUS_EXPLANATION.md # Attendance logic guide
└── app/
```

---

## 📚 Documentation

The project includes comprehensive documentation:

### 📄 [SYSTEM_FLOW.md](DOCUMENTATION/SYSTEM_FLOW.md)
Detailed technical documentation covering:
- System architecture diagrams
- Authentication flows (student, coordinator, supervisor)
- Complete OJT journey workflow
- Coordinator management workflows
- Supervisor workflows
- Data flow diagrams
- Notification system architecture

### 📖 [USER_MANUAL.md](DOCUMENTATION/USER_MANUAL.md)
Complete user guide for all roles:
- Getting started & account activation
- Step-by-step guides for each user role
- Common tasks and troubleshooting
- FAQ and support information

### 🔐 [ADMIN_MANAGEMENT_GUIDE.md](DOCUMENTATION/ADMIN_MANAGEMENT_GUIDE.md)
Technical guide for managing Master Admin credentials and database seeders.

### ⏰ [ATTENDANCE_STATUS_EXPLANATION.md](DOCUMENTATION/ATTENDANCE_STATUS_EXPLANATION.md)
Explanation of the 'In Progress' vs 'Completed' attendance status logic.

### 🛠️ [SETUP_GUIDE.md](DOCUMENTATION/SETUP_GUIDE.md)
Detailed hosting, turnover, and production deployment instructions.

### 📋 README.md (This File)
- Installation and setup
- System overview
- Technology stack
- Quick reference guide

**To get started:**
1. Read this README for installation
2. Check `DOCUMENTATION/SYSTEM_FLOW.md` to understand workflows
3. Refer to `DOCUMENTATION/USER_MANUAL.md` for detailed user instructions

## 🔒 Security Features

- **Email Verification** - All users must verify email addresses
- **CSRF Protection** - Laravel's built-in CSRF tokens
- **SQL Injection Prevention** - Eloquent ORM parameterized queries
- **XSS Protection** - Blade template escaping
- **Role-Based Access Control** - Middleware authorization
- **Password Hashing** - Bcrypt password encryption
- **Secure File Upload** - File type and size validation
- **Session Security** - Secure session management
- **IP Verification** - Optional IP-based attendance verification

---

## 🚀 Deployment

### Production Checklist
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Configure production database
- [ ] Set up proper mail server (SMTP)
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Run `npm run build` for production assets
- [ ] Configure SSL certificate
- [ ] Set proper file permissions (755 for directories, 644 for files)
- [ ] Configure backup strategy
- [ ] Set up monitoring and logging

### Deploy to Production Server

**Important:** Ensure your web server's **Document Root** points to the `/public` directory.

Run these commands after following the standard installation steps (cloning, dependencies, env setup):

```bash
# Optimize for production
php artisan optimize

# Build assets
npm run build

# Clear and cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🐛 Troubleshooting

### Common Issues

**1. "Class not found" errors**
```bash
composer dump-autoload
```

**2. Permission denied errors**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

**3. Database connection failed**
- Check `.env` database credentials
- Ensure MySQL is running
- Test connection: `php artisan tinker` then `DB::connection()->getPdo();`

**4. Email not sending**
- Verify MAIL_* settings in `.env`
- Check spam folder
- Test with `php artisan tinker` and `Mail::raw('Test', function($m) { $m->to('test@example.com')->subject('Test'); });`

**5. Vite asset errors**
```bash
npm run dev
# In another terminal:
php artisan serve
```

---

## 🤝 Contributing

We welcome contributions! To contribute:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

**Coding Standards:**
- Follow PSR-12 for PHP
- Use Laravel best practices
- Write descriptive commit messages
- Add tests for new features
- Update documentation as needed

---



## 👨‍💻 Author

**Neal God**
- GitHub: [@nealgod](https://github.com/nealgod)
- Repository: [OJT360](https://github.com/nealgod/OJT360)
- Email: gasalneal09123@gmail.com

---

## 🙏 Acknowledgments

- Laravel team for the amazing framework
- Tailwind CSS for the utility-first CSS framework
- Alpine.js for lightweight reactivity

---

## 📞 Support

### Getting Help

- **📖 Documentation**: See [USER_MANUAL.md](DOCUMENTATION/USER_MANUAL.md) and [SYSTEM_FLOW.md](DOCUMENTATION/SYSTEM_FLOW.md)
- **🐛 Issues**: [GitHub Issues](https://github.com/nealgod/OJT360/issues)
- **💬 Discussions**: [GitHub Discussions](https://github.com/nealgod/OJT360/discussions)

### For Users
- **Students**: Contact your coordinator
- **Coordinators**: Contact system admin
- **Supervisors**: Contact the coordinator who invited you
---


