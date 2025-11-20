# Acceptance Letter Template Implementation Guide

## 📁 File Organization

### Step 1: Place Your 12 Templates in `resources/templates/`

**Naming Convention:** Use simple, consistent names

```
resources/templates/
├── BSIT-acceptance-letter.pdf                    (existing, rename from BSITacceptancelettertemplate.pdf)
├── BEED-acceptance-letter.pdf                    (Bachelor of Elementary Education)
├── BIT-CA-acceptance-letter.pdf                  (BIT major in Culinary Arts)
├── BIT-ET-acceptance-letter.pdf                  (BIT major in Electronics)
├── BPEd-acceptance-letter.pdf                    (Bachelor of Physical Education)
├── BSCE-acceptance-letter.pdf                    (BS Civil Engineering)
├── BSEE-acceptance-letter.pdf                    (BS Electrical Engineering)
├── BSHM-acceptance-letter.pdf                    (BS Hospitality Management)
├── BSME-acceptance-letter.pdf                    (BS Mechanical Engineering)
├── BSEd-Math-acceptance-letter.pdf               (BSEd major in Mathematics)
├── BSEd-Science-acceptance-letter.pdf            (BSEd major in Science)
├── BTVTEd-acceptance-letter.pdf                  (Bachelor of Technical-Vocational Teacher Education)
└── DTS-acceptance-letter.pdf                     (Diploma in Teaching Secondary)
```

---

## 🔍 Program Detection Logic

The system will match student's course name to the correct template using keyword matching.

### Program Mapping:

| Student's Course Name | Template File | Code |
|----------------------|---------------|------|
| Bachelor of Science in Information Technology | BSIT-acceptance-letter.pdf | BSIT |
| Bachelor of Elementary Education | BEED-acceptance-letter.pdf | BEED |
| Bachelor of Industrial Technology major in Culinary Arts | BIT-CA-acceptance-letter.pdf | BIT-CA |
| Bachelor of Industrial Technology major in Electronics | BIT-ET-acceptance-letter.pdf | BIT-ET |
| Bachelor of Physical Education | BPEd-acceptance-letter.pdf | BPEd |
| Bachelor of Science in Civil Engineering | BSCE-acceptance-letter.pdf | BSCE |
| Bachelor of Science in Electrical Engineering | BSEE-acceptance-letter.pdf | BSEE |
| Bachelor of Science in Hospitality Management | BSHM-acceptance-letter.pdf | BSHM |
| Bachelor of Science in Mechanical Engineering | BSME-acceptance-letter.pdf | BSME |
| Bachelor of Secondary Education major in Mathematics | BSEd-Math-acceptance-letter.pdf | BSEd-Math |
| Bachelor of Secondary Education major in Science | BSEd-Science-acceptance-letter.pdf | BSEd-Science |
| Bachelor of Technical-Vocational Teacher Education | BTVTEd-acceptance-letter.pdf | BTVTEd |
| Diploma in Teaching Secondary | DTS-acceptance-letter.pdf | DTS |

---

## 💻 Code Implementation

### Add These Methods to `SupervisorAcceptanceController.php`

```php
/**
 * Get the appropriate template path based on student's course
 */
private function getTemplatePath($course)
{
    $programCode = $this->getProgramCode($course);
    
    // Build template path
    $templatePath = resource_path("templates/{$programCode}-acceptance-letter.pdf");
    
    // Check if template exists
    if (file_exists($templatePath)) {
        \Log::info("Using template for {$programCode}: {$templatePath}");
        return $templatePath;
    }
    
    // Fallback to BSIT template if program-specific not found
    $fallbackPath = resource_path('templates/BSIT-acceptance-letter.pdf');
    \Log::warning("Template not found for {$programCode}, using BSIT template");
    
    return $fallbackPath;
}

/**
 * Extract program code from course name
 */
private function getProgramCode($course)
{
    if (empty($course)) {
        return 'BSIT'; // Default fallback
    }
    
    $courseLower = strtolower(trim($course));
    
    // Exact matching for complex programs
    $exactMatches = [
        // BIT programs (check major first)
        'culinary arts' => 'BIT-CA',
        'electronics' => 'BIT-ET',
        
        // BSEd programs (check major)
        'mathematics' => 'BSEd-Math',
        'science' => 'BSEd-Science',
    ];
    
    // Check exact matches first (for majors)
    foreach ($exactMatches as $keyword => $code) {
        if (str_contains($courseLower, $keyword)) {
            return $code;
        }
    }
    
    // General program matching
    $programMatches = [
        'information technology' => 'BSIT',
        'elementary education' => 'BEED',
        'industrial technology' => 'BIT-CA', // Default BIT to Culinary Arts if no major specified
        'physical education' => 'BPEd',
        'civil engineering' => 'BSCE',
        'electrical engineering' => 'BSEE',
        'hospitality management' => 'BSHM',
        'mechanical engineering' => 'BSME',
        'secondary education' => 'BSEd-Math', // Default BSEd to Math if no major specified
        'technical-vocational teacher education' => 'BTVTEd',
        'teaching secondary' => 'DTS',
    ];
    
    foreach ($programMatches as $keyword => $code) {
        if (str_contains($courseLower, $keyword)) {
            return $code;
        }
    }
    
    // Default fallback
    \Log::warning("No matching program found for: {$course}");
    return 'BSIT';
}
```

