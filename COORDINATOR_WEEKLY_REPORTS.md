# Coordinator Weekly Reports Feature

## Overview
Coordinators can now view and review weekly reports submitted by students in their program.

## Features Implemented

### 1. Coordinator Controller (`app/Http/Controllers/CoordinatorReportController.php`)
- **index()**: List all weekly reports for students in coordinator's program with filtering by student and status
- **show()**: View detailed weekly report with daily activities and supervisor info
- **updateStatus()**: Update report status and add coordinator feedback with authorization

### 2. Student Controller (`app/Http/Controllers/WeeklyReportController.php`)
- **submit()**: Submit weekly report with validation (checks for content, prevents duplicate submission)

### 3. Routes (`routes/web.php`)
**Coordinator Routes:**
- `GET /coord/reports` - List weekly reports
- `GET /coord/reports/{report}` - View report details
- `PATCH /coord/reports/{report}/status` - Update report status

**Student Routes:**
- `PATCH /reports/weekly/{weekly}/submit` - Submit weekly report

### 4. Views
**Coordinator Views:**
- `resources/views/coord/reports/index.blade.php` - Reports listing with filters (matches OJT theme)
- `resources/views/coord/reports/show.blade.php` - Detailed report view with status update form (matches OJT theme)

**Student Views:**
- `resources/views/reports/weekly/show.blade.php` - Updated with submit button and status display

### 5. Navigation
- Added "Weekly Reports" link to coordinator navigation (desktop and mobile)

### 6. Authorization
- `app/Policies/WeeklyReportPolicy.php` - Ensures coordinators can only access their assigned reports

### 7. Model Updates
- Added `supervisor` accessor to get supervisor through student profile
- Removed `attendanceLogs` relationship (column doesn't exist in database)

## Database Fields Used
- `coordinator_user_id` - Links report to coordinator
- `coordinator_feedback` - Coordinator's feedback on the report
- `coordinator_reviewed_at` - Timestamp when coordinator reviewed
- `status` - Report status (draft, submitted, reviewed)

## Student Features

### Submit Weekly Report
Students can:
- Submit draft reports with validation
- See submit button only for draft reports
- View status badge (draft, submitted, reviewed)
- See submission timestamp
- Get error messages if:
  - Report is empty
  - No activities added
  - Already submitted

### Error Handling
- Cannot submit empty reports
- Cannot submit reports without activities
- Cannot re-submit already submitted reports
- Clear error messages displayed

## Coordinator Features

### Filtering
Coordinators can filter reports by:
- Student
- Status (draft, submitted, reviewed)

### Report Details
Coordinators can view:
- Student information
- Week number and dates
- Supervisor information
- Attendance records with late/absent indicators
- Weekly summary
- Previous coordinator feedback

### Status Management
Coordinators can:
- Update report status (draft, submitted, reviewed)
- Add feedback for students
- Track review timestamps

## Access Control
- Only coordinators can access these routes
- Coordinators can only view reports assigned to them (via `coordinator_user_id`)
- Authorization enforced via policy

## Next Steps
To use this feature:
1. Run migrations if not already done: `php artisan migrate`
2. Ensure weekly reports have `coordinator_user_id` set when created
3. Coordinators can access via navigation menu "Weekly Reports"
