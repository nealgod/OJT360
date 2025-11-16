# DOCUMENTS SYSTEM - DEEP ANALYSIS

## OVERVIEW
The document management system handles student document submissions for OJT requirements with a three-phase workflow: pre-placement, post-placement, and ongoing requirements. The system supports multiple file submissions per requirement with coordinator review and approval workflows.

---

## DATABASE STRUCTURE

### Tables

#### 1. `document_requirements`
**Purpose:** Defines document types that students must submit

**Columns:**
- `id` - Primary key
- `name` - Document name (e.g., "Medical Certificate")
- `description` - Detailed description (nullable)
- `type` - ENUM: 'pre_placement', 'post_placement', 'ongoing'
- `is_required` - BOOLEAN: Whether document is mandatory
- `file_types` - JSON: Allowed file extensions ['pdf', 'jpg', 'docx']
- `max_file_size_mb` - INTEGER: Maximum file size in MB (default: 10)
- `max_files_per_submission` - INTEGER: Max files allowed per requirement (default: 2)
- `instructions` - TEXT: Instructions for students (nullable)
- `is_active` - BOOLEAN: Whether requirement is currently active
- `created_at`, `updated_at` - Timestamps

**Key Features:**
- Supports 3 document types (phases)
- Configurable file type restrictions
- Configurable file size limits
- Configurable multiple file submissions per requirement
- Can be marked as required or optional
- Can be activated/deactivated

#### 2. `student_document_submissions`
**Purpose:** Stores individual file submissions from students

**Columns:**
- `id` - Primary key
- `student_user_id` - FK to users table
- `document_requirement_id` - FK to document_requirements table
- `file_path` - STRING: Storage path in public disk
- `original_filename` - STRING: Original uploaded filename
- `file_size` - STRING: File size in bytes
- `mime_type` - STRING: File MIME type
- `status` - ENUM: 'submitted', 'under_review', 'approved', 'rejected'
- `feedback` - TEXT: Coordinator feedback (nullable)
- `reviewed_by` - FK to users table (nullable)
- `reviewed_at` - TIMESTAMP: When review was completed (nullable)
- `created_at`, `updated_at` - Timestamps

**Key Features:**
- NO UNIQUE CONSTRAINT - Allows multiple files per requirement
- Tracks review status and reviewer
- Stores coordinator feedback
- Cascades on delete (if user or requirement deleted)

**Important Migration History:**
- Originally had unique constraint on (student_user_id, document_requirement_id)
- Migration `2025_10_21_082811` removed this constraint to allow multiple files
- Migration `2025_10_27_072726` added max_files_per_submission to requirements table

---

## MODELS

### DocumentRequirement Model
**Location:** `app/Models/DocumentRequirement.php`

**Fillable Fields:**
```php
'name', 'description', 'type', 'is_required', 'file_types', 
'max_file_size_mb', 'max_files_per_submission', 'instructions', 'is_active'
```

**Casts:**
- `file_types` → array
- `is_required` → boolean
- `is_active` → boolean

**Relationships:**
- `submissions()` - hasMany StudentDocumentSubmission

**Scopes:**
- `active()` - Only active requirements
- `prePlacement()` - Type = 'pre_placement'
- `postPlacement()` - Type = 'post_placement'
- `ongoing()` - Type = 'ongoing'

**Accessors:**
- `file_types_string` - Comma-separated file types or "Any"
- `max_file_size_string` - Size with "MB" suffix

### StudentDocumentSubmission Model
**Location:** `app/Models/StudentDocumentSubmission.php`

**Fillable Fields:**
```php
'student_user_id', 'document_requirement_id', 'file_path', 
'original_filename', 'file_size', 'mime_type', 'status', 
'feedback', 'reviewed_by', 'reviewed_at'
```

**Casts:**
- `reviewed_at` → datetime

**Relationships:**
- `student()` - belongsTo User (student_user_id)
- `requirement()` - belongsTo DocumentRequirement
- `reviewer()` - belongsTo User (reviewed_by)

