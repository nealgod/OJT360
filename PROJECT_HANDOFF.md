# OJT360 Project - Continuation Handoff Document

## PROJECT OVERVIEW

**Project Name:** OJT360 - End-to-End Web-Based Internship Monitoring and Management System  
**Framework:** Laravel (PHP) with Blade Templates  
**Status:** Student Module + Whitelist Activation Complete - Ready for Long-Term Use  
**URL:** https://github.com/nealgod/OJT360.git

---

## CURRENT STATE (Student Module Complete - Ready for Adviser Presentation)

### Recently Implemented Features:
1. **Reports Section Enhancements:**
   - Dynamic attendance display based on selected work date
   - Consistent formatting across all attendance displays
   - PDF generation for weekly reports using DomPDF
   - Direct submission of weekly reports to documents system
   - Removed feedback system (simplified workflow)
   - Auto-generated report templates from attendance data

2. **Documents Section Improvements:**
   - Enhanced UI with grid layout (3 columns responsive)
   - Added search and filter capabilities
   - Added cancel submission feature (like Google Classroom)
   - Letter of Acceptance auto-approved upon placement approval
   - Flexible file upload limits (configurable per requirement)
   - Photo Documentation: up to 50 files
   - Weekly Accomplishment Report: up to 4 files

3. **Program Management Features:**
   - Coordinators can modify required OJT hours for their programs
   - BSIT program updated to 486 hours (from 460)
   - Program-specific hours override default department config
   - Automatic notifications to students when hours change

4. **Supervisor Management Enhancements:**
   - Auto-creation of supervisor accounts from placement requests
   - Email notifications with temporary passwords for new supervisors
   - Support for both external and listed company supervisors
   - Improved supervisor assignment workflow

5. **Database Changes:**
   - Added `max_files_per_submission` to document_requirements
   - Added `required_hours` to programs table
   - Added supervisor fields to placement_requests table
   - Migration for flexible file upload limits

---

## SYSTEM ARCHITECTURE

### User Roles & Authentication Flow

**Roles (4 types):**
1. **Admin** - Full system access, manages users
2. **Coordinator** - Manages students, approvals, supervises OJT workflow
3. **Supervisor** - Manages interns at companies
4. **Intern/Student** - Submits requests, attends OJT, submits reports/docs

**Authentication Flow:**
- Email verification required for all users
- First password change enforced for coordinators/supervisors (created by admin)
- Students DO NOT publicly register. Registration is via activation against a coordinator-uploaded whitelist:
  - Coordinator uploads Class List (CSV/XLSX) → creates pending whitelist rows
  - Student opens Activate page (`/activate`) and enters Student ID + EVSU email
  - If matched to a pending whitelist row: account is created (`role=intern`), email verification sent, auto-login
  - Whitelist row flips to `activated`
- Admin creates coordinators/supervisors → sends temp password → they login → change password

### Key Middleware:
- `auth` - Standard authentication
- `verified` - Email verification check
- `force.password.change` - Forces password change for temp passwords
- `profile.complete` - Ensures user completed profile before accessing features
- `placement.started` - Students can only access some features after OJT approval

---

## MAIN CONTROLLERS & WORKFLOWS
### 0. **ActivationController** (`app/Http/Controllers/ActivationController.php`)
**Purpose:** Student account activation via whitelist

**Key Methods:**
- `show()` - Renders activation form (`resources/views/auth/activate.blade.php`)
- `activate()` - Validates EVSU email + student_id against pending whitelist; creates user, minimal profile, marks row activated, sends email verification, logs in

**Notes:**
- EVSU emails only; rejects non-`@evsu.edu.ph`
- Sets `course` and `department` from the program associated with the whitelist row

### 0.1. **CoordinatorImportController** (`app/Http/Controllers/CoordinatorImportController.php`)
**Purpose:** Coordinator class list import + whitelist management

**Key Methods:**
- `showImport()` - Upload UI
- `preview()` - Validates CSV/XLSX, shows valid/invalid preview with clear errors and counts; supports "Upload" shortcut path which persists whitelist directly
- `commit()` - Enforces single active upload: clears existing pending for program, inserts valid rows, redirects to whitelist
- `status()` - Whitelist Status page with search, filter, pagination; archived hidden by default with optional toggle
- `endTerm()` - Archives all pending/activated rows for current program (past batches remain viewable when toggled)
- `export()` - CSV export of current (pending/activated) whitelist (kept for admin usage; UI download links currently removed)
- `downloadUploaded()` - Download last uploaded original file (currently disabled in UI)

