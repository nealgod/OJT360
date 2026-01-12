# Performance Analysis: Resume & Application Letter Slowness

## Investigation Date: 2025-12-03

### User Report:
"System is slow especially during resume creation/editing and application letter saves. Takes a while to save."

---

## Findings & Root Causes:

### 1. **JSON Column Overhead** ⚠️ HIGH IMPACT
**Location:** `database/migrations/2025_11_13_134005_create_resumes_table.php`

**Problem:**
- Resume table has **6 JSON columns** that serialize/deserialize on every save:
  - `personal_info` (JSON)
  - `education` (JSON)
  - `work_experience` (JSON)
  - `skills` (JSON)
  - `certifications` (JSON)
  - `references` (JSON)

**Why It's Slow:**
- `JSON` column type in MySQL/MariaDB requires:
  - Validation on write
  - Serialization/deserialization overhead
  - No indexing possible on JSON content
  - Slower than TEXT columns

**Evidence:**
```php
// ResumeController line 156-165
$resume = Resume::create([
    'user_id' => $user->id,
    'personal_info' => $personalInfo,      // JSON encode
    'objective' => ...,
    'education' => $education,             // JSON encode
    'work_experience' => $workExperience,  // JSON encode
    'skills' => $skills,                   // JSON encode
    'certifications' => $certifications,   // JSON encode
    'profile_image' => $profileImagePath,
]);
```

Each array is being JSON encoded during save, validated by MySQL, then stored.

---

### 2. **Session Driver: File-based** ⚠️ MEDIUM IMPACT
**Location:** `config/session.php` line 21

**Current Setting:**
```php
'driver' => env('SESSION_DRIVER', 'file'),
```

**Problem:**
- File-based sessions on **Windows/XAMPP** are slow
- Each request reads/writes to disk
- `storage/framework/sessions` directory can become cluttered

**Better Options:**
- `database` - Store sessions in database (faster on most servers)
- `cookie` - Store in encrypted cookies (fastest for small data)

---

### 3. **Large Data Collection Processing** ⚠️ MEDIUM IMPACT
**Location:** `ResumeController::store()` and `ResumeController::update()`

**Problem:**
Lines 105-149: Heavy data sanitization using Laravel Collections:
```php
$education = collect($validated['education'] ?? [])->map(function ($edu) {
    // ... complex nested logic for each education entry
})->filter()->values()->all();

$workExperience = collect(...)->map(...)->filter()->values()->all();
$skills = collect(...)->map(...)->filter()->values()->all();
$certifications = collect(...)->map(...)->filter()->values()->all();
```

**Why It's Slow:**
- Creating multiple collection objects
- Multiple iterations (map → filter → values → all)
- Doing this for 4arate arrays on EVERY save

---

### 4. **Middleware Stack** ⚠️ LOW-MEDIUM IMPACT
**Location:** `routes/web.php` line 36, 105, 115

**Current Middleware:**
```php
Route::middleware(['auth', 'profile.complete'])->group(function () {
    Route::post('/student-documents/resume', ...);
    Route::patch('/student-documents/application-letter/{letter}', ...);
});
```

**Each Request Runs:**
1. `web` middleware (sessions, CSRF, cookies)
2. `auth` middleware (user authentication check)
3. `profile.complete` middleware (custom check)
4. Route-level validation
5. Controller authorization checks

---

### 5. **No Database Indexes on Foreign Keys** ⚠️ LOW IMPACT
**Location:** Migration files

**Current:**
```php
$table->foreignId('user_id')->constrained()->onDelete('cascade');
```

**Good News:** Foreign keys automatically create indexes in MySQL ✓

**But:** No indexes on:
- `resumes.created_at` (if querying by date)
- `application_letters.created_at`

---

### 6. **Session Configuration Issues** ⚠️ LOW IMPACT
**Location:** `config/session.php`

**Potential Issues:**
```php
'lifetime' => env('SESSION_LIFETIME', 120),      // 2 hours
'expire_on_close' => true,                       // Conflicts with lifetime
'encrypt' => false,                              // OK for local
'same_site' => 'strict',                         // Very restrictive
```

The `expire_on_close => true` setting means sessions are deleted when browser closes, making the 120-minute lifetime meaningless.

---

## Performance Impact Ranking:

### 🔴 **Critical (Fix First)**
1. **JSON Column Overhead** - Converting to LONGTEXT would speed up writes significantly

### 🟡 **Medium (Should Fix)**
2. **Session Driver** - Switch to `database` or `cookie`
3. **Data Collection Processing** - Optimize sanitization logic

### 🟢 **Low (Nice to Have)**
4. **Add Indexes** - On `created_at` columns if sorting/filtering by date
5. **Session Config** - Fix `expire_on_close` conflict

---

## NOT The Problem:

✅ **Image Upload** - Only stores path, no processing during save  
✅ **PDF Generation** - Only happens on download, not save  
✅ **No Model Observers** - No hidden event listeners slowing saves  
✅ **APP_DEBUG** - User confirmed it's already false  
✅ **No Heavy Middleware** - Standard Laravel middleware only  

---

## Recommended Solutions:

### **Quick Win #1: Optimize Session Driver** (5 minutes)
Change in `.env`:
```env
SESSION_DRIVER=database
```

Then run:
```bash
php artisan session:table
php artisan migrate
```

### **Quick Win #2: Fix Session Config** (2 minutes)
In `config/session.php`:
```php
'expire_on_close' => false,  // Let lifetime handle expiration
```

### **Medium Fix: Optimize Data Processing** (30 minutes)
Replace heavy collection chains with simpler loops or array_map/array_filter

### **Long-term Fix: Database Schema Change** (1 hour + testing)
Change JSON columns to LONGTEXT:
- Still stores JSON strings
- Laravel still casts to arrays automatically
- But MySQL doesn't validate/index, making writes faster

---

## Next Steps:
1. User to implement Quick Wins first
2. Test and measure performance improvement
3. If still slow, implement Medium/Long-term fixes