**Scopes:**
- `submitted()` - Status = 'submitted'
- `underReview()` - Status = 'under_review'
- `approved()` - Status = 'approved'
- `rejected()` - Status = 'rejected'

**Accessors:**
- `status_badge` - Tailwind CSS classes for status badges
- `status_text` - Human-readable status text
- `file_size_formatted` - Formatted file size (B, KB, MB, GB)

**Status Badge Colors:**
- submitted → blue
- under_review → yellow
- approved → green
- rejected → red

---

## CONTROLLER: DocumentController

**Location:** `app/Http/Controllers/DocumentController.php`

### Routes & Methods

#### Student Routes
1. **GET /documents** → `index()`
   - Shows all document requirements grouped by type
   - Displays submission status for each requirement
   - Shows progress bar and pre-requirement checklist
   - Includes search and filter functionality

2. **GET /documents/{requirement}** → `show()`
   - Shows detailed view of specific requirement
   - Lists all student's submissions for that requirement
   - Provides submission form if not submitted or rejected
   - Shows file details, status, and coordinator feedback

3. **POST /documents/{requirement}/submit** → `submit()`
   - Handles file upload (multiple files supported)
   - Validates file types, size, and count
   - Checks against max_files_per_submission limit
   - Creates submission records
   - Notifies coordinator of new submission
   - Prevents exceeding file limit (ignores rejected submissions)

4. **DELETE /documents/submissions/{submission}/cancel** → `cancel()`
   - Allows students to cancel submitted documents
   - Only works if status is 'submitted' (not reviewed yet)
   - Deletes file from storage
   - Deletes submission record

5. **GET /documents/submissions/{submission}/download** → `download()`
   - Downloads submission file
   - Checks permissions (student owns it or coordinator in same department)

6. **GET /documents/submissions/{submission}/preview** → `preview()`
   - Displays file inline in browser
   - Used for PDF/image preview

7. **GET /documents/submissions/{submission}/stream** → `stream()`
   - Streams file for inline viewing
   - Alternative to preview method

#### Coordinator Routes
8. **GET /coord/documents** → `index()` (coordinator view)
   - Shows all students in coordinator's department
   - Lists all document submissions
   - Provides 4 view modes:
     - Needs Review (queue)
     - All Submissions
     - Per-Requirement view
     - By Student view
   - Shows statistics (total, pending, approved, rejected)

9. **POST /coord/documents/submissions/{submission}/review** → `review()`
   - Reviews single submission
   - Sets status to 'approved' or 'rejected'
   - Adds optional feedback
   - Records reviewer and review timestamp
   - Notifies student of review
   - Checks if all pre-placement requirements completed

10. **POST /coord/documents/bulk-review** → `bulkReview()`
    - Reviews multiple submissions at once
    - Same validation and notification as single review
    - Filters by coordinator's department
    - Checks pre-placement completion for each student

### Key Business Logic

#### File Submission Limits
```php
// Check existing non-rejected submissions
$existingCount = StudentDocumentSubmission::where('student_user_id', $user->id)
    ->where('document_requirement_id', $requirement->id)
    ->where('status', '!=', 'rejected')
    ->count();

// Rejected submissions don't count toward limit (allows resubmission)
if ($existingCount + $newFilesCount > $maxFiles) {
    // Error: limit exceeded
}
```

#### Pre-Placement Completion Check
```php
private function checkPrePlacementCompletion($studentId)
{
    // Get all required pre-placement requirements
    $prePlacementRequirements = DocumentRequirement::where('type', 'pre_placement')
        ->where('is_required', true)
        ->get();

    // Get all approved submissions
    $approvedSubmissions = StudentDocumentSubmission::where('student_user_id', $studentId)
        ->whereIn('document_requirement_id', $prePlacementRequirements->pluck('id'))
        ->where('status', 'approved')
        ->get();

    // Check if ALL required pre-placement docs are approved
    $allRequiredApproved = $prePlacementRequirements->every(function ($requirement) use ($approvedSubmissions) {
        return $approvedSubmissions->pluck('document_requirement_id')->contains($requirement->id);
    });

    if ($allRequiredApproved) {
        // Send special notification
        Notification::create([
            'type' => 'pre_placement_complete',
            'title' => '🎉 Pre-Placement Requirements Complete!',
            'message' => 'You can now proceed with your placement request.',
        ]);
    }
}
```

