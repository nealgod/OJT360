# Weekly Report Workflow

## Student Workflow

### 1. Create Report
- Click "Create Weekly Report" button
- Select date range (max 7 days)
- System validates:
  - No overlapping reports with attendance
  - No incomplete attendance (missing time out)
  - Date range within 7 days
- Fill in daily activities
- Click "Save Report"
- **Report saved as DRAFT** (not submitted yet)

### 2. Review Draft
- View report details
- Check attendance summary
- Review daily activities
- Options available:
  - **Submit Report** - Send to coordinator for review
  - **Delete** - Remove draft report (only for drafts)
  - **Download PDF** - Get PDF copy
  - **Back to List** - Return to reports list

### 3. Submit Report
- Click "Submit Report" button
- System validates:
  - Report has content
  - At least one activity added
  - Not already submitted
- Status changes from "draft" to "submitted"
- Coordinator can now review it
- **Cannot delete after submission**

### 4. Track Status
- **Draft** (Yellow) - Not yet submitted, can edit/delete
- **Submitted** (Blue) - Sent to coordinator, awaiting review
- **Reviewed** (Green) - Coordinator has reviewed

## Coordinator Workflow

### 1. View Reports
- Access via "Weekly Reports" menu
- **Only submitted and reviewed reports are visible** (drafts are hidden)
- **Only see students from their own program** (no overlap with other coordinators)
- Search by student ID
- See all submitted reports from students in their program
- View report details including:
  - Student information
  - Attendance summary
  - Daily activities
  - Problems encountered

### 2. Review Report
- Read through report content
- View attendance summary
- Review daily activities
- **Download PDF copy** with download button
- See any existing feedback
- **View-only mode** (no editing form)

### 3. Search Functionality
- Search bar for finding specific students
- **Search by student ID only** (not by name)
- Clear button to reset search
- Results update automatically
- Only searches through submitted/reviewed reports

### 4. Coordinator Isolation
- Each coordinator only sees reports where `coordinator_user_id` matches their ID
- Students are filtered by coordinator's `program_id`
- Policy authorization prevents unauthorized access
- No overlap between different coordinators

## Key Features

### For Students
✅ Save reports as drafts before submitting
✅ Delete draft reports if needed
✅ Submit when ready with validation
✅ Clear status indicators
✅ Warning for pending drafts
✅ Cannot delete submitted reports

### For Coordinators
✅ Only see submitted reports (drafts hidden)
✅ Only see their own students (isolated by program)
✅ Search by student ID
✅ Download PDF reports
✅ View all report details
✅ No editing/review form (view-only)

## Validation Rules

### Creating Report
- Date range max 7 days
- No overlapping with existing reports (with attendance)
- No incomplete attendance records
- Must have at least one activity

### Submitting Report
- Report must have content
- At least one activity required
- Can only submit drafts
- Cannot re-submit

### Deleting Report
- Only draft reports can be deleted
- Submitted/reviewed reports cannot be deleted
- Confirmation required

## Status Flow
```
Draft → Submitted → Reviewed
  ↓
Delete (only if draft)
```
