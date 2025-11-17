# Implementation Plan: New Supervisor Flow

## 🎯 Goal
Replace student-initiated acceptance request flow with supervisor-initiated flow where supervisors self-register and search for students.

---

## 📋 Step-by-Step Implementation

### STEP 1: Create Supervisor Registration (Email Verification)

#### 1.1 Create Migration for Verification Tokens
```
php artisan make:migration create_supervisor_registrations_table
```
**Columns:**
- id
- email (unique)
- token (unique)
- expires_at
- verified_at (nullable)
- created_at, updated_at

#### 1.2 Create Model
- `app/Models/SupervisorRegistration.php`

#### 1.3 Create Controller
- `app/Http/Controllers/SupervisorRegistrationController.php`
**Methods:**
- `showEmailForm()` - Show email input page
- `sendVerification()` - Generate token, send email
- `verify($token)` - Verify token, show complete registration form
- `complete()` - Create account, login, redirect to dashboard

#### 1.4 Create Email
- `app/Mail/SupervisorVerificationEmail.php`

#### 1.5 Create Views
- `resources/views/supervisor/register/email.blade.php` - Email input
- `resources/views/supervisor/register/complete.blade.php` - Complete registration form

#### 1.6 Add Routes
```php
// Public routes
Route::get('/register/supervisor', 'showEmailForm')->name('supervisor.register');
Route::post('/register/supervisor/send', 'sendVerification')->name('supervisor.register.send');
Route::get('/register/supervisor/verify/{token}', 'verify')->name('supervisor.register.verify');
Route::post('/register/supervisor/complete', 'complete')->name('supervisor.register.complete');
```

---

### STEP 2: Create Student Search & Accept Feature

#### 2.1 Update SupervisorAcceptanceController
**New Methods:**
- `searchForm()` - Show search page
- `search(Request $request)` - Search student by ID
- `viewStudent($studentId)` - Show student details + documents
- `acceptStudent($studentId)` - Show acceptance letter form
- `generateLetter(Request $request, $studentId)` - Generate letter

#### 2.2 Create Views
- `resources/views/supervisor/students/search.blade.php` - Search form
- `resources/views/supervisor/students/view.blade.php` - Student details + documents
- `resources/views/supervisor/acceptance/generate.blade.php` - Letter form (reuse existing create.blade.php)

#### 2.3 Add Routes
```php
Route::middleware(['auth', 'supervisor'])->group(function () {
    Route::get('/supervisor/students/search', 'searchForm')->name('supervisor.students.search');
    Route::post('/supervisor/students/search', 'search')->name('supervisor.students.search.post');
    Route::get('/supervisor/students/{student}', 'viewStudent')->name('supervisor.students.view');
    Route::get('/supervisor/students/{student}/accept', 'acceptStudent')->name('supervisor.students.accept');
    Route::post('/supervisor/students/{student}/generate-letter', 'generateLetter')->name('supervisor.students.generate');
});
```

---

### STEP 3: Remove Old Acceptance Request System

#### 3.1 Delete Files
- [ ] `app/Http/Controllers/AcceptanceRequestController.php`
- [ ] `resources/views/acceptance/request.blade.php`
- [ ] `app/Mail/SupervisorAcceptanceInvitation.php`
- [ ] `resources/views/emails/supervisor-acceptance-invitation.blade.php`
- [ ] `resources/views/supervisor/acceptance/register.blade.php`
- [ ] `resources/views/supervisor/acceptance/expired.blade.php`
- [ ] `resources/views/supervisor/acceptance/resent.blade.php`
- [ ] `resources/views/supervisor/acceptance/error.blade.php`

#### 3.2 Remove Routes
- Remove acceptance request routes from `routes/web.php`
- Remove old supervisor acceptance routes (show, register, resend)

#### 3.3 Update Views
- Remove "Request Acceptance Letter" button from `resources/views/documents/index.blade.php`
- Update supervisor dashboard to remove old acceptance request features

#### 3.4 Database (Optional)
- Keep `acceptance_requests` table for now (might be useful for history)
- Or create migration to drop it

---

### STEP 4: Update Dashboard & Navigation

#### 4.1 Supervisor Dashboard
- Add "Accept Student" card/button
- Update stats to show accepted students count
- Remove old "Pending Requests" stat

#### 4.2 Navigation
- Add "Accept Student" menu item
- Keep "My Students" menu item
- Remove old acceptance request links

---

## 🔄 New Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│ STUDENT (Offline)                                           │
│ 1. Brings physical resume/application to company           │
│ 2. Gets accepted by company                                │
│ 3. Asks supervisor to register online and accept them      │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ SUPERVISOR REGISTRATION                                     │
│ 1. Goes to /register/supervisor                            │
│ 2. Enters email                                            │
│ 3. Receives verification email                             │
│ 4. Clicks link                                             │
│ 5. Completes registration (name, company, position, etc.)  │
│ 6. Account created & logged in                             │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ SUPERVISOR SEARCHES & ACCEPTS STUDENT                       │
│ 1. Logs in to dashboard                                    │
│ 2. Clicks "Accept Student"                                 │
│ 3. Enters student ID (e.g., 2022-31481)                    │
│ 4. Views student profile + digital documents               │
│ 5. Clicks "Accept & Generate Letter"                       │
│ 6. Fills acceptance letter form                            │
│ 7. Letter generated                                        │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ NOTIFICATIONS & LINKING                                     │
│ - Student receives notification + email                    │
│ - Coordinator receives notification                        │
│ - Supervisor linked to student                             │
│ - Letter added to student documents                        │
└─────────────────────────────────────────────────────────────┘
```

---

## ⚠️ Important Notes

1. **Student ID Format:** Need to confirm format (e.g., "2022-31481")
2. **Student Documents:** Optional or required for acceptance?
3. **Duplicate Prevention:** Check if student already has supervisor
4. **Email Verification:** 24-hour expiration for registration links

---

**Ready to implement?** Let me know and I'll start with Step 1!