#### Permission Checks
- Students can only view/download their own submissions
- Coordinators can only view/review submissions from students in their department
- Department matching is enforced on all coordinator actions

---

## VIEWS

### Student Views

#### 1. `resources/views/documents/index.blade.php`
**Purpose:** Main document listing page for students

**Features:**
- Progress bar showing overall completion
- Pre-requirement checklist with approval status
- Search and filter functionality (by status and type)
- Document cards grouped by type (pre-placement, post-placement, ongoing)
- Each card shows:
  - Document name
  - Required/Optional badge
  - Status badge
  - File type and size limits
  - Number of files submitted
  - "Submit Now" or "View Details" button

**JavaScript Features:**
- Real-time search filtering
- Status filter (all, submitted, approved, rejected, pending)
- Type filter (all, pre_placement, post_placement)
- Dynamic section hiding when no matches

#### 2. `resources/views/documents/show.blade.php`
**Purpose:** Detailed view of single document requirement

**Features:**
- Requirement details (name, description, type, required status)
- File specifications (types, size, max files)
- Instructions for submission
- List of all student's submissions for this requirement
- Each submission shows:
  - File details (name, size, submission date)
  - Status with badge
  - Review information (reviewer, date, feedback)
  - Download button
  - Cancel button (if status = submitted)
  - Resubmit link (if status = rejected)
- Submission form (if not submitted or rejected)
  - Multiple file upload
  - File preview before submission
  - Remove file functionality

**JavaScript Features:**
- File selection preview
- File count validation
- Individual file removal
- File size display

### Coordinator Views

#### 3. `resources/views/documents/coordinator.blade.php`
**Purpose:** Document review interface for coordinators

**Features:**
- Quick stats dashboard (total, pending, approved, rejected)
- 4 tab views:
  1. **Needs Review** - Queue of submitted documents
  2. **All Submissions** - Complete list of all submissions
  3. **Per-Requirement** - Grid of requirements with submission counts
  4. **By Student** - Student-centric view with checklist

**Tab 1: Needs Review (Queue)**
- Lists only submissions with status = 'submitted'
- Sorted by newest first
- Shows student info, document name, file details
- Preview, Download, and Review buttons

**Tab 2: All Submissions**
- Shows all submissions regardless of status
- Same layout as queue
- Includes status badges

**Tab 3: Per-Requirement**
- Grid of document requirement cards
- Click to see all submissions for that requirement
- Opens modal with student list

**Tab 4: By Student**
- Student search and picker
- Shows student profile in sidebar
- Displays all requirements grouped by type
- Shows missing requirements count
- Each requirement shows:
  - Status
  - Submission date
  - Feedback
  - Preview/Download/Review buttons

**Modals:**
1. **Document Details Modal**
   - Shows all students who submitted specific document
   - Search functionality
   - Review buttons for each submission

2. **Review Modal**
   - Status dropdown (approved/rejected)
   - Feedback textarea
   - Submit button

**JavaScript Features:**
- Tab switching
- Student search with debounce
- Dynamic rendering of submissions
- Modal management
- Inline review forms
- Real-time filtering

---

## SEEDED DOCUMENT REQUIREMENTS

### Pre-Placement Requirements (9 total)
1. **Copy of Certificate of Registration** (Required)
   - Types: pdf, jpg, jpeg, png | Max: 5MB | Max Files: 2

2. **Copy of Report of Grades** (Required)
   - Types: pdf, jpg, jpeg, png | Max: 5MB | Max Files: 2

3. **Application Letter and PDS/Resume** (Required)
   - Types: pdf, doc, docx | Max: 3MB | Max Files: 2

