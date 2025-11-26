# OJT360 - Tech Stack & APIs Documentation

**Last Updated:** November 25, 2025

---

## Table of Contents

1. [Backend Technologies](#backend-technologies)
2. [Frontend Technologies](#frontend-technologies)
3. [Database](#database)
4. [APIs & Endpoints](#apis--endpoints)
5. [External Libraries](#external-libraries)
6. [Development Tools](#development-tools)

---

## Backend Technologies

### Core Framework
- **Laravel 9.19+** - PHP web application framework
  - MVC architecture
  - Eloquent ORM for database operations
  - Blade templating engine
  - Built-in authentication system

### PHP Version
- **PHP 8.0.2+** - Server-side programming language

### Key Backend Libraries

#### PDF Generation
- **barryvdh/laravel-dompdf** (^2.2) - PDF generation from HTML
  - Used for: Weekly reports, evaluations, acceptance letters
  - Generates coordinate-based PDFs for 13 program templates

- **setasign/fpdf** (^1.8) - FPDF library for PDF creation
- **setasign/fpdi** (^2.6) - FPDI library for PDF manipulation
- **tecnickcom/tcpdf** (^6.10) - TCPDF library for advanced PDF features

#### Excel/Spreadsheet Processing
- **maatwebsite/excel** (3.1.50) - Excel import/export
  - Used for: Student whitelist imports, data exports
  
- **phpoffice/phpspreadsheet** (1.29) - Spreadsheet manipulation

#### HTTP Client
- **guzzlehttp/guzzle** (^7.2) - HTTP client for API requests
  - Used for: External API calls, webhooks

#### Database Tools
- **doctrine/dbal** (^3.10) - Database abstraction layer
  - Used for: Schema introspection, migrations

#### Authentication
- **laravel/sanctum** (^3.0) - API token authentication
  - Used for: API token-based authentication (if API routes added)

#### Development Tools
- **laravel/tinker** (^2.7) - Interactive PHP shell
  - Used for: Development debugging and testing

---

## Frontend Technologies

### JavaScript Framework
- **Alpine.js** (^3.4.2) - Lightweight JavaScript framework
  - Used for: Interactive UI components, form handling
  - Minimal footprint, no build step required

### CSS Framework
- **Tailwind CSS** (^3.1.0) - Utility-first CSS framework
  - Used for: Responsive design, styling
  - Configured with PostCSS

#### Tailwind Plugins
- **@tailwindcss/forms** (^0.5.2) - Form styling utilities

### Build Tools
- **Vite** (^4.0.0) - Next-generation frontend build tool
  - Used for: Asset bundling, hot module replacement
  - Faster than Webpack for development

- **laravel-vite-plugin** (^0.7.2) - Laravel integration for Vite

### CSS Processing
- **PostCSS** (^8.4.6) - CSS transformation tool
- **Autoprefixer** (^10.4.2) - Vendor prefix automation

### Utilities
- **Axios** (^1.1.2) - HTTP client for JavaScript
  - Used for: AJAX requests, API calls from frontend

- **Lodash** (^4.17.19) - JavaScript utility library
  - Used for: Array/object manipulation

- **pdf-lib** (^1.17.1) - PDF manipulation in JavaScript
  - Used for: Client-side PDF operations

---

## Database

### Database System
- **MySQL** (>= 5.7) or **MariaDB** (>= 10.3)

### Key Tables
- `users` - User accounts (students, coordinators, supervisors, admins)
- `student_profiles` - Student-specific information
- `coordinator_profiles` - Coordinator-specific information
- `supervisor_profiles` - Supervisor-specific information
- `attendance_logs` - Daily attendance records
- `weekly_reports` - Weekly report submissions
- `monthly_evaluations` - Monthly evaluation records
- `final_evaluations` - Final evaluation records
- `companies` - Company/employer information
- `departments` - Academic departments
- `programs` - Academic programs
- `acceptance_letters` - OJT acceptance letters
- `audit_logs` - System audit trail
- `student_document_submissions` - Document submissions
- `messages` - Internal messaging
- `notifications` - System notifications

---

## APIs & Endpoints

### Authentication Routes
```
GET  /login                                    - Login form
POST /login                                    - Process login
POST /logout                                   - Logout
GET  /register                                 - Registration form
POST /register                                 - Process registration
GET  /forgot-password                          - Forgot password form
POST /forgot-password                          - Send reset link
GET  /reset-password/{token}                   - Reset password form
POST /reset-password                           - Process password reset
```

### Supervisor Registration (Public)
```
GET  /register/supervisor                      - Supervisor registration form
POST /register/supervisor/send                 - Send verification email
GET  /register/supervisor/verify/{token}       - Verify email token
POST /register/supervisor/complete             - Complete registration
POST /register/supervisor/resend               - Resend verification email
```

### Student Registration
```
GET  /register/student                         - Student ID verification form
POST /register/student                         - Send verification
GET  /register/student/complete/{token}        - Complete registration form
POST /register/student/complete                - Process registration
```

### Coordinator Registration
```
GET  /register/coordinator/complete/{token}    - Complete coordinator registration
POST /register/coordinator/complete            - Process registration
POST /register/coordinator/resend              - Resend invitation
```

### Dashboard
```
GET  /dashboard                                - Main dashboard
```

### Profile Management
```
GET  /profile                                  - Edit profile form
PATCH /profile                                 - Update profile
DELETE /profile                                - Delete profile
```

### Attendance Management
```
GET  /attendance                               - View attendance logs
POST /attendance/time-in                       - Record time in
POST /attendance/time-out                      - Record time out
POST /attendance/recovery                      - Submit recovery request
POST /attendance/report-absence                - Report absence
GET  /api/attendance/{date}                    - Get attendance by date (JSON API)
```

### Weekly Reports
```
GET  /reports/weekly                           - List weekly reports
GET  /reports/weekly/create                    - Create report form
POST /reports/weekly                           - Store report
GET  /reports/weekly/{weekly}                  - View report
PATCH /reports/weekly/{weekly}/submit          - Submit report
DELETE /reports/weekly/{weekly}                - Delete report
GET  /reports/weekly/{weekly}/pdf              - Download PDF
```

### Evaluations
```
GET  /evaluations                              - View evaluation status
GET  /evaluations/final/status                 - View final evaluation status
```

### Documents & Requirements
```
GET  /documents                                - List document requirements
GET  /documents/{requirement}                  - View requirement
POST /documents/{requirement}/submit           - Submit document
DELETE /documents/submissions/{submission}/cancel - Cancel submission
GET  /documents/submissions/{submission}/download - Download submission
GET  /documents/submissions/{submission}/stream   - Stream submission
```

### Student Documents (Resume & Application Letter)
```
GET  /student-documents                        - List documents
GET  /student-documents/resume/create          - Create resume form
POST /student-documents/resume                 - Store resume
GET  /student-documents/resume/{resume}/edit   - Edit resume form
PATCH /student-documents/resume/{resume}       - Update resume
DELETE /student-documents/resume/{resume}      - Delete resume
GET  /student-documents/resume/{resume}/download - Download resume
POST /student-documents/resume/{resume}/submit - Submit resume

GET  /student-documents/application-letter/create - Create letter form
POST /student-documents/application-letter     - Store letter
GET  /student-documents/application-letter/{letter}/edit - Edit letter form
PATCH /student-documents/application-letter/{letter} - Update letter
DELETE /student-documents/application-letter/{letter} - Delete letter
GET  /student-documents/application-letter/{letter}/download - Download letter
POST /student-documents/application-letter/{letter}/submit - Submit letter
```

### Acceptance Letters
```
GET  /acceptance-letters/{letter}/download     - Download acceptance letter
```

### Messaging
```
GET  /messages                                 - List messages
GET  /messages/create                          - Create message form
POST /messages                                 - Send message
GET  /messages/{message}                       - View message
PATCH /messages/{message}/read                 - Mark as read
PATCH /messages/{message}/unread               - Mark as unread
DELETE /messages/{message}                     - Delete message
```

### Notifications
```
GET  /notifications                            - List notifications
PATCH /notifications/{notification}/read       - Mark as read
```

### Companies
```
GET  /companies                                - List companies
GET  /coord/companies/create                   - Create company form
POST /coord/companies                          - Store company
GET  /coord/companies/{company}/edit           - Edit company form
POST /coord/companies/{company}                - Update company
PATCH /coord/companies/{company}/toggle-status - Toggle company status
DELETE /coord/companies/{company}              - Delete company
```

### Supervisor Management (Coordinator)
```
GET  /coord/supervisors                        - List supervisors
GET  /coord/supervisors/create                 - Create supervisor form
POST /coord/supervisors                        - Store supervisor
```

### Supervisor Acceptance Letters
```
GET  /supervisor/acceptance-letters            - List acceptance letters
GET  /supervisor/students                      - List students
GET  /supervisor/students/search               - Search form
GET  /api/supervisor/students/autocomplete     - Autocomplete API
POST /supervisor/students/search               - Search students
GET  /supervisor/students/{student}            - View student
GET  /supervisor/students/{student}/accept     - Accept student form
POST /supervisor/students/{student}/generate-letter - Generate letter
GET  /supervisor/students/success/{letter}     - Success page
```

### Supervisor Evaluations
```
GET  /supervisor/evaluations                   - List evaluations
GET  /supervisor/evaluations/create/{student}  - Create evaluation form
POST /supervisor/evaluations                   - Store evaluation
GET  /supervisor/evaluations/{evaluation}      - View evaluation
GET  /supervisor/evaluations/{evaluation}/pdf  - Download PDF
```

### Supervisor Final Evaluations
```
GET  /supervisor/final-evaluations             - List evaluations
GET  /supervisor/final-evaluations/create/{student} - Create form
POST /supervisor/final-evaluations             - Store evaluation
GET  /supervisor/final-evaluations/{evaluation} - View evaluation
GET  /supervisor/final-evaluations/{evaluation}/pdf - Download PDF
```

### Coordinator Reports
```
GET  /coord/reports                            - List reports
GET  /coord/reports/{report}                   - View report
GET  /coord/reports/{report}/pdf               - Download PDF
PATCH /coord/reports/{report}/status           - Update status
```

### Coordinator Evaluations
```
GET  /coord/evaluations                        - List monthly evaluations
GET  /coord/evaluations/{evaluation}           - View evaluation
GET  /coord/evaluations/{evaluation}/pdf       - Download PDF
PATCH /coord/evaluations/{evaluation}/review   - Mark as reviewed

GET  /coord/final-evaluations                  - List final evaluations
GET  /coord/final-evaluations/{evaluation}     - View evaluation
GET  /coord/final-evaluations/{evaluation}/pdf - Download PDF
PATCH /coord/final-evaluations/{evaluation}/review - Mark as reviewed
```

### Coordinator Students
```
GET  /coord/students                           - List students
GET  /coord/students/import                    - Import form
POST /coord/students/import/preview            - Preview import
POST /coord/students/import/commit             - Commit import
GET  /coord/students/whitelist                 - Whitelist status
GET  /coord/students/whitelist/export          - Export whitelist
GET  /coord/students/whitelist/uploaded-file   - Download uploaded file
POST /coord/students/whitelist/end-term        - End term
GET  /coord/students/{student}                 - View student
POST /coord/students/{student}/update-company  - Update company
POST /coord/students/{student}/update-status   - Update status
POST /coord/students/{student}/assign-supervisor - Assign supervisor
```

### Coordinator Attendance
```
POST /coord/attendance/{log}/approve-recovery  - Approve recovery
POST /coord/attendance/{log}/reject-recovery   - Reject recovery
```

### Coordinator Documents
```
GET  /coord/documents                          - List documents
POST /coord/documents/submissions/{submission}/review - Review document
POST /coord/documents/bulk-review              - Bulk review
```

### Coordinator Program Management
```
GET  /coord/program/hours                      - View program hours
PATCH /coord/program/hours                     - Update program hours
```

### Admin Dashboard
```
GET  /admin/dashboard                          - Admin dashboard
```

### Admin User Management
```
GET  /admin/users                              - List users
GET  /admin/users/create                       - Create user form
POST /admin/users                              - Store user
```

### Admin Departments & Programs
```
GET  /admin/departments                        - List departments
POST /admin/departments                        - Create department
PUT  /admin/departments/{department}           - Update department
DELETE /admin/departments/{department}         - Delete department
POST /admin/departments/{department}/programs  - Create program
PUT  /admin/programs/{program}                 - Update program
DELETE /admin/programs/{program}               - Delete program
```

### Admin Reports
```
GET  /admin/reports                            - Reports dashboard
GET  /admin/reports/attendance                 - Attendance report
GET  /admin/reports/weekly                     - Weekly reports
GET  /admin/reports/evaluations                - Evaluations report
```

### Admin Audit Logs
```
GET  /admin/audit                              - List audit logs
GET  /admin/audit/{audit}                      - View audit log
```

### Student Placement
```
GET  /my-placement                             - View placement status
```

---

## External Libraries

### PDF Generation Libraries
- **FPDF** - Basic PDF generation
- **FPDI** - PDF manipulation and import
- **TCPDF** - Advanced PDF features
- **DomPDF** - HTML to PDF conversion

### Excel Processing
- **PhpSpreadsheet** - Excel file manipulation

### HTTP Communication
- **Guzzle** - HTTP client for external APIs

### Development & Testing
- **PHPUnit** (^9.5.10) - Unit testing framework
- **Mockery** (^1.4.4) - Mocking library
- **Faker** (^1.9.1) - Fake data generation
- **Laravel Pint** (^1.0) - Code style fixer
- **Laravel Sail** (^1.0.1) - Docker development environment
- **Collision** (^6.1) - Error display
- **Laravel Ignition** (^1.0) - Error debugging

---

## Development Tools

### Build & Asset Management
- **Vite** - Frontend build tool
- **PostCSS** - CSS processing
- **Autoprefixer** - CSS vendor prefixes

### Package Managers
- **Composer** - PHP dependency manager
- **NPM** - JavaScript package manager

### Code Quality
- **Laravel Pint** - PHP code style fixer
- **PHPUnit** - Unit testing

### Development Environment
- **Laravel Sail** - Docker-based development environment
- **Laravel Tinker** - Interactive shell

---

## API Response Format

### Success Response
```json
{
  "success": true,
  "data": {
    // Response data
  },
  "message": "Operation successful"
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    // Validation errors if applicable
  }
}
```

---

## Authentication Methods

### Session-Based (Web)
- Laravel session authentication
- CSRF token protection
- Email verification required

### Token-Based (API)
- Laravel Sanctum tokens
- Bearer token in Authorization header

---

## File Upload Handling

### Supported File Types
- **Images**: JPG, JPEG, PNG (for attendance photos)
- **Documents**: PDF, DOC, DOCX (for submissions)
- **Spreadsheets**: XLS, XLSX (for imports)

### Storage
- Local filesystem storage
- Public disk for downloadable files
- Private disk for sensitive documents

---

## Email Notifications

### Email Services
- SMTP configuration via `.env`
- Mailpit for local development
- Support for external mail providers

### Email Types
- User verification emails
- Supervisor registration invitations
- Coordinator invitations
- Notification emails
- Report submission confirmations

---

## Caching

### Cache Drivers
- File-based caching (default)
- Redis support (optional)
- Memcached support (optional)

### Cached Data
- Dashboard statistics
- User permissions
- Configuration data

---

## Session Management

### Session Driver
- File-based sessions (default)
- Database sessions (optional)
- Redis sessions (optional)

### Session Lifetime
- Default: 120 minutes
- Configurable via `.env`

---

## Security Features

### Built-in Security
- CSRF protection on all forms
- SQL injection prevention (Eloquent ORM)
- XSS protection (Blade escaping)
- Password hashing (bcrypt)
- Email verification
- Role-based access control

### Security Headers
- X-Frame-Options
- X-Content-Type-Options
- X-XSS-Protection

---

## Performance Optimization

### Caching Strategies
- Query result caching
- View caching
- Configuration caching

### Database Optimization
- Eager loading of relationships
- Query optimization
- Database indexing

### Frontend Optimization
- Asset minification via Vite
- CSS/JS bundling
- Image optimization

---

## Deployment Requirements

### Server Requirements
- PHP 8.0.2+
- MySQL 5.7+ or MariaDB 10.3+
- Composer
- Node.js & NPM (for asset building)

### Environment Variables
- `APP_KEY` - Application encryption key
- `DB_*` - Database credentials
- `MAIL_*` - Email configuration
- `APP_URL` - Application URL
- `APP_ENV` - Environment (local, production)

---

## Version Information

| Component | Version |
|-----------|---------|
| Laravel | 9.19+ |
| PHP | 8.0.2+ |
| MySQL | 5.7+ |
| Node.js | 14.x+ |
| Vite | 4.0.0+ |
| Tailwind CSS | 3.1.0+ |
| Alpine.js | 3.4.2+ |

---

## Additional Resources

- **Laravel Documentation**: https://laravel.com/docs/9.x
- **Tailwind CSS**: https://tailwindcss.com
- **Alpine.js**: https://alpinejs.dev
- **Vite**: https://vitejs.dev
- **MySQL**: https://dev.mysql.com/doc/

---

**End of Documentation**

