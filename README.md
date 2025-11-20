# OJT360

End-to-end web-based internship monitoring and management system with automated documentation and evaluation workflow.

## 📋 Table of Contents

- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Running the Application](#running-the-application)
- [User Roles](#user-roles)
- [Key Features](#key-features)
- [Development](#development)
- [Testing](#testing)

---

## 🖥️ System Requirements

- **PHP**: >= 8.0.2
- **Composer**: Latest version
- **Node.js**: >= 14.x
- **NPM**: >= 6.x
- **MySQL**: >= 5.7 or MariaDB >= 10.3
- **Web Server**: Apache or Nginx

### Required PHP Extensions
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- GD (for image processing)

---

## 📦 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/nealgod/OJT360.git
cd OJT360
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

---

## ⚙️ Configuration

Edit the `.env` file with your settings:

- **APP_NAME** - Application name
- **APP_URL** - Your application URL
- **DB_*** - Database connection settings
- **MAIL_*** - Email configuration for notifications
- **Timezone** - Set in `config/app.php` (default: Asia/Manila)

---

## 🗄️ Database Setup

### 1. Create Database

Create a MySQL database named `ojt360` (or your preferred name):

```sql
CREATE DATABASE ojt360 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Run Migrations

```bash
php artisan migrate
```

### 3. Seed Database

```bash
php artisan db:seed
```

This will create:
- Default admin account
- Sample departments and programs
- Document requirements
- Initial system data

### 4. Link Storage

```bash
php artisan storage:link
```

---

## 🚀 Running the Application

### Development Server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

### Build Frontend Assets

For development (with hot reload):
```bash
npm run dev
```

For production:
```bash
npm run build
```

### Watch for Changes

```bash
npm run watch
```

---

## 👥 User Roles

The system supports four user roles:

### 1. **Admin**
- Full system access
- User management
- System configuration

### 2. **Coordinator**
- Manage students in their program
- Approve placement requests
- Monitor student progress
- Set program required hours
- Review weekly reports

### 3. **Supervisor**
- Register via email verification
- Monitor assigned students
- Review attendance and reports
- Provide feedback
- Generate acceptance letters

### 4. **Intern (Student)**
- Submit placement requests
- Log daily attendance with photo proof
- Submit weekly reports
- Upload required documents
- Track OJT progress
- Communicate with supervisors

---

## ✨ Key Features

### Attendance Management
- Daily time in/out with photo verification
- GPS location tracking
- Automatic break time deduction
- Overtime calculation
- Attendance history and reports

### Weekly Reports
- Date range selection (up to 7 days)
- Daily activity logging
- Learnings and challenges documentation
- Supervisor and coordinator feedback
- PDF generation with official template
- Validation for incomplete attendance

### Document Management
- Resume/CV upload and management
- Application letter generation
- Acceptance letter generation
- Program-specific templates (13 programs)
- PDF generation with coordinate-based positioning

### Placement System
- Student placement requests
- Coordinator approval workflow
- Company assignment
- Supervisor assignment
- Acceptance letter generation

### Communication
- Internal messaging system
- Real-time notifications
- Email notifications
- Status updates

### Progress Tracking
- Hours worked calculation
- Progress percentage
- Required hours vs completed hours
- Visual progress indicators
- Dashboard analytics

---

## 🛠️ Development

### Key Technologies

**Backend:**
- Laravel 9.52.21
- PHP 8.0+
- MySQL

**Frontend:**
- Tailwind CSS
- Alpine.js
- Blade Templates

**PDF Generation:**
- FPDF 1.8
- FPDI 2.6
- TCPDF 6.10
- DomPDF 2.2

**Other Libraries:**
- Maatwebsite Excel 3.1.50
- Guzzle HTTP 7.2
- Laravel Breeze (Authentication)

### Project Structure

```
ojt360/
├── app/
│   ├── Http/Controllers/     # Application controllers
│   ├── Models/               # Eloquent models
│   ├── Services/             # Business logic services
│   └── Support/              # Helper classes
├── database/
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── resources/
│   ├── views/                # Blade templates
│   └── templates/            # PDF templates
├── routes/
│   ├── web.php              # Web routes
│   └── api.php              # API routes
└── public/                   # Public assets
```

### Important Files

- **WeeklyReportController**: Handles weekly report CRUD
- **WeeklyReportPdfService**: Generates PDF reports
- **SupervisorAcceptanceController**: Manages acceptance letters
- **ProgramCodeResolver**: Resolves student program codes

---

## 🧪 Testing

### Quick Attendance Test

Use Tinker to manually set attendance for testing:

```bash
php artisan tinker
```

```php
$email = 'student@example.com';
$date = now()->format('Y-m-d');
$in   = '07:00:00';
$out  = '17:30:00';

$user = App\Models\User::where('email', $email)->firstOrFail();
$log = App\Models\AttendanceLog::firstOrCreate(
    ['student_user_id' => $user->id, 'work_date' => \Carbon\Carbon::parse($date)],
    ['company_id' => optional($user->studentProfile)->assigned_company_id]
);
$log->time_in = $in;
$log->time_out = $out;

$acceptance = App\Models\AcceptanceLetter::where('student_user_id', $user->id)
    ->latest()
    ->first();
$break = $acceptance && isset($acceptance->work_schedule['break_minutes']) 
    ? (int)$acceptance->work_schedule['break_minutes'] 
    : 0;

$tz = 'Asia/Manila';
$timeIn = \Carbon\Carbon::parse("{$date} {$in}", $tz);
$timeOut = \Carbon\Carbon::parse("{$date} {$out}", $tz);
$total = max(0, $timeIn->diffInMinutes($timeOut));
$minutes = max(0, $total - $break);

$log->minutes_worked = $minutes;
$log->save();
```

---

## 📝 Additional Notes

### Security Features
- Email verification required
- First-time password change enforced
- Role-based access control
- CSRF protection
- SQL injection prevention

### Break Time Handling
- Automatic break deduction once per day
- Configurable per acceptance letter
- Default break time if not specified

### PDF Templates
- 13 program-specific acceptance letter templates
- 1 weekly report template
- Located in `resources/templates/`
- Coordinate-based field positioning

### Future Features
- Shift start/end times (schema ready, UI hidden)
- Working days configuration (schema ready, UI hidden)
- Monthly report aggregation
- Advanced analytics dashboard

---

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

This project is licensed under the MIT License.

---

## 👨‍💻 Author

**Neal God**
- GitHub: [@nealgod](https://github.com/nealgod)
- Repository: [OJT360](https://github.com/nealgod/OJT360)

---

## 🆘 Support

For issues, questions, or contributions, please open an issue on GitHub.

---

**Last Updated:** November 20, 2025