4. **Medical Certificate** (Required)
   - Types: pdf, jpg, jpeg, png | Max: 5MB | Max Files: 2

5. **Notarized Parent's Consent Form** (Required)
   - Types: pdf, jpg, jpeg, png | Max: 5MB | Max Files: 2

6. **Insurance Certificate** (Required)
   - Types: pdf, jpg, jpeg, png | Max: 3MB | Max Files: 2

7. **Certificates of Participation/Attendance** (Required)
   - Types: pdf, jpg, jpeg, png | Max: 5MB | Max Files: 2

8. **Letter of Acceptance** (Required)
   - Types: pdf, jpg, jpeg, png | Max: 3MB | Max Files: 2

9. **Recommendation** (Required)
   - Types: pdf, jpg, jpeg, png | Max: 3MB | Max Files: 2

### Post-Placement Requirements (15 total)
1. **Documentation Report** (Required)
   - Types: pdf, doc, docx | Max: 10MB | Max Files: 2

2. **Company Profile** (Required)
   - Types: pdf, doc, docx | Max: 5MB | Max Files: 2

3. **Weekly Accomplishment Report** (Required)
   - Types: pdf, doc, docx | Max: 5MB | **Max Files: 4** ⭐
   - Special: Allows 4 weekly reports (one per week for a month)

4. **OJT Learning Experience Journal** (Required)
   - Types: pdf, doc, docx | Max: 5MB | Max Files: 2

5. **Pertinent Documents** (Required)
   - Types: pdf, doc, docx, jpg, jpeg, png | Max: 10MB | Max Files: 2

6. **Personal Data Sheet or Resume** (Required)
   - Types: pdf, doc, docx | Max: 3MB | Max Files: 2

7. **Application Letter** (Required)
   - Types: pdf, doc, docx | Max: 3MB | Max Files: 2

8. **Letter of Acceptance (Post-placement)** (Optional)
   - Types: pdf, jpg, jpeg, png | Max: 3MB | Max Files: 2

9. **Recommendation Letter** (Required)
   - Types: pdf, jpg, jpeg, png | Max: 3MB | Max Files: 2

10. **Certificate of Completion (Photocopy)** (Required)
    - Types: pdf, jpg, jpeg, png | Max: 5MB | Max Files: 2

11. **Supervisor's Evaluation Form** (Required)
    - Types: pdf, jpg, jpeg, png | Max: 5MB | Max Files: 2

12. **Authenticated Copy of DTR** (Required)
    - Types: pdf, jpg, jpeg, png | Max: 5MB | Max Files: 2

13. **Photo Documentation** (Required)
    - Types: pdf, jpg, jpeg, png, zip | Max: 10MB | **Max Files: 50** ⭐
    - Special: Allows up to 50 photos

14. **Other Documents Not Specified** (Optional)
    - Types: pdf, doc, docx, jpg, jpeg, png | Max: 10MB | Max Files: 2

### Ongoing Requirements
Currently: **0 requirements** defined in seeder

---

## FILE STORAGE

**Storage Disk:** `public`
**Base Path:** `storage/app/public/document-submissions/`
**Public URL:** `storage/document-submissions/`

**File Naming:** Laravel's automatic hashing (e.g., `abc123def456.pdf`)

**Storage Operations:**
- Upload: `$file->store('document-submissions', 'public')`
- Delete: `Storage::disk('public')->delete($submission->file_path)`
- Download: `Storage::disk('public')->download($path, $filename)`
- Stream: `Storage::disk('public')->response($path, $filename, ['Content-Disposition' => 'inline'])`

---

## NOTIFICATIONS

### Document Submission Notification
**Trigger:** Student submits document(s)
**Recipient:** Coordinator in student's department
**Type:** `document_submitted`
**Data:**
```php
[
    'submission_count' => int,
    'requirement_id' => int,
    'requirement_name' => string,
    'student_user_id' => int,
]
```

