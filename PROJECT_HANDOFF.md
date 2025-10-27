# OJT360 Project - Continuation Handoff Document

## PROJECT OVERVIEW

**Project Name:** OJT360 - End-to-End Web-Based Internship Monitoring and Management System  
**Framework:** Laravel (PHP) with Blade Templates  
**Status:** Active Development - Latest commit includes Reports & Documents improvements  
**URL:** https://github.com/nealgod/OJT360.git

---

## CURRENT STATE (Latest Changes - Commit 01b2363)

### Recently Implemented Features:
1. **Reports Section Improvements:**
   - Added view functionality for detailed report viewing
   - Simplified filtering (search + month only, removed status filter)
   - Removed edit functionality (submit only, delete if submitted)
   - Clean, consistent UI with back buttons

2. **Documents Section Improvements:**
   - Enhanced UI with grid layout (3 columns responsive)
   - Added search and filter capabilities
   - Added cancel submission feature (like Google Classroom)
   - Letter of Acceptance now required (was optional)
   - Support for multiple file uploads (up to 2 files per requirement)

3. **Database Changes:**
   - Migration: Fixed document submissions table to allow multiple files
   - Removed unique constraint on student_document_submissions

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
- Students self-register → email verify → complete profile → submit placement request
- Admin creates coordinators/supervisors → sends temp password → they login → change password

### Key Middleware:
- `auth` - Standard authentication
- `verified` - Email verification check
- `force.password.change` - Forces password change for temp passwords
- `profile.complete` - Ensures user completed profile before accessing features
- `placement.started` - Students can only access some features after OJT approval

---

## MAIN CONTROLLERS & WORKFLOWS

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
**Purpose:** Handles student daily reports

**Key Methods:**
- `index()` - Lists student's reports (with search/filter)
- `create()` - Shows report submission form
- `store()` - Saves report, sends notification to coordinator
- `show()` - Shows detailed report view
- `destroy()` - Deletes report (only if status='submitted')

**Important Logic:**
- Reports cannot be edited (submit-only)
- Reports require minimum 50 characters
- Auto-generates template from attendance if available
- Stores drafts in localStorage

### 4. **DocumentController** (`app/Http/Controllers/DocumentController.php`)
**Purpose:** Manages document submissions

**Key Methods:**
- `index()` - Shows documents to students/coordinators (role-based)
- `show()` - Shows document details and submission form
- `submit()` - Handles multiple file uploads (up to 2 files)
- `cancel()` - Allows cancellation of 'submitted' status documents
- `download()` - Downloads submitted documents

**Important Logic:**
- Supports multiple file uploads (1-2 files per requirement)
- Letter of Acceptance is required (auto-submitted with placement)
- Can cancel submissions before review

### 5. **AttendanceController** (`app/Http/Controllers/AttendanceController.php`)
**Purpose:** Handles time-in/time-out with photo proof

**Key Logic:**
- Requires photo for time-in/time-out
- Breaks auto-deducted based on placement request settings
- Recovery feature for missed time-out
- Calculates hours worked automatically

### 6. **DashboardController** (`app/Http/Controllers/DashboardController.php`)
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
- Key methods: isStudent(), isCoordinator(), hasActiveOJT(), hasCompletedProfile()

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

**StudentDocumentSubmission**
- Links student to requirement
- Supports multiple files per requirement (up to 2)
- Status: submitted/under_review/approved/rejected

**DailyReport**
- Links to student
- Contains: work_date, summary, attachment_path, status, feedback
- Status: submitted/approved/returned

**AttendanceLog**
- Links to student and company
- Tracks: time_in, time_out, minutes_worked, photo_path

---

## ROUTES STRUCTURE (`routes/web.php`)

### Public Routes:
- `/` - Welcome page
- `/login`, `/register` - Authentication
- Email verification routes

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
- `/coord/placements/inbox` - Review placement requests
- `/coord/documents` - Review student documents
- `/coord/reports` - Review daily reports
- Companies management

### Admin Routes:
- `/admin/dashboard`
- `/admin/users` - User management

---

## KEY CONCEPTS & DECISIONS

### 1. **OJT Status Flow:**
- Student registers → completes profile → submits placement request
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

## NEXT DEVELOPMENT AREAS

1. **Reports:** Consider adding edit functionality or weekly reports
2. **Documents:** PDF generation for documents
3. **Attendance:** Analytics dashboard for coordinators
4. **Messaging:** Real-time chat functionality
5. **Notifications:** Email notifications for key events

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

**Last Updated:** Commit 01b2363 (Latest push)  
**Project Status:** Active Development  
**Contact:** Continue with this document as your base understanding.