**Views:**
- `resources/views/coord/students/import.blade.php`
- `resources/views/coord/students/import-preview.blade.php`
- `resources/views/coord/students/whitelist.blade.php`

### 1. **PlacementRequestController** (`app/Http/Controllers/PlacementRequestController.php`)
**Purpose:** Handles student placement requests and coordinator approvals

**Key Methods:**
- `submit()` - Student submits placement request with company choice
- `inbox()` - Coordinator sees pending requests
- `approve()` - Coordinator approves request, activates OJT
- `decline()` - Coordinator declines request

**Important Logic:**
- Students can choose listed companies OR submit external company
- Coordinator approves/declines from inbox
- Upon approval: `ojt_status` changes to 'active' in student_profile
- Letter of Acceptance automatically submitted if uploaded during request

### 2. **CoordinatorStudentController** (`app/Http/Controllers/CoordinatorStudentController.php`)
**Purpose:** Coordinator manages their department's students

**Key Methods:**
- `index()` - Lists students in coordinator's department
- `show()` - Shows student details with supervisor assignment
- `updateCompany()` - Updates student's company assignment
- `assignSupervisor()` - Assigns supervisor from approved list

**Important Logic:**
- Coordinator only sees students from their department
- Supervisors filtered by company (must match student's company)
- External companies require special handling

### 3. **DailyReportController** (`app/Http/Controllers/DailyReportController.php`)
**Purpose:** Handles student daily and weekly reports

**Key Methods:**
- `index()` - Lists student's reports (with search/filter)
- `create()` - Shows report submission form with dynamic attendance
- `store()` - Saves report, sends notification to coordinator
- `show()` - Shows detailed report view with attendance data
- `destroy()` - Deletes report (only if status='submitted')
- `weekly()` - Generates weekly report preview
- `generateWeekly()` - Creates weekly report with PDF generation
- `downloadWeekly()` - Downloads weekly report as PDF
- `submitWeeklyToDocuments()` - Submits weekly report to documents system

**Important Logic:**
- Reports cannot be edited (submit-only)
- Reports require minimum 50 characters
- Auto-generates template from attendance if available
- Dynamic attendance display based on selected work date
- Weekly reports generated as PDFs using DomPDF
- Direct integration with document management system

### 4. **DocumentController** (`app/Http/Controllers/DocumentController.php`)
**Purpose:** Manages document submissions

**Key Methods:**
- `index()` - Shows documents to students/coordinators (role-based)
- `show()` - Shows document details and submission form
- `submit()` - Handles flexible file uploads (configurable limits)
- `cancel()` - Allows cancellation of 'submitted' status documents
- `download()` - Downloads submitted documents
- `review()` - Coordinator reviews and approves/rejects documents

**Important Logic:**
- Supports flexible file uploads (configurable per requirement)
- Photo Documentation: up to 50 files
- Weekly Accomplishment Report: up to 4 files
- Letter of Acceptance auto-approved with placement
- Can cancel submissions before review

### 5. **AttendanceController** (`app/Http/Controllers/AttendanceController.php`)
**Purpose:** Handles time-in/time-out with photo proof

**Key Logic:**
- Requires photo for time-in/time-out
- Breaks auto-deducted based on placement request settings
- Recovery feature for missed time-out
- Calculates hours worked automatically

### 6. **CoordinatorProgramController** (`app/Http/Controllers/CoordinatorProgramController.php`)
**Purpose:** Manages program-specific settings for coordinators

**Key Methods:**
- `showHours()` - Displays program hours and statistics
- `updateHours()` - Updates required hours for coordinator's program

**Important Logic:**
- Coordinators can modify required OJT hours for their programs
- Changes automatically notify affected students
- Program hours override default department config
- Shows statistics on students using custom vs default hours

### 7. **DashboardController** (`app/Http/Controllers/DashboardController.php`)
**Purpose:** Role-based dashboard

**Student Dashboard Shows:**
- Progress (completed hours vs required hours)
- Recent activity
- Quick stats
- Placement summary
- Recent attendance
- Recent reports

**Coordinator Dashboard Shows:**
- Pending placement requests
- Student statistics
- Recent activity

---

## DATABASE MODELS & RELATIONSHIPS

### Core Models:

**User** (app/Models/User.php)
- Central auth model with roles: admin, coordinator, supervisor, intern
- Key relationships: studentProfile(), coordinatorProfile(), supervisorProfile()
- Key methods: isStudent(), isCoordinator(), hasActiveOJT(), hasCompletedProfile(), getRequiredHours()
- Enhanced getRequiredHours() prioritizes program-specific hours over department defaults

**StudentProfile** (app/Models/StudentProfile.php)
- Links to User
- Stores: student_id, course, department, ojt_status, assigned_company_id, supervisor_id
- `ojt_status`: pending/active/completed

**PlacementRequest**
- Student submits with company choice
- Status: pending/approved/declined
- Can include external_company_name

**DocumentRequirement**
- Pre-defined document types
- Types: pre_placement, post_placement, ongoing
- Has file_types, max_file_size restrictions
- Added max_files_per_submission for flexible upload limits

**StudentDocumentSubmission**
- Links student to requirement
- Supports flexible file uploads (configurable per requirement)
- Status: submitted/under_review/approved/rejected

**DailyReport**
- Links to student
- Contains: work_date, summary, attachment_path, status
- Status: submitted/approved/returned
- Removed feedback system for simplified workflow

**AttendanceLog**
- Links to student and company
- Tracks: time_in, time_out, minutes_worked, photo_path

---

## ROUTES STRUCTURE (`routes/web.php`)

### Public Routes:
- `/` - Welcome page
- `/login` - Authentication (Register link removed)
- `/activate` - Student activation via whitelist (replaces public register)
- Email verification routes (with coordinator/supervisor resend using temporary password)

### Student Routes (protected by auth, verified, profile.complete):
- `/dashboard` - Student dashboard
- `/placement-requests` - Submit placement request
- `/attendance` - Time in/out with photos
- `/reports/*` - Daily reports (protected by placement.started)
  - `/reports` - List reports with search/filter
  - `/reports/create` - Submit report
  - `/reports/{id}` - View report details
  - DELETE `/reports/{id}` - Delete report
- `/documents/*` - Document submissions
  - `/documents` - List requirements
  - `/documents/{requirement}` - Submit documents
  - DELETE `/documents/submissions/{id}/cancel` - Cancel submission
- `/messages` - Messaging system
- `/notifications` - Notification center

### Coordinator Routes:
- `/coord/students` - Manage students
- `/coord/students/import` - Class List upload (CSV/XLSX)
- `/coord/students/import/preview` - Preview validation results
- `/coord/students/import/commit` - Commit valid rows (single active upload policy)
- `/coord/students/whitelist` - Whitelist Status (search, filter; archived toggle)
- `/coord/students/whitelist/end-term` - Archive all pending/activated (close term)
- `/coord/placements/inbox` - Review placement requests
- `/coord/documents` - Review student documents
- Companies management

### Admin Routes:
- `/admin/dashboard`
- `/admin/users` - User management

---

## KEY CONCEPTS & DECISIONS

### 1. **OJT Status Flow:**
- Student activates via whitelist → completes profile → submits placement request
- Coordinator approves → `ojt_status` = 'active'
- Student can now access: Attendance, Reports, Documents
- Navigation hides these until active

### 2. **Department/Program Scoping:**
- Coordinators only see students from their department
- Students only see companies from their department
- Supervisors only manage students from their company
- All queries are scoped by department/program

### 3. **Document Submission Strategy:**
- Multiple files allowed (up to 2 per requirement)
- Letter of Acceptance auto-submitted with placement request
- Can cancel before review (like Google Classroom)
- No editing after review

### 4. **Report Strategy:**
- No editing (submitted reports are final)
- Can delete if status = 'submitted'
- Auto-template generation from attendance
- Draft autosave in localStorage

### 5. **Navigation Gating:**
- Attendance/Reports/Documents links hidden until OJT active
- Uses: `@if(Auth::user()->hasActiveOJT())`
- Coordinators always see their sections

### 6. **Whitelist & Activation (Key Points):
- Public register disabled; links changed to activation
- Upload accepts external formats (headers like "Student ID", "Student Name", "Phone", "E-Mail"). Name parsing supports "Last, First Middle".
- Validation checks required fields, EVSU email domain, duplicate IDs/emails in file, and existing IDs in DB
- Single active upload: re-upload clears pending rows before insert; activated users remain
- End Term action archives active/pending rows; archived hidden by default but viewable with toggle
- Activation creates user and minimal profile; whitelist row marked `activated`

---

## CUSTOM MIDDLEWARE

### CheckProfileCompletion (`app/Http/Middleware/CheckProfileCompletion.php`)
**Purpose:** Ensures users complete profile before accessing features

**Checks:**
- Students: need student_id, course, department
- Coordinators: need employee_id, department
- Supervisors: need company_id
- Admins: always complete

### ForcePasswordChange (`app/Http/Middleware/ForcePasswordChange.php`)
**Purpose:** Forces password change for users with temp passwords

### EnsurePlacementStarted (`app/Http/Middleware/EnsurePlacementStarted.php`)
**Purpose:** Gates attendance/reports/documents until OJT active

**Checks:** `studentProfile->ojt_status === 'active'`

---

## UI/UX DECISIONS

### Design System:
- **Primary Color:** Maroon/OJT (hex: #8B0000)
- **Accent Color:** Light blue
- **Framework:** Tailwind CSS
- **Icons:** Heroicons (SVG)

### Recent UX Improvements:
1. **Simplified Filtering:** Removed overwhelming stats, kept search + month
2. **Consistent Navigation:** Back buttons on all forms
3. **Status Indicators:** Visual badges with icons (✓ Approved, ↩ Returned, ⏳ Pending)
4. **Grid Layouts:** Documents use responsive grid (1/2/3 columns)
5. **No Edit Complexity:** Submit-only for reports, delete to resubmit

### Key Files:
- `resources/views/layouts/app.blade.php` - Main layout
- `resources/views/layouts/navigation.blade.php` - Nav menu (role-based)
- `resources/views/dashboard.blade.php` - Role-based dashboards
- `resources/css/app.css` - Custom styles
- Tailwind config in `tailwind.config.js`

---

## TESTING & SEEDING

### Seeders (`database/seeders/`):
- `UserSeeder` - Creates admin user
- `DocumentRequirementsSeeder` - Pre-defined document requirements
- `DepartmentSeeder` - Departments and programs

### Test Data:
- See README.md for attendance testing via Tinker
- Use database factories for generating test data

---

## CONFIGURATION

### Important Config Files:
- `.env` - Database, mail, app settings
- `config/departments.php` - Department structure
- `config/app.php` - Timezone (Asia/Manila default)

### Storage:
- Public storage: `storage/app/public`
- Symbolic link: `php artisan storage:link`
- Photos: `storage/app/public/attendance-photos`
- Reports: `storage/app/public/daily-reports`
- Documents: `storage/app/public/document-submissions`
 - Whitelists (internal): `storage/app/whitelists/program_{id}/latest.(csv|xlsx|xls|txt)` (kept for internal reference; not exposed in UI by default)

---

## COMMON TASKS

### Setup New Environment:
```bash
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run dev
php artisan storage:link
```

### Database Changes:
Create migration: `php artisan make:migration migration_name`
Run: `php artisan migrate`
Rollback: `php artisan migrate:rollback`

### Recent Migration:
`2025_10_21_082811_recreate_student_document_submissions_table.php` - Removes unique constraint to allow multiple files

---

## NEXT DEVELOPMENT AREAS (Pending Modules)

### 🎯 **Priority 1: Coordinator Module**
- Enhanced coordinator dashboard with analytics
- Advanced student management features
- Company management and approval workflows
- Document review and approval system
- Report analytics and insights

### 🎯 **Priority 2: Supervisor Module**
- Supervisor dashboard for managing interns
- Attendance monitoring and approval
- Report review and feedback system
- Intern progress tracking
- Communication tools with students

### 🎯 **Priority 3: Admin Module**
- System-wide user management
- Department and program configuration
- System analytics and reporting
- Backup and maintenance tools
- Global settings and configurations

### 🔧 **Technical Enhancements**
- Real-time notifications
- Advanced analytics and reporting
- Mobile responsiveness improvements
- API development for mobile app
- Performance optimizations

---

## IMPORTANT NOTES

- Always check role before accessing resources
- Always scope queries by department/program
- Use `hasActiveOJT()` for student-specific features
- Profiles must be completed before accessing most features
- Supervisors only manage students from their company
- Coordinators only see their department's students

---

## QUICK REFERENCE

**Check if student has active OJT:**
```php
Auth::user()->hasActiveOJT()
```

**Get student's completed hours:**
```php
Auth::user()->getCompletedHours()
```

**Check profile completion:**
```php
Auth::user()->hasCompletedProfile()
```

**Scope by department:**
```php
whereHas('studentProfile', function($q) {
    $q->where('department', $department);
})
```

**Create notification:**
```php
\App\Models\Notification::create([
    'user_id' => $userId,
    'type' => 'type_name',
    'title' => 'Title',
    'message' => 'Message',
    'data' => ['key' => 'value']
]);
```

---

**Last Updated:** Student Module + Whitelist Activation Complete  
**Project Status:** Student Module + Coordinator Whitelist flow complete; Supervisor/Admin modules pending  
**Contact:** Continue with this document as your base understanding.
