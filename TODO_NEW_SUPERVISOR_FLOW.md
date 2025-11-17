# TODO: New Supervisor-Initiated Acceptance Letter Flow

## 📋 Overview
**Adviser's Requirements:**
- Replace student-initiated flow with supervisor-initiated flow
- Students bring physical resume/application letter to company
- Student asks supervisor to register and accept them
- Supervisor registers → searches student by ID → accepts → generates letter

---

## 🔄 New Flow Design

### **Step 1: Student Preparation**
1. Student brings physical documents to company
2. Student gets accepted by company (offline)
3. Student asks supervisor to go online and accept them digitally

### **Step 2: Supervisor Registration** (Email Verification)
1. Supervisor visits registration page
2. Enters email address
3. Receives verification email with link
4. Clicks link to complete registration (name, company, position, etc.)
5. Account created and verified

### **Step 3: Supervisor Searches & Accepts Student**
1. Supervisor logs in to dashboard
2. Goes to "Accept Student" page
3. Searches student by Student ID
4. Views student's digital resume/application letter
5. Clicks "Accept & Generate Letter"
6. Fills acceptance letter form
7. Letter generated and sent to student

---

## ✅ TODO List

### 🔥 PHASE 0: CRITICAL FIXES (DO FIRST!)

#### **Fix Migration Dependencies** ⚠️ BLOCKING ISSUE
**Problem:** These migrations modify existing data and fail on fresh database:
- `2025_11_16_174857_split_application_letter_and_resume_requirements.php` (splits existing requirement)
- `2025_11_16_175810_add_display_order_to_document_requirements.php` (adds column + updates data)
- `2025_11_16_180929_update_max_files_per_submission_for_requirements.php` (updates existing data)

**Solution:**
- [ ] Delete the 3 problematic migrations
- [ ] Create ONE new migration: `add_display_order_to_document_requirements.php`
  - Adds `display_order` column (default 999)
  - No data manipulation (works on fresh DB)
- [ ] Handle data splitting in seeder instead (optional, for existing data)
- [ ] Test: `php artisan migrate:fresh`
- [ ] Verify all tables created correctly
- [ ] Commit migration fix

#### **Fix Template Issue** ⚠️ BLOCKING ISSUE
- [ ] Copy template from `storage/app/templates/` to Git-tracked location
- [ ] Options:
  - **Option A:** Move to `resources/templates/` (tracked by Git)
  - **Option B:** Move to `public/templates/` (tracked by Git)
  - **Option C:** Keep in storage but add to Git (add exception to .gitignore)
- [ ] Update template path in SupervisorAcceptanceController
- [ ] Test template loads correctly
- [ ] Add template to Git: `git add [template-path]`
- [ ] Commit template
- [ ] Test on friend's laptop: `git pull` → template should work
- [ ] Document template location in README

**Success Criteria:**
- ✅ Can run `php artisan migrate:fresh` on any machine without errors
- ✅ Can `git pull` on any machine and template works immediately
- ✅ No manual setup required after pulling from Git

---

### Phase 1: Remove Old Flow (After Phase 0)

---

### Phase 2: Remove Old Student-Initiated Flow
- [ ] **Remove Student-Initiated Request System**
  - [ ] Remove `acceptance_requests` table (keep for now, might repurpose)
  - [ ] Remove AcceptanceRequestController
  - [ ] Remove acceptance request views
  - [ ] Remove acceptance request routes
  - [ ] Remove "Request Acceptance Letter" button from student documents page
  - [ ] Remove acceptance request notifications

---

### Phase 3: Supervisor Self-Registration (Email Verification)
- [ ] **Create Registration Flow**
  - [ ] Create `/register/supervisor` route (public)
  - [ ] Create email input page (Step 1)
  - [ ] Generate verification token
  - [ ] Send verification email with link
  - [ ] Create verification link handler
  - [ ] Create complete registration form (Step 2)
    - Name
    - Email (pre-filled, readonly)
    - Password
    - Company name
    - Company address
    - Position
    - Phone
  - [ ] Create supervisor account
  - [ ] Auto-login after registration
  - [ ] Redirect to dashboard

- [ ] **Email Template**
  - [ ] Create SupervisorRegistrationVerification email
  - [ ] Design email with verification link
  - [ ] Add expiration (24 hours)

---

### Phase 4: Student Search & Accept Feature
- [ ] **Create Search Page**
  - [ ] Create `/supervisor/students/search` route
  - [ ] Create search view with Student ID input
  - [ ] Add search functionality
  - [ ] Display student info:
    - Name
    - Student ID
    - Course/Program
    - Department
    - Email
    - Digital resume (if uploaded)
    - Digital application letter (if uploaded)
  - [ ] Add "Accept & Generate Letter" button

- [ ] **Student Document Viewing**
  - [ ] Show student's uploaded resume
  - [ ] Show student's uploaded application letter
  - [ ] Add download buttons for documents
  - [ ] Handle case when documents not uploaded

