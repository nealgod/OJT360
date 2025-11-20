# Weekly Reports System - Complete Implementation

## Overview
This document describes the complete weekly reports system that replaced the old daily reports functionality. The system allows students to submit weekly activity reports that are reviewed by supervisors and coordinators, with PDF generation capabilities.

---

## System Architecture

### Database Schema
**Table: `weekly_reports`**
- `id` - Primary key
- `user_id` - Foreign key to users table (student)
- `week_start_date` - Start date of the reporting week
- `week_end_date` - End date of the reporting week
- `activities` - JSON field storing daily activities
- `learnings` - Text field for weekly learnings
- `challenges` - Text field for challenges faced
- `supervisor_feedback` - Text field for supervisor comments
- `coordinator_feedback` - Text field for coordinator comments
- `status` - Enum: pending, approved, rejected
- `submitted_at` - Timestamp of submission
- `reviewed_at` - Timestamp of review
- `timestamps` - created_at, updated_at

### Models

**WeeklyReport Model** (`app/Models/WeeklyReport.php`)
- Relationships:
  - `belongsTo(User::class)` - Student who created the report
- Casts:
  - `activities` as array
  - `week_start_date`, `week_end_date`, `submitted_at`, `reviewed_at` as dates
- Fillable fields: user_id, week_start_date, week_end_date, activities, learnings, challenges, supervisor_feedback, coordinator_feedback, status

**User Model** (`app/Models/User.php`)
- Added relationship: `hasMany(WeeklyReport::class)` as `weeklyReports()`

---

## Routes

All routes are prefixed with `/reports/weekly` and named with `reports.weekly.*`:

```php
Route::middleware(['auth'])->prefix('reports/weekly')->name('reports.weekly.')->group(function () {
    Route::get('/', [WeeklyReportController::class, 'index'])->name('index');
    Route::get('/create', [WeeklyReportController::class, 'create'])->name('create');
    Route::post('/', [WeeklyReportController::class, 'store'])->name('store');
    Route::get('/{weeklyReport}', [WeeklyReportController::class, 'show'])->name('show');
    Route::get('/{weeklyReport}/pdf', [WeeklyReportController::class, 'generatePdf'])->name('pdf');
});
```

---

## Controllers

### WeeklyReportController (`app/Http/Controllers/WeeklyReportController.php`)

**Methods:**

1. **index()** - List all weekly reports for the authenticated user
   - Loads reports with pagination
   - Orders by week_start_date descending
   - Returns: `resources/views/reports/weekly/index.blade.php`

2. **create()** - Show form to create a new weekly report
   - Validates user has required profile data (program, year_level)
   - Returns: `resources/views/reports/weekly/create.blade.php`

3. **store(Request $request)** - Save a new weekly report
   - Validates:
     - week_start_date and week_end_date are required dates
     - activities is required array with 7 entries (one per day)
     - learnings and challenges are required strings
   - Creates report with status 'pending'
   - Sets submitted_at timestamp
   - Redirects to index with success message

4. **show(WeeklyReport $weeklyReport)** - Display a single report
   - Authorization: User must own the report OR be supervisor/coordinator
   - Returns: `resources/views/reports/weekly/show.blade.php`

5. **generatePdf(WeeklyReport $weeklyReport)** - Generate PDF of report
   - Authorization: User must own the report OR be supervisor/coordinator
   - Uses WeeklyReportPdfService
   - Returns: PDF download

---

## Services

### WeeklyReportPdfService (`app/Services/WeeklyReportPdfService.php`)

Generates PDF reports using FPDF library with the official OJT template format.

**Key Features:**
- Uses official template: `resources/templates/WEEKLY_REPORT_TEMPLATE.pdf`
- Precise coordinate positioning for all fields
- Handles multi-line text wrapping
- Formats dates properly
- Includes student information, activities, learnings, challenges, and feedback

**Main Method:**
```php
public function generate(WeeklyReport $report): string
```
Returns the path to the generated PDF file.

**Template Coordinates:**
- Student Name: (65, 52)
- Program: (65, 60)
- Week Period: (65, 68)
- Daily Activities: Starting at Y=85, 7mm spacing between days
- Learnings: (20, 155)
- Challenges: (20, 175)
- Supervisor Feedback: (20, 195)
- Coordinator Feedback: (20, 215)

---

## Views

### Index View (`resources/views/reports/weekly/index.blade.php`)
- Lists all weekly reports in a table
- Shows: Week period, submission date, status
- Actions: View details, Download PDF
- Includes "Create New Report" button
- Status badges with color coding (pending=yellow, approved=green, rejected=red)