### Document Review Notification
**Trigger:** Coordinator reviews submission
**Recipient:** Student who submitted
**Type:** `document_reviewed`
**Data:**
```php
[
    'submission_id' => int,
    'status' => 'approved'|'rejected',
]
```

### Pre-Placement Complete Notification
**Trigger:** All required pre-placement documents approved
**Recipient:** Student
**Type:** `pre_placement_complete`
**Title:** "🎉 Pre-Placement Requirements Complete!"
**Data:**
```php
[
    'type' => 'pre_placement_complete',
    'completed_at' => ISO8601 timestamp,
]
```

---

## INTEGRATION WITH OTHER SYSTEMS

### 1. Weekly Reports Integration
**File:** `app/Http/Controllers/DailyReportController.php`

**Method:** `submitWeeklyToDocuments()`
- Generates PDF of weekly accomplishment report
- Finds "Weekly Accomplishment Report" requirement
- Creates StudentDocumentSubmission automatically
- Links generated PDF to document system
- Status: 'submitted' (requires coordinator review)

### 2. Placement Request Gating
**File:** `app/Http/Controllers/DocumentController.php`

**Logic:** Pre-placement requirements must be approved before student can proceed with placement
- Dashboard shows "Complete pre-requirements first" message
- Navigation may be restricted
- `checkPrePlacementCompletion()` sends notification when ready

### 3. Dashboard Integration
**File:** `resources/views/dashboard.blade.php`

**Features:**
- Shows pre-requirement completion status
- Progress indicator
- Link to documents page
- Conditional messaging based on completion

---

## SECURITY & PERMISSIONS

### Student Permissions
- ✅ View own document requirements
- ✅ Submit documents for requirements
- ✅ View own submissions
- ✅ Download own submissions
- ✅ Cancel own submissions (if not reviewed)
- ❌ View other students' submissions
- ❌ Review documents
- ❌ Modify requirements

### Coordinator Permissions
- ✅ View all requirements
- ✅ View submissions from students in their department
- ✅ Review submissions (approve/reject)
- ✅ Provide feedback
- ✅ Download submissions from their department
- ✅ Bulk review multiple submissions
- ❌ View submissions from other departments
- ❌ Modify requirements (would need admin)
- ❌ Delete submissions

### Permission Enforcement
```php
// Student ownership check
if ($user->isStudent() && $submission->student_user_id !== $user->id) {
    abort(403);
}

// Coordinator department check
if ($user->isCoordinator()) {
    $student = User::find($submission->student_user_id);
    $department = $user->coordinatorProfile?->department;
    if ($student->studentProfile?->department !== $department) {
        abort(403);
    }
}
```

---

## VALIDATION RULES

### File Upload Validation
```php
'files' => [
    'required',
    'array',
    'min:1',
    'max:' . $maxFiles, // Dynamic based on requirement
],
'files.*' => [
    'required',
    'file',
    'max:' . $requirement->max_file_size_mb * 1024, // MB to KB
    function ($attribute, $value, $fail) use ($requirement) {
        if ($requirement->file_types && !in_array($value->getClientOriginalExtension(), $requirement->file_types)) {
            $fail('File type must be one of: ' . implode(', ', $requirement->file_types));
        }
    },
],
```

### Review Validation
```php
'status' => ['required', 'in:approved,rejected'],
'feedback' => ['nullable', 'string', 'max:1000'],
```

### Bulk Review Validation
```php
'submission_ids' => ['required', 'array', 'min:1'],
'submission_ids.*' => ['exists:student_document_submissions,id'],
'status' => ['required', 'in:approved,rejected'],
'feedback' => ['nullable', 'string', 'max:1000'],
```

---

## CURRENT SUBMISSION LIMITS

### Default Limits
- **Max Files Per Requirement:** 2 (default)
- **Max File Size:** Varies by requirement (3-10 MB)

### Special Cases
- **Weekly Accomplishment Report:** 4 files (one per week for a month)
- **Photo Documentation:** 50 files (multiple photos)