- [ ] **Accept & Generate Letter**
  - [ ] Create acceptance letter form (reuse existing)
  - [ ] Pre-fill student information
  - [ ] Generate PDF with template
  - [ ] Save acceptance letter
  - [ ] Link supervisor to student
  - [ ] Send notification to student
  - [ ] Send notification to coordinator
  - [ ] Redirect to success page

---

### Phase 5: Update Dashboard & Navigation
- [ ] **Supervisor Dashboard**
  - [ ] Add "Accept Student" button/card
  - [ ] Show list of accepted students
  - [ ] Show generated letters count
  
- [ ] **Navigation Menu**
  - [ ] Add "Accept Student" menu item
  - [ ] Add "My Students" menu item
  - [ ] Remove old acceptance request links

---

### Phase 6: Database Changes
- [ ] **Update Tables**
  - [ ] Keep `acceptance_letters` table (no changes needed)
  - [ ] Keep `supervisor_profiles` table (no changes needed)
  - [ ] Keep `student_profiles` table (no changes needed)
  - [ ] Decide: Keep or remove `acceptance_requests` table?
    - Option A: Remove completely
    - Option B: Repurpose for tracking supervisor-student relationships

- [ ] **Add New Table (Optional)**
  - [ ] `supervisor_registrations` table for email verification tokens
    - id
    - email
    - token
    - expires_at
    - verified_at
    - created_at

---

### Phase 7: Testing & Validation
- [ ] **Test Registration Flow**
  - [ ] Test email sending
  - [ ] Test verification link
  - [ ] Test account creation
  - [ ] Test duplicate email handling
  - [ ] Test expired token handling

- [ ] **Test Search & Accept**
  - [ ] Test student search by ID
  - [ ] Test viewing student documents
  - [ ] Test letter generation
  - [ ] Test notifications
  - [ ] Test supervisor-student linking

- [ ] **Test Edge Cases**
  - [ ] Student not found
  - [ ] Student already accepted by another supervisor
  - [ ] Student has no documents uploaded
  - [ ] Supervisor tries to accept same student twice

---

## 🎯 Key Decisions Needed

### 1. **Acceptance Requests Table**
**Question:** Keep or remove `acceptance_requests` table?
- **Option A:** Remove completely (clean slate)
- **Option B:** Repurpose for tracking which students are "pending acceptance"
- **Recommendation:** Remove for now, can add back if needed

### 2. **Student Document Requirements**
**Question:** Should students be required to upload resume/application letter before supervisor can accept them?
- **Option A:** Required (supervisor can't accept without documents)
- **Option B:** Optional (supervisor can accept even without digital documents)
- **Recommendation:** Optional (student brings physical, digital is bonus)

### 3. **Multiple Supervisors**
**Question:** Can a student be accepted by multiple supervisors?
- **Option A:** No, one supervisor per student
- **Option B:** Yes, multiple supervisors (different companies)
- **Recommendation:** One supervisor per student (current behavior)

### 4. **Supervisor Verification**
**Question:** Should coordinators verify/approve supervisor accounts?
- **Option A:** Auto-approved after email verification
- **Option B:** Coordinator must approve
- **Recommendation:** Auto-approved (faster, less friction)

---

## 📝 Files to Modify

### **Remove/Update:**
- `app/Http/Controllers/AcceptanceRequestController.php` - DELETE
- `resources/views/acceptance/request.blade.php` - DELETE
- `routes/web.php` - Remove acceptance request routes
- `resources/views/documents/index.blade.php` - Remove "Request Acceptance Letter" button

### **Create New:**
- `app/Http/Controllers/SupervisorRegistrationController.php`
- `app/Mail/SupervisorRegistrationVerification.php`
- `resources/views/supervisor/register/email.blade.php`
- `resources/views/supervisor/register/complete.blade.php`
- `resources/views/supervisor/students/search.blade.php`
- `resources/views/supervisor/students/view.blade.php`
- `database/migrations/xxxx_create_supervisor_registrations_table.php` (optional)

### **Modify:**
- `app/Http/Controllers/SupervisorAcceptanceController.php` - Update for new flow
- `resources/views/supervisor/acceptance/index.blade.php` - Update UI
- `resources/views/dashboard.blade.php` - Add supervisor features
- `resources/views/layouts/navigation.blade.php` - Update menu

---

## 🚀 Implementation Order

1. **PHASE 0** - Fix migrations & template (CRITICAL - DO FIRST!)
2. **Phase 3** - Supervisor registration (NEW ENTRY POINT)
3. **Phase 4** - Student search & accept (CORE FEATURE)
4. **Phase 5** - Dashboard updates (UX)
5. **Phase 2** - Remove old flow (CLEANUP)
6. **Phase 7** - Testing (VALIDATION)

---

## ⚠️ Breaking Changes

**What will break:**
- Existing acceptance request links in emails (will 404)
- Student "Request Acceptance Letter" button (will be removed)
- Any pending acceptance requests (will be orphaned)

**Migration Strategy:**
- Inform users of new flow
- Clear pending requests
- Update documentation

---

**Created:** November 17, 2025
**Status:** Planning Phase - Awaiting Approval