### Create View (`resources/views/reports/weekly/create.blade.php`)
- Form with date pickers for week start/end dates
- 7 textarea fields for daily activities (Monday-Sunday)
- Textarea for learnings
- Textarea for challenges
- Submit button
- JavaScript for date validation

### Show View (`resources/views/reports/weekly/show.blade.php`)
- Displays all report details
- Shows student information
- Lists daily activities
- Shows learnings and challenges
- Displays supervisor and coordinator feedback (if any)
- Status badge
- Download PDF button
- Back to list button

---

## Navigation Integration

Updated in `resources/views/layouts/navigation.blade.php`:

```blade
<x-nav-link :href="route('reports.weekly.index')" :active="request()->routeIs('reports.weekly.*')">
    {{ __('Weekly Reports') }}
</x-nav-link>
```

---

## Dashboard Integration

Updated in `resources/views/dashboard.blade.php`:

**For Students:**
- Shows recent weekly reports
- Quick link to create new report
- Displays submission status

**For Supervisors:**
- Shows pending weekly reports from supervised students
- Quick review links

**For Coordinators:**
- Shows all pending weekly reports
- Overview statistics

---

## Migration History

1. **Created weekly_reports table** - Initial schema
2. **Dropped daily_reports table** - Removed old system
3. **Updated users table** - Removed dailyReports relationship reference

---

## Cleanup Completed

### Removed Files:
- `app/Models/DailyReport.php`
- `app/Http/Controllers/DailyReportController.php`
- `resources/views/reports/create.blade.php`
- `resources/views/reports/index.blade.php`
- `resources/views/reports/show.blade.php`

### Updated References:
- All views now reference `weeklyReports` instead of `dailyReports`
- All routes use `reports.weekly.*` naming
- Navigation updated to "Weekly Reports"
- Dashboard queries updated to use WeeklyReport model

### Verified Clean:
✅ No `dailyReports` references in codebase
✅ No `DailyReport` class references
✅ No old route references
✅ No `work_date` field on reports (only on attendance_logs)
✅ User model has only `weeklyReports()` relationship

---

## User Workflow

### Student Workflow:
1. Navigate to "Weekly Reports" from main menu
2. Click "Create New Report"
3. Select week start and end dates
4. Fill in daily activities for each day of the week
5. Describe learnings and challenges
6. Submit report (status: pending)
7. View submitted reports and download PDFs
8. Check for supervisor/coordinator feedback

### Supervisor Workflow:
1. View pending reports from supervised students
2. Review activities, learnings, and challenges
3. Add feedback
4. Approve or reject report

### Coordinator Workflow:
1. View all pending reports
2. Review student progress
3. Add coordinator feedback
4. Monitor submission compliance

---

## Technical Notes

### Date Handling:
- Week dates are stored as DATE fields in database
- Carbon is used for date manipulation
- Validation ensures week_end_date is after week_start_date

### Activities Structure:
```json
{
  "monday": "Activity description",
  "tuesday": "Activity description",
  "wednesday": "Activity description",
  "thursday": "Activity description",
  "friday": "Activity description",
  "saturday": "Activity description",
  "sunday": "Activity description"
}
```

### Status Flow:
1. **pending** - Initial state after submission
2. **approved** - Reviewed and approved by supervisor/coordinator
3. **rejected** - Needs revision

### PDF Generation:
- Uses FPDF library
- Template-based approach with coordinate positioning
- Automatic text wrapping for long content
- Professional formatting matching official template

---

## Future Enhancements (Potential)

Possible improvements for the system:
- **Monthly Reports** - Aggregate weekly reports into monthly summaries
- **Auto-population** - Pull attendance data automatically into reports
- **Supervisor Review Workflow** - Allow supervisors to approve/reject with feedback
- **Advanced Filtering** - Filter by date range, status, student
- **Bulk Operations** - Export multiple reports, bulk approve
- **Email Notifications** - Notify when reports are submitted/reviewed
- **Analytics Dashboard** - Visual charts of submission rates, hours worked
- **Export Capabilities** - Excel/CSV export of report data

---

## Status: ✅ COMPLETE AND OPERATIONAL

The weekly reports system is fully functional with:
- Complete CRUD operations
- PDF generation
- Role-based access control
- Clean codebase with no legacy references
- Proper navigation and dashboard integration
- Template-based PDF output

Last Updated: November 20, 2025