### Update `generateAcceptanceLetter()` Method

**Find this line (around line 366):**
```php
$templatePath = resource_path('templates/BSITacceptancelettertemplate.pdf');
```

**Replace with:**
```php
$templatePath = $this->getTemplatePath($studentProfile->course);
```

**That's it! No other changes needed.**

---

## ✅ Implementation Checklist

### Step 1: Rename Existing Template
- [ ] Rename `BSITacceptancelettertemplate.pdf` to `BSIT-acceptance-letter.pdf`

### Step 2: Add New Templates
- [ ] Add `BEED-acceptance-letter.pdf`
- [ ] Add `BIT-CA-acceptance-letter.pdf`
- [ ] Add `BIT-ET-acceptance-letter.pdf`
- [ ] Add `BPEd-acceptance-letter.pdf`
- [ ] Add `BSCE-acceptance-letter.pdf`
- [ ] Add `BSEE-acceptance-letter.pdf`
- [ ] Add `BSHM-acceptance-letter.pdf`
- [ ] Add `BSME-acceptance-letter.pdf`
- [ ] Add `BSEd-Math-acceptance-letter.pdf`
- [ ] Add `BSEd-Science-acceptance-letter.pdf`
- [ ] Add `BTVTEd-acceptance-letter.pdf`
- [ ] Add `DTS-acceptance-letter.pdf`

### Step 3: Update Code
- [ ] Add `getTemplatePath()` method to SupervisorAcceptanceController
- [ ] Add `getProgramCode()` method to SupervisorAcceptanceController
- [ ] Update `generateAcceptanceLetter()` to use `getTemplatePath()`

### Step 4: Test Each Program
- [ ] Test BSIT student
- [ ] Test BEED student
- [ ] Test BIT-CA student
- [ ] Test BIT-ET student
- [ ] Test BPEd student
- [ ] Test BSCE student
- [ ] Test BSEE student
- [ ] Test BSHM student
- [ ] Test BSME student
- [ ] Test BSEd-Math student
- [ ] Test BSEd-Science student
- [ ] Test BTVTEd student
- [ ] Test DTS student

---

## 🧪 Testing Guide

### How to Test:

1. **Check student's course in database:**
   ```sql
   SELECT name, course FROM users 
   JOIN student_profiles ON users.id = student_profiles.user_id 
   WHERE role = 'intern';
   ```

2. **Generate acceptance letter for that student**

3. **Check logs to verify correct template:**
   ```
   storage/logs/laravel.log
   ```
   Look for: `"Using template for BSIT: ..."`

4. **Verify PDF generated correctly**

### Test Cases:

**Test 1: BSIT Student**
- Course: "Bachelor of Science in Information Technology"
- Expected: Uses `BSIT-acceptance-letter.pdf`

**Test 2: BIT with Major**
- Course: "Bachelor of Industrial Technology major in Culinary Arts"
- Expected: Uses `BIT-CA-acceptance-letter.pdf`

**Test 3: BSEd with Major**
- Course: "Bachelor of Secondary Education major in Mathematics"
- Expected: Uses `BSEd-Math-acceptance-letter.pdf`

**Test 4: Unknown Program**
- Course: "Some New Program"
- Expected: Falls back to `BSIT-acceptance-letter.pdf` (with warning in log)

---

## 🎓 Defense Points

### Q: How does the system know which template to use?
**A:** The system analyzes the student's course name using keyword matching. It checks for specific majors first (like "Culinary Arts" or "Mathematics"), then checks for general program names (like "Civil Engineering"). Each match corresponds to a specific template file.