### How Limits Work
1. System counts existing non-rejected submissions
2. Rejected submissions don't count (allows resubmission)
3. New uploads + existing must not exceed max_files_per_submission
4. Error shown if limit would be exceeded

---

## WORKFLOW SUMMARY

### Student Workflow
1. Student logs in and navigates to Documents
2. Views list of requirements grouped by type
3. Clicks "Submit Now" on a requirement
4. Uploads file(s) following specifications
5. System validates and stores files
6. Coordinator receives notification
7. Student can view submission status
8. If rejected, student can resubmit
9. If approved, requirement is marked complete
10. When all pre-placement approved, student gets notification

### Coordinator Workflow
1. Coordinator logs in and navigates to Document Review
2. Views queue of pending submissions
3. Can switch between different view modes
4. Clicks "Review" on a submission
5. Views/downloads file
6. Selects approved or rejected
7. Optionally adds feedback
8. Submits review
9. Student receives notification
10. System checks if student completed all pre-placement requirements

---

## POTENTIAL ISSUES & CONSIDERATIONS

### Current Limitations
1. **No Document Versioning** - If student resubmits, old file is deleted
2. **No Audit Trail** - Can't see history of status changes
3. **No Batch Upload** - Students must upload files one requirement at a time
4. **No Document Templates** - No downloadable forms/templates
5. **No Expiration Dates** - Documents don't expire (e.g., medical cert valid 6 months)
6. **No File Preview in List** - Must click through to view
7. **No Coordinator Comments Thread** - Only single feedback field
8. **No Student Response to Feedback** - One-way communication

### Performance Considerations
1. Large file uploads may timeout
2. Many submissions could slow coordinator view
3. No pagination on submission lists
4. All students/submissions loaded at once in coordinator view

### Security Considerations
1. File type validation is client-side and server-side
2. No virus scanning on uploads
3. Files stored in public disk (accessible if path known)
4. No encryption at rest

---

## STATISTICS & METRICS

### Trackable Metrics
- Total submissions per student
- Total submissions per requirement
- Average review time
- Approval/rejection rates
- Pre-placement completion rate
- Documents pending review
- Most commonly rejected documents

### Current Dashboard Stats (Coordinator View)
- Total submissions
- Pending submissions (status = submitted)
- Approved submissions
- Rejected submissions

---

## RECOMMENDATIONS FOR IMPROVEMENTS

### High Priority
1. **Add Document Versioning** - Keep history of all submissions
2. **Add Expiration Dates** - Flag expired documents (medical certs, etc.)
3. **Add Bulk Actions** - Allow coordinator to approve/reject multiple at once (partially exists)
4. **Add File Preview** - Show thumbnails or inline preview
5. **Add Downloadable Templates** - Provide forms students need to fill

### Medium Priority
6. **Add Audit Trail** - Log all status changes with timestamps
7. **Add Pagination** - For large lists of submissions
8. **Add Email Notifications** - In addition to in-app notifications
9. **Add Document Checklist Export** - PDF report of student's status
10. **Add Coordinator Notes** - Private notes not visible to students

### Low Priority
11. **Add Virus Scanning** - Scan uploaded files
12. **Add File Compression** - Automatically compress large images
13. **Add OCR** - Extract text from scanned documents
14. **Add Digital Signatures** - For official documents
15. **Add Document Sharing** - Between coordinators or with supervisors

---

## CONCLUSION

The document management system is well-structured with clear separation between student and coordinator workflows. It supports the three-phase OJT process (pre-placement, post-placement, ongoing) and includes essential features like multiple file uploads, review workflows, and notifications.

**Strengths:**
- Clean database design
- Flexible file limits per requirement
- Good permission enforcement
- Integration with other systems (weekly reports, placement gating)
- User-friendly interfaces for both students and coordinators

**Areas for Enhancement:**
- Document versioning and history
- Expiration tracking
- More robust audit trails
- Performance optimization for large datasets
- Enhanced security (virus scanning, encryption)

The system is production-ready for basic document management but would benefit from the recommended improvements for a more robust enterprise solution.
