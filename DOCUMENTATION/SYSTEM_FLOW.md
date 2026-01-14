# OJT360 - System Flow Documentation

## Table of Contents
1. [System Architecture](#system-architecture)
2. [Authentication Flow](#authentication-flow)
3. [Student OJT Journey](#student-ojt-journey)
4. [Coordinator Workflow](#coordinator-workflow)
5. [Supervisor Workflow](#supervisor-workflow)
6. [Data Flow Diagrams](#data-flow-diagrams)

---

## System Architecture

### High-Level Architecture
```
┌─────────────────────────────────────────────────────────┐
│                    PRESENTATION LAYER                    │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌─────────┐│
│  │ Student  │  │Coordinator│  │Supervisor│  │  Admin  ││
│  │Interface │  │ Interface │  │Interface │  │Interface││
│  └──────────┘  └──────────┘  └──────────┘  └─────────┘│
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│                   APPLICATION LAYER                      │
│  ┌────────────────┐         ┌────────────────┐         │
│  │  Controllers   │←───────→│   Middleware   │         │
│  └────────────────┘         └────────────────┘         │
│          ↓                           ↓                   │
│  ┌────────────────┐         ┌────────────────┐         │
│  │    Services    │         │  Notifications │         │
│  └────────────────┘         └────────────────┘         │
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│                     DATA LAYER                           │
│  ┌────────────────┐         ┌────────────────┐         │
│  │   Eloquent     │←───────→│     MySQL      │         │
│  │     ORM        │         │   Database     │         │
│  └────────────────┘         └────────────────┘         │
│                                                          │
│  ┌────────────────┐                                     │
│  │ File Storage   │ (Documents, PDFs, Images)          │
│  └────────────────┘                                     │
└─────────────────────────────────────────────────────────┘
```

---

## Authentication Flow

### 1. Student Registration (Whitelist-Based Activation)

**Prerequisites**: Coordinator must upload class list first!

```
PREREQUISITE STEP (Coordinator):
  Coordinator uploads class list (.xlsx or .csv)
    ↓
  System creates EnrollmentWhitelist records
    - student_id
    - name
    - email (@evsu.edu.ph)
    - program_id
    - contact_number
    - status: 'pending'
    ↓
  Students can now activate accounts
---

STUDENT ACTIVATION FLOW:
START → Student visits activation page
  ↓
Student enters Student ID
  ↓
System checks EnrollmentWhitelist
  ├─ Not found → Error: "Student ID not found or already activated"
  └─ Found (status='pending')
      ↓
  Generate verification token (60 min expiry)
      ↓
  Send email to student's school email (from whitelist)
      ↓
  "Verification link sent to your school email"
      ↓
Student checks email inbox
      ↓
Student clicks verification link
      ↓
┌─────────────────────────────┐
│  Token Validation           │
│  ├─ Expired → Show resend   │
│  ├─ Invalid → Error page    │
│  └─ Valid → Continue        │
└─────────────────────────────┘
      ↓
Registration form opens (pre-filled):
  - Name (from whitelist, read-only)
  - Email (from whitelist, read-only)
  - Student ID (from whitelist, read-only)
  - Department (from whitelist, read-only)
  - Program (from whitelist, read-only)
      ↓
Student fills in:
  - Password (minimum 8 characters)
  - Confirm Password
  - Phone Number
  - Address
  - Year Level (dropdown)
  - Section (dropdown based on program)
      ↓
Submit "Complete Registration"
      ↓
System creates:
  1. User record (role='intern')
  2. StudentProfile record
  3. Auto-verify email (email_verified_at = now())
  4. Update whitelist status to 'activated'
  5. Delete verification token
      ↓
Auto-login student
      ↓
Redirect to Dashboard
      ↓
END - Account Ready!
```

### 2. Coordinator Registration (Invitation-Based)

```
ADMIN INITIATES:
Admin creates coordinator invitation
  ↓
System creates CoordinatorInvitation record:
  - email
  - department_id
  - program_id
  - token (random 64 chars)
  - expires_at (1 hour)
  ↓
Send invitation email
---

COORDINATOR ACTIVATION:
Coordinator receives email
  ↓
Clicks invitation link
  ↓
┌─────────────────────────────┐
│  Token Validation           │
│  ├─ Expired → Show resend   │
│  ├─ Invalid → Error page    │
│  └─ Valid → Continue        │
└─────────────────────────────┘
      ↓
Registration form (pre-filled):
  - Email (read-only)
  - Department (read-only, from invitation)
  - Program (read-only, from invitation)
      ↓
Coordinator fills in:
  - Full Name
  - Employee ID (must be unique)
  - Phone Number
  - Password (minimum 8 characters)
  - Confirm Password
      ↓
Submit "Complete Registration"
      ↓
System creates:
  1. User record (role='coordinator')
  2. CoordinatorProfile record
  3. Auto-verify email
  4. Delete invitation record
      ↓
Auto-login coordinator
      ↓
Redirect to Coordinator Dashboard
      ↓
END
```

### 3. Supervisor Registration (Two Methods)

**Method A: Invitation from Coordinator**
```
Coordinator sends supervisor invitation
  ↓
Similar flow to coordinator invitation
  ↓
Supervisor receives email
  ↓
Clicks link → Complete registration
  ↓
Email auto-verified
```

**Method B: Self-Registration**
```
Supervisor visits public registration page
  ↓
Enters company email
  ↓
System sends verification email
  ↓
Supervisor clicks link
  ↓
Verifies email
  ↓
Completes registration with company details
  ↓
Account created
```

### 4. Login Process (All Roles)
```
START
  ↓
User enters email + password
  ↓
System checks credentials
  ├─ Invalid → Error message → RETRY
  └─ Valid
      ↓
  Check email verification
  ├─ Not verified (shouldn't happen with new flow) → Show  verification required
  └─ Verified (auto-verified during registration)
      ↓
  Check profile completion
  ├─ Incomplete → Redirect to profile
  └─ Complete
      ↓
  Create session
      ↓
  Redirect to role-specific dashboard
      ↓
  END
```

### 5. Middleware Chain
```
Request → Auth Middleware → Email Verified? → Profile Complete? → Role Check → Controller
                 ↓               ↓                 ↓                ↓
              [Fail]          [Fail]            [Fail]          [Fail]
                 ↓               ↓                 ↓                ↓
              Login Page    Verify Email      Complete Profile   403 Error
```

**Note**: With whitelist-based activation and invitation system, email verification happens automatically during registration, so users rarely see the email verification screen.

---

## Student OJT Journey

### Complete OJT Process Flow
```
┌──────────────────────────────────────────────────────────────┐
│ PHASE 1: PRE-PLACEMENT (Before OJT Starts)                  │
└──────────────────────────────────────────────────────────────┘
        ↓
[1] Student Registration & Profile Setup
        ↓
[2] Submit Required Documents
    ├─ Application Letter
    ├─ Resume/CV
    ├─ PDS (Personal Data Sheet)
    ├─ Medical Certificate
    ├─ Parent's Consent
    ├─ Barangay Clearance
    └─ NBI Clearance
        ↓
[3] Coordinator Reviews Documents
    ├─ Approve → Continue
    └─ Reject → Student revises → Resubmit
        ↓
[4] Find Company & Apply
        ↓
[5] Supervisor Issues Acceptance Letter
        ↓
[6] MOA (Memorandum of Agreement)
    - Coordinator notifies all students
    - Student receives notification
    - Student collects MOA from coordinator
        ↓
[7] Coordinator Activates OJT Status
        ↓
┌──────────────────────────────────────────────────────────────┐
│ PHASE 2: ACTIVE OJT (During Training)                       │
└──────────────────────────────────────────────────────────────┘
        ↓
[8] Daily Attendance Logging
    - Time In (start of day)
    - Time Out (end of day)
    - Automatic hours calculation
        ↓
[9] Weekly Report Submission
    - Every week: Submit accomplishments
    - Coordinator reviews
        ↓
[10] Monthly Evaluation
     - Supervisor rates performance
     - Coordinator reviews
     - Student receives notification
        ↓
[11] Repeat steps 8-10 until completion
        ↓
┌──────────────────────────────────────────────────────────────┐
│ PHASE 3: COMPLETION (End of OJT)                            │
└──────────────────────────────────────────────────────────────┘
        ↓
[12] Required Hours Met
        ↓
[13] Final Evaluation
     - Supervisor submits final rating
     - Coordinator reviews
        ↓
[14] OJT Status → Completed
        ↓
[15] Student downloads certificates/reports
        ↓
END
```

### Detailed Document Submission Flow
```
Student Dashboard
     ↓
Click "Documents"
     ↓
Select Document Type
     ↓
Upload File (PDF/DOCX)
     ↓
Add Notes (Optional)
     ↓
Click "Submit"
     ↓
     ┌─────────────┐
     │   Stored    │
     │  in System  │
     └─────────────┘
          ↓
Status: "Pending Review" (Yellow badge)
          ↓
Coordinator Notified
          ↓
┌─────────────────────────────┐
│   Coordinator Reviews       │
│  ┌──────────┬──────────┐   │
│  │ Approve  │  Reject  │   │
│  └──────────┴──────────┘   │
└─────────────────────────────┘
     ↓              ↓
  Approved       Rejected
     ↓              ↓
Green Badge    Red Badge
     ↓              ↓
 Continue      Student Revises
              & Resubmits
```

### Attendance Logging Flow (Quad-System)
The system uses a high-precision "Quad-Logging" verification system for maximum accuracy.

```
Student arrives at company
        ↓
Opens OJT360 on phone/computer
        ↓
Goes to "Attendance" page
        ↓
[1] MORNING PUNCH (AM IN)
    - Captures Live Photo
    - Captures GPS Coordinates
    - System records am_in_time
        ↓
... Working (Morning Shift) ...
        ↓
[2] LUNCH BREAK (AM OUT)
    - Captures Live Photo
    - Captures GPS Coordinates
    - System records am_out_time
    - Banks morning minutes
        ↓
[3] AFTERNOON RETURN (PM IN)
    - Captures Live Photo
    - Captures GPS Coordinates
    - System records pm_in_time
        ↓
[4] END OF DAY (PM OUT)
    - Captures Live Photo
    - Captures GPS Coordinates
    - System records pm_out_time
    - Calculates Total Minutes & Overtime
        ↓
Day complete. Hours added to student's total.
```

### Attendance Recovery Flow
If a student misses a punch (e.g., forgets to Time Out), they must use the Recovery System.

```
Student misses a punch
        ↓
Dashboard shows: "Incomplete Record" (Missing Out)
        ↓
Student clicks "Complete Recovery"
        ↓
Modal options:
 ├─ Recover Specific Punch (e.g., AM Out only)
 └─ Whole Day Recovery (Replaces AM/PM in one click)
        ↓
[1] Student inputs missing times
[2] Student uploads Photo Proof
[3] Student enters Reason
        ↓
Submit for approval
        ↓
Supervisor receives notification
        ↓
Supervisor Reviews:
 ├─ [Approve] → Time is validated and hours calculated
 └─ [Reject]  → Record remains incomplete; student must resubmit
```

---

## Coordinator Workflow

### Student Management Flow
```
Coordinator Dashboard
        ↓
View "Managed Students"
        ↓
Filter/Search Students
 - By status
 - By name/ID
 - By supervisor
        ↓
Select Student
        ↓
┌──────────────────────────┐
│   Student Profile View   │
│  ┌────────────────────┐ │
│  │ - OJT Status       │ │
│  │ - Documents        │ │
│  │ - Attendance       │ │
│  │ - Reports          │ │
│  │ - Evaluations      │ │
│  │ - Milestones       │ │
│  └────────────────────┘ │
└──────────────────────────┘
        ↓
Available Actions:
 - Review Documents
 - Assign Supervisor
 - Update Status
 - View Reports
```

### Document Review Workflow
```
Coordinator Dashboard
        ↓
Click "Document Checklist"
        ↓
See Pending Documents (Yellow 🟡)
        ↓
Click Document to Review
        ↓
View:
 - Student name
 - Document type
 - Uploaded file
 - Student notes
 - Submission date
        ↓
Download & Review Document
        ↓
Decision:
├─ [Approve]
│   ↓
│   Add approval comment (optional)
│   ↓
│   Submit
│   ↓
│   Status → Approved (Green ✅)
│   ↓
│   Student notified
│
└─ [Reject]
    ↓
    Add rejection reason (required)
    ↓
    Submit
    ↓
    Status → Rejected (Red ❌)
    ↓
    Student notified to resubmit
```

### MOA Notification Flow
```
Coordinator visits company
        ↓
Collects MOAs from all students
        ↓
Returns to office
        ↓
Opens OJT360 Dashboard
        ↓
Clicks "📄 MOA Notification" button
        ↓
Modal appears showing:
 - Program name
 - Number of students to notify
 - Warning about notifications
        ↓
Clicks "✓ Notify Students"
        ↓
System processes:
 ┌─────────────────────────┐
 │ For each student:       │
 │  Check if already       │
 │  notified?              │
 │   ├─ Yes → Skip         │
 │   └─ No → Send          │
 │           notification  │
 └─────────────────────────┘
        ↓
Results shown:
 "Notified X students (Y already notified)"
        ↓
Students see notification:
 "Your MOA is ready"
```

---

## Supervisor Workflow

### Student Acceptance Flow
```
Supervisor Dashboard
        ↓
Click "Accept Student"
        ↓
Enter student email/ID
        ↓
Click "Search"
        ↓
System searches database
        ↓
┌─────────────────────┐
│  Student Found?     │
│  ├─ Yes → Show info │
│  └─ No → Error msg  │
└─────────────────────┘
        ↓
Review student profile:
 - Name
 - Student ID
 - Program
 - Documents status
        ↓
Fill Acceptance Form:
 - OJT Start Date
 - OJT End Date
 - Department/Division
 - Position/Role
 - Supervisor Name
        ↓
Click "Generate Acceptance Letter"
        ↓
System creates acceptance record
        ↓
Student linked to supervisor
        ↓
Student receives notification
        ↓
Student can view acceptance letter
```

### Evaluation Submission Flow

#### Monthly Evaluation
```
End of Month
        ↓
Supervisor Dashboard
        ↓
Click "Submit Evaluation"
        ↓
Select Student from dropdown
        ↓
Rate Performance Areas (1-5):
 ├─ Work Quality
 ├─ Punctuality
 ├─ Initiative
 ├─ Cooperation
 └─ Technical Skills
        ↓
System auto-calculates:
 Average Rating = (Sum of all ratings) / 5
        ↓
Add Comments (required)
        ↓
Click "Submit"
        ↓
        ┌──────────────────────┐
        │   Stored in DB       │
        │  Status: Submitted   │
        └──────────────────────┘
        ↓
Coordinator receives notification
        ↓
Coordinator reviews → Marks as reviewed
        ↓
Student receives notification
        ↓
Student can view evaluation
```

#### Final Evaluation
```
Near OJT End (Student completed hours)
        ↓
Supervisor Dashboard
        ↓
Click "Final Evaluations"
        ↓
Click "Submit Final Evaluation"
        ↓
Select Student
        ↓
Complete Comprehensive Rating:
 ├─ Professional Skills
 ├─ Technical Competence
 ├─ Work Attitude
 ├─ Communication
 ├─ Problem Solving
 ├─ Teamwork
 ├─ Initiative
 ├─ Adaptability
 └─ Overall Performance
        ↓
Add Final Comments & Recommendations
        ↓
System calculates final grade
        ↓
Submit for coordinator review
        ↓
Status: "Pending Coordinator Review"
        ↓
Coordinator reviews
        ↓
┌──────────────────────────┐
│  Coordinator Decision    │
│  ├─ Approve → PDF        │
│  │  generated            │
│  └─ Request revision     │
└──────────────────────────┘
        ↓
Student notified
        ↓
Student downloads final evaluation PDF
```

---

## Data Flow Diagrams

### 1. Authentication Data Flow
```
┌─────────┐    Login Request     ┌────────────┐
│ Browser │─────────────────────→│ Controller │
└─────────┘                       └────────────┘
                                        ↓
                                  Validate Input
                                        ↓
                                  ┌──────────┐
                                  │   Hash   │
                                  │ Password │
                                  └──────────┘
                                        ↓
                                  ┌──────────┐
                                  │ Database │
                                  │  Query   │
                                  └──────────┘
                                        ↓
                                  Match Found?
                              ┌────────┴────────┐
                            YES                NO
                              ↓                 ↓
                        Create Session    Return Error
                              ↓
                        Set Cookie
                              ↓
                    ┌───────────────────┐
                    │ Return Dashboard  │
                    │      HTML         │
                    └───────────────────┘
```

### 2. File Upload Data Flow
```
Student Browser
      ↓
Select File (PDF/DOCX)
      ↓
      ┌──────────────────┐
      │ Client-side      │
      │ Validation       │
      │ - File type      │
      │ - File size      │
      └──────────────────┘
      ↓
POST to /documents/upload
      ↓
┌──────────────────────┐
│  Controller          │
│  - Validate request  │
│  - Check auth        │
│  - Validate file     │
└──────────────────────┘
      ↓
┌──────────────────────┐
│  Store File          │
│  storage/documents/  │
└──────────────────────┘
      ↓
┌──────────────────────┐
│  Database Insert     │
│  - File path         │
│  - Student ID        │
│  - Document type     │
│  - Status: Pending   │
└──────────────────────┘
      ↓
┌──────────────────────┐
│  Send Notification   │
│  to Coordinator      │
└──────────────────────┘
      ↓
Return Success Response
      ↓
Update UI → Show "Pending"
```

### 3. Notification System Flow
```
Trigger Event
(e.g., Document Approved)
      ↓
Create Notification Instance
      ↓
┌──────────────────────┐
│ MoaReadyNotification │
│ - User ID            │
│ - Type               │
│ - Message            │
│ - Action URL         │
└──────────────────────┘
      ↓
Insert to notifications table
      ↓
      ┌────────────┬────────────┐
      ↓            ↓            ↓
  Database     Email (if      Push (if
 Notification  configured)   configured)
      ↓
User checks notifications
      ↓
      ┌──────────────┐
      │ Bell Icon    │
      │ Shows Count  │
      └──────────────┘
      ↓
User clicks notification
      ↓
Redirect to action_url
      ↓
Mark as read
```

### 4. Report Generation Flow
```
Student submits weekly report
      ↓
POST /reports/weekly
      ↓
┌──────────────────────┐
│  Validate Data       │
│  - Week number       │
│  - Tasks             │
│  - Hours             │
└──────────────────────┘
      ↓
┌──────────────────────┐
│  Insert to DB        │
│  weekly_reports      │
│  - student_id        │
│  - week_number       │
│  - tasks             │
│  - hours             │
│  - status: submitted │
└──────────────────────┘
      ↓
┌──────────────────────┐
│  Notify Coordinator  │
└──────────────────────┘
      ↓
Coordinator reviews
      ↓
Updates status: reviewed
      ↓
┌──────────────────────┐
│  Notify Student      │
│  "Report reviewed"   │
└──────────────────────┘
```

### 5. PDF Generation Flow
```
Request PDF
(e.g., Final Evaluation)
      ↓
Controller receives request
      ↓
Authorize user access
      ↓
Retrieve data from database
 ├─ Evaluation record
 ├─ Student info
 └─ Supervisor info
      ↓
Pass data to PDF Service
      ↓
┌──────────────────────────┐
│ FinalEvaluationPdfService│
│  - Load template         │
│  - Populate data         │
│  - Format layout         │
│  - Add headers/footers   │
│  - Generate PDF binary   │
└──────────────────────────┘
      ↓
Return PDF as response
      ↓
Set headers:
 - Content-Type: application/pdf
 - Content-Disposition: inline
      ↓
Browser displays/downloads PDF
```

---

## System States & Transitions

### Student Status States
```
┌─────────────┐
│ Registered  │ (Initial state)
└──────┬──────┘
       ↓
   Documents
   Submitted
       ↓
┌──────────────┐
│   Pending    │ (Waiting for placement)
└──────┬───────┘
       ↓
   Acceptance
   Letter
   Received
       ↓
┌──────────────┐
│    Active    │ (OJT in progress)
└──────┬───────┘
       ↓
   Required
   Hours
   Completed
       ↓
┌──────────────┐
│  Completed   │ (OJT finished)
└──────────────┘
```

### Document Status States
```
┌──────────────┐
│   Uploaded   │
└──────┬───────┘
       ↓
┌──────────────┐
│   Pending    │ (Awaiting review)
└──────┬───────┘
       ↓
Coordinator
Reviews
       │
       ├──────────┐
       ↓          ↓
┌──────────┐  ┌──────────┐
│Approved  │  │ Rejected │
└──────────┘  └────┬─────┘
                   ↓
              Student
              Revises
                   ↓
              Reupload
                   ↓
            Back to Pending
```

---

## Integration Points

### External System Interfaces
```
┌──────────────────────────────────────────────┐
│           OJT360 Core System                 │
└──────────────────────────────────────────────┘
              ↕ (SMTP)
┌──────────────────────────────────────────────┐
│         Email Service Provider               │
│     (Mailgun / SendGrid / SMTP)              │
└──────────────────────────────────────────────┘

┌──────────────────────────────────────────────┐
│           OJT360 Core System                 │
└──────────────────────────────────────────────┘
              ↕ (File I/O)
┌──────────────────────────────────────────────┐
│         File Storage System                  │
│     (Local / S3 / Cloud Storage)             │
└──────────────────────────────────────────────┘

┌──────────────────────────────────────────────┐
│           OJT360 Core System                 │
└──────────────────────────────────────────────┘
              ↕ (MySQL Protocol)
┌──────────────────────────────────────────────┐
│          MySQL Database Server               │
└──────────────────────────────────────────────┘
```

---

## Error Handling Flow

### General Error Flow
```
User Action
    ↓
Request Sent
    ↓
┌──────────────────┐
│  Try Operation   │
└────────┬─────────┘
         │
    ┌────┴────┐
    ↓         ↓
Success    Exception
    ↓         ↓
 Return   Log Error
 Success      ↓
 Response  ┌──────────────┐
           │Show User-    │
           │Friendly Error│
           └──────────────┘
                ↓
           Rollback
           Transaction
           (if needed)
                ↓
           Return Error
           Response
```

---

**Last Updated**: January 12, 2026  
**Version**: 1.0.0