### Q: What if a student's course name doesn't match exactly?
**A:** The system uses flexible keyword matching (e.g., "BS in Civil Engineering" and "Bachelor of Science in Civil Engineering" both match). If no match is found, it falls back to the BSIT template and logs a warning for administrator review.

### Q: What if we add a new program?
**A:** Simply add the new template PDF to the templates folder and add one line to the `getProgramCode()` method. No other code changes needed.

### Q: How do you ensure all templates have the same field positions?
**A:** All templates should be designed with the same coordinate system. The code uses the same coordinates for all templates, so fields appear in the same positions across all programs.

---

## 📝 Important Notes

### Template Coordinates
**DO NOT CHANGE** the coordinate values in the code. All templates must be designed to match these coordinates:

```php
// These coordinates are used for ALL templates
$writeField(50, 100, $studentName);      // Student name position
$writeField(50, 120, $companyName);      // Company name position
// ... etc
```

### Template Design Requirements
Each template PDF must:
- ✅ Have the same page size (Letter or A4)
- ✅ Have fillable areas at the same coordinates
- ✅ Use the same font sizes (or close enough)
- ✅ Have program-specific branding/logos
- ✅ Have program name hardcoded in the template

### File Naming Rules
- Use hyphens (-) not underscores (_)
- Use consistent capitalization
- Include "-acceptance-letter.pdf" suffix
- Keep names short but descriptive

---

## 🚀 Quick Implementation (Copy-Paste Ready)

### 1. Add to SupervisorAcceptanceController.php (after existing methods):

```php
/**
 * Get the appropriate template path based on student's course
 */
private function getTemplatePath($course)
{
    $programCode = $this->getProgramCode($course);
    $templatePath = resource_path("templates/{$programCode}-acceptance-letter.pdf");
    
    if (file_exists($templatePath)) {
        \Log::info("Using template for {$programCode}: {$templatePath}");
        return $templatePath;
    }
    
    $fallbackPath = resource_path('templates/BSIT-acceptance-letter.pdf');
    \Log::warning("Template not found for {$programCode}, using BSIT template");
    return $fallbackPath;
}

/**
 * Extract program code from course name
 */
private function getProgramCode($course)
{
    if (empty($course)) {
        return 'BSIT';
    }
    
    $courseLower = strtolower(trim($course));
    
    // Check for specific majors first
    $exactMatches = [
        'culinary arts' => 'BIT-CA',
        'electronics' => 'BIT-ET',
        'mathematics' => 'BSEd-Math',
        'science' => 'BSEd-Science',
    ];
    
    foreach ($exactMatches as $keyword => $code) {
        if (str_contains($courseLower, $keyword)) {
            return $code;
        }
    }
    
    // Check for general programs
    $programMatches = [
        'information technology' => 'BSIT',
        'elementary education' => 'BEED',
        'industrial technology' => 'BIT-CA',
        'physical education' => 'BPEd',
        'civil engineering' => 'BSCE',
        'electrical engineering' => 'BSEE',
        'hospitality management' => 'BSHM',
        'mechanical engineering' => 'BSME',
        'secondary education' => 'BSEd-Math',
        'technical-vocational teacher education' => 'BTVTEd',
        'teaching secondary' => 'DTS',
    ];
    
    foreach ($programMatches as $keyword => $code) {
        if (str_contains($courseLower, $keyword)) {
            return $code;
        }
    }
    
    \Log::warning("No matching program found for: {$course}");
    return 'BSIT';
}
```

### 2. Update generateAcceptanceLetter() method:

**Find line 366:**
```php
$templatePath = resource_path('templates/BSITacceptancelettertemplate.pdf');
```

**Replace with:**
```php
$templatePath = $this->getTemplatePath($studentProfile->course);
```

### 3. Done! 🎉

---

## 📂 Final File Structure

```
resources/
└── templates/
    ├── BSIT-acceptance-letter.pdf
    ├── BEED-acceptance-letter.pdf
    ├── BIT-CA-acceptance-letter.pdf
    ├── BIT-ET-acceptance-letter.pdf
    ├── BPEd-acceptance-letter.pdf
    ├── BSCE-acceptance-letter.pdf
    ├── BSEE-acceptance-letter.pdf
    ├── BSHM-acceptance-letter.pdf
    ├── BSME-acceptance-letter.pdf
    ├── BSEd-Math-acceptance-letter.pdf
    ├── BSEd-Science-acceptance-letter.pdf
    ├── BTVTEd-acceptance-letter.pdf
    └── DTS-acceptance-letter.pdf
```

Total: 13 templates (12 new + 1 renamed existing)
