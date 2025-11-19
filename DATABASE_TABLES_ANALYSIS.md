# Database Tables Usage Analysis - FINAL

## Tables Checked

### 1. `personal_access_tokens` ✅ KEEP
**Status:** Laravel Sanctum default table
**Usage:** Used for API token authentication (Laravel built-in)
**Your Question:** "Do I need to touch it?"
**Answer:** ✅ **NO - Don't touch it!** This is a standard Laravel/Sanctum table. Even if you're not using API tokens now, it's part of Laravel's core and should remain.

---

### 2. `password_resets` ✅ KEEP
**Status:** Laravel default table - ACTIVELY USED
**Usage:** 
- Referenced in `config/auth.php` for password reset functionality
- Standard Laravel password reset mechanism
**Recommendation:** ✅ KEEP - Essential for password reset functionality.

---

### 3. `student_verifications` ✅ KEEP - ACTIVELY USED
**Status:** ACTIVELY USED in your current flow
**Your Question:** "Aren't I using student ID verification that sends email?"
**Answer:** ✅ **YES, you ARE using this!** Here's your exact flow:

**Your Current Flow:**
1. Student enters Student ID on verify page
2. System checks `enrollment_whitelist` table
3. Creates record in `student_verifications` with token
4. Sends email with verification link
5. Student clicks link → completes registration
6. User account created with `email_verified_at` auto-set
7. Token deleted from `student_verifications`

**Code Location:** `app/Http/Controllers/ActivationController.php`
- `sendVerification()` - Creates verification record
- `showComplete()` - Validates token
- `completeRegistration()` - Creates user & auto-verifies email

**Recommendation:** ✅ KEEP - This is your active student registration flow!

---

### 4. `student_application_materials` ❌ SAFE TO REMOVE
**Status:** Completely unused - 0 records
**Your Question:** "Is it safe to drop? Not used in current flow?"
**Answer:** ✅ **YES, 100% SAFE TO DROP!**

**Evidence:**
- ❌ NO controllers use it
- ❌ NO views reference it
- ❌ NO routes for it
- ❌ 0 records in database
- ❌ Model exists but never imported/used anywhere

**What replaced it:**
- `student_documents` table (for document management)
- `acceptance_letters` table (for acceptance workflow)

**Recommendation:** ❌ REMOVE - Create migration to drop this table

---

### 5. `supervisor_assignment_requests` ❌ SAFE TO REMOVE
**Status:** Old/abandoned flow - 0 records
**Your Question:** "Needs more review"
**Answer:** ✅ **SAFE TO REMOVE - Old workflow that was replaced**

**Evidence:**
- Table has 0 records
- Only used in 2 places with `Schema::hasTable()` guards (defensive coding)
- NO views or forms to create these requests
- NO routes for this feature
- Guards suggest it's optional/being phased out

**What it was for (OLD FLOW):**
Students would propose a supervisor (name + email) → Coordinator approves → System creates supervisor

**What replaced it (NEW FLOW):**
- `supervisor_registrations` table - Coordinator generates invite link
- `acceptance_letters` table - Supervisor accepts student directly
- Much cleaner workflow!

**Code using it:**
```php
// CoordinatorStudentController.php - Lines 195-198 & 297-300
if (Schema::hasTable('supervisor_assignment_requests')) {
    $latestProposal = SupervisorAssignmentRequest::where(...)->first();
}
```
These guards mean "if table exists, try to use it, otherwise skip" - classic sign of deprecated feature.

**Recommendation:** ❌ REMOVE - This is dead code from old workflow

---

## Summary

| Table | Status | Action |
|-------|--------|--------|
| `personal_access_tokens` | Standard Laravel | ✅ Keep |
| `password_resets` | Actively Used | ✅ Keep |
| `student_application_materials` | Unused | ❌ Remove |
| `student_verifications` | Actively Used | ✅ Keep |
| `supervisor_assignment_requests` | Partially Used | ⚠️ Review |

---

## Recommended Actions

1. **Remove `student_application_materials`** - Create a migration to drop this table
2. **Review `supervisor_assignment_requests`** - Determine if this is still part of the current workflow or if it can be removed


---

## Summary Table

| Table | Keep/Remove | Reason | Has Data? |
|-------|-------------|--------|-----------|
| `personal_access_tokens` | ✅ Keep | Laravel built-in (don't touch) | N/A |
| `password_resets` | ✅ Keep | Active password reset feature | N/A |
| `student_verifications` | ✅ Keep | **YOUR ACTIVE REGISTRATION FLOW** | Yes (temp) |
| `student_application_materials` | ❌ Remove | Never used, replaced by new system | No (0 records) |
| `supervisor_assignment_requests` | ❌ Remove | Old flow, replaced by new system | No (0 records) |

---

## Action Plan

### Step 1: Create migrations to drop unused tables
```bash
php artisan make:migration drop_student_application_materials_table
php artisan make:migration drop_supervisor_assignment_requests_table
```

### Step 2: Remove unused models
- Delete `app/Models/StudentApplicationMaterial.php`
- Delete `app/Models/SupervisorAssignmentRequest.php`

### Step 3: Clean up code references
- Remove `SupervisorAssignmentRequest` import from `CoordinatorStudentController.php`
- Remove the guarded code blocks that check for `supervisor_assignment_requests` table

### Step 4: Run migrations
```bash
php artisan migrate
```

---

## Final Answer to Your Questions

1. **personal_access_tokens** → ✅ Don't touch it (Laravel built-in)
2. **student_verifications** → ✅ Keep it! (Your active registration flow)
3. **student_application_materials** → ❌ Safe to drop (never used)
4. **supervisor_assignment_requests** → ❌ Safe to drop (old flow, 0 records)
