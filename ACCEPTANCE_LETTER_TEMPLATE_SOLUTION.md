# Acceptance Letter Template Solution

## 🎯 Problem
Currently, the system uses a hardcoded BSIT template:
```php
$templatePath = resource_path('templates/BSITacceptancelettertemplate.pdf');
```

This won't work for other programs (BSCS, BSIS, BSCpE, etc.)

---

## 💡 Recommended Solutions

### **Option 1: Multiple Templates (Program-Specific)** ⭐ RECOMMENDED

**Pros:**
- Each program has its own branded template
- Professional and program-specific
- Easy to maintain per program

**Cons:**
- Need 12 PDF templates (one per program)
- More files to manage

**Implementation:**
```
resources/templates/
├── BSIT-acceptance-letter.pdf
├── BSCS-acceptance-letter.pdf
├── BSIS-acceptance-letter.pdf
├── BSCpE-acceptance-letter.pdf
├── BSEE-acceptance-letter.pdf
├── BSECE-acceptance-letter.pdf
├── BSME-acceptance-letter.pdf
├── BSCE-acceptance-letter.pdf
├── BSIE-acceptance-letter.pdf
├── BSArch-acceptance-letter.pdf
├── BSABE-acceptance-letter.pdf
└── BSGE-acceptance-letter.pdf
```

**Code:**
```php
// Get student's program code
$programCode = $this->getProgramCode($studentProfile->course);

// Build template path
$templatePath = resource_path("templates/{$programCode}-acceptance-letter.pdf");

// Fallback to generic if program-specific doesn't exist
if (!file_exists($templatePath)) {
    $templatePath = resource_path('templates/generic-acceptance-letter.pdf');
}
```

---

### **Option 2: Single Generic Template (Dynamic Text)**

**Pros:**
- Only ONE template to maintain
- Automatically works for all programs
- Easy to update

**Cons:**
- Less program-specific branding
- All programs look the same

**Implementation:**
```
resources/templates/
└── generic-acceptance-letter.pdf  (no program name hardcoded)
```

**Code:**
```php
$templatePath = resource_path('templates/generic-acceptance-letter.pdf');

// Write program name dynamically
$writeField(50, 30, $studentProfile->course); // e.g., "BSIT", "BSCS", etc.
```

---

### **Option 3: Hybrid Approach** ⭐⭐ BEST FOR SCALABILITY

**Pros:**
- Program-specific templates for major programs
- Generic fallback for others
- Flexible and scalable

**Cons:**
- Slightly more complex logic

**Implementation:**
```
resources/templates/
├── BSIT-acceptance-letter.pdf      (custom)
├── BSCS-acceptance-letter.pdf      (custom)
├── BSIS-acceptance-letter.pdf      (custom)
├── BSCpE-acceptance-letter.pdf     (custom)
└── generic-acceptance-letter.pdf   (fallback for others)
```

**Code:**
```php
public function getTemplatePath($course)
{
    $programCode = $this->getProgramCode($course);
    
    // Try program-specific template first
    $specificPath = resource_path("templates/{$programCode}-acceptance-letter.pdf");
    
    if (file_exists($specificPath)) {
        return $specificPath;
    }
    
    // Fallback to generic
    $genericPath = resource_path('templates/generic-acceptance-letter.pdf');
    
    if (file_exists($genericPath)) {
        return $genericPath;
    }
    
    // Last resort: use BSIT template
    return resource_path('templates/BSITacceptancelettertemplate.pdf');
}

private function getProgramCode($course)
{
    // Extract program code from course name
    // e.g., "Bachelor of Science in Information Technology" → "BSIT"
    
    $mapping = [
        'information technology' => 'BSIT',
        'computer science' => 'BSCS',
        'information systems' => 'BSIS',
        'computer engineering' => 'BSCpE',
        'electrical engineering' => 'BSEE',
        'electronics engineering' => 'BSECE',
        'mechanical engineering' => 'BSME',
        'civil engineering' => 'BSCE',
        'industrial engineering' => 'BSIE',
        'architecture' => 'BSArch',
        'agricultural engineering' => 'BSABE',
        'geodetic engineering' => 'BSGE',
    ];
    
    $courseLower = strtolower($course);
    
    foreach ($mapping as $keyword => $code) {
        if (str_contains($courseLower, $keyword)) {
            return $code;
        }
    }
    
    // Default fallback
    return 'GENERIC';
}
```

---

## 🎨 Template Design Considerations

### What Should Be Program-Specific?
1. **Program Logo/Seal** (if different per program)
2. **Program Name** (hardcoded in template)
3. **Department Name** (hardcoded in template)
4. **Program-specific text** (e.g., "College of Engineering" vs "College of Computer Studies")

### What Should Be Dynamic (Filled by Code)?
1. Student name
2. Company name and address
3. Supervisor name and position
4. Work schedule
5. Start/end dates
6. Total hours
7. Job title/department at company

---

## 📝 Implementation Steps

### Step 1: Decide on Approach
**My Recommendation:** Option 3 (Hybrid)
- Create templates for 4-5 major programs (BSIT, BSCS, BSIS, BSCpE, BSEE)
- Use generic template for others
- Easy to add more program-specific templates later

### Step 2: Create Template Files
1. Get PDF templates from your school/department
2. Rename them following naming convention:
   - `BSIT-acceptance-letter.pdf`
   - `BSCS-acceptance-letter.pdf`
   - etc.
3. Create one `generic-acceptance-letter.pdf` (no program name hardcoded)
4. Place all in `resources/templates/`

### Step 3: Update Code

**Add helper methods to SupervisorAcceptanceController:**
```php
private function getTemplatePath($course)
{
    $programCode = $this->getProgramCode($course);
    
    // Try program-specific template
    $specificPath = resource_path("templates/{$programCode}-acceptance-letter.pdf");
    if (file_exists($specificPath)) {
        \Log::info("Using program-specific template: {$programCode}");
        return $specificPath;
    }
    
    // Fallback to generic
    $genericPath = resource_path('templates/generic-acceptance-letter.pdf');
    if (file_exists($genericPath)) {
        \Log::info("Using generic template for: {$course}");
        return $genericPath;
    }
    
    // Last resort
    \Log::warning("No template found, using BSIT template for: {$course}");
    return resource_path('templates/BSITacceptancelettertemplate.pdf');
}

private function getProgramCode($course)
{
    if (empty($course)) {
        return 'GENERIC';
    }
    
    $mapping = [
        'information technology' => 'BSIT',
        'computer science' => 'BSCS',
        'information systems' => 'BSIS',
        'computer engineering' => 'BSCpE',
        'electrical engineering' => 'BSEE',
        'electronics engineering' => 'BSECE',
        'mechanical engineering' => 'BSME',
        'civil engineering' => 'BSCE',
        'industrial engineering' => 'BSIE',
        'architecture' => 'BSArch',
        'agricultural engineering' => 'BSABE',
        'geodetic engineering' => 'BSGE',
    ];
    
    $courseLower = strtolower($course);
    
    foreach ($mapping as $keyword => $code) {
        if (str_contains($courseLower, $keyword)) {
            return $code;
        }
    }
    
    return 'GENERIC';
}
```

**Update generateAcceptanceLetter() method:**
```php
public function generateAcceptanceLetter(Request $request, User $student)
{
    // ... existing validation ...
    
    $supervisor = Auth::user();
    $company = $supervisor->supervisorProfile->company;
    $studentProfile = $student->studentProfile;
    
    // Get appropriate template based on student's program
    $templatePath = $this->getTemplatePath($studentProfile->course);
    
    if (!file_exists($templatePath)) {
        return back()->with('error', 'Acceptance letter template not found. Please contact administrator.');
    }
    
    // Create PDF using FPDI
    $pdf = new \setasign\Fpdi\Fpdi();
    $pdf->setSourceFile($templatePath);
    
    // ... rest of existing code ...
}
```

### Step 4: Test
1. Test with BSIT student (should use BSIT template)
2. Test with BSCS student (should use BSCS template if exists, else generic)
3. Test with unknown program (should use generic)
4. Check logs to verify correct template is being used

---

## 🎓 Defense Points

### Q: Why not just one template for all programs?
**A:** Different programs may have different branding, logos, or department-specific requirements. Program-specific templates maintain professional standards while the generic fallback ensures the system works for all programs.

### Q: What if a new program is added?
**A:** The system automatically uses the generic template. If the program needs a specific template later, just add the PDF file - no code changes needed.

### Q: How do you ensure the right template is used?
**A:** The system extracts the program code from the student's course name using keyword matching, then looks for a matching template file. Logs track which template is used for auditing.

---

## 📋 Template Checklist

### For Each Program Template:
- [ ] Get official PDF template from department
- [ ] Verify program name is correct
- [ ] Verify department name is correct
- [ ] Check logo/seal placement
- [ ] Ensure fillable areas match code coordinates
- [ ] Test PDF generation
- [ ] Verify all dynamic fields appear correctly

### Generic Template:
- [ ] Remove any program-specific text
- [ ] Use neutral branding
- [ ] Add placeholder for program name (to be filled dynamically)
- [ ] Test with multiple programs

---

## 🚀 Quick Start (Minimal Implementation)

**If you want to implement quickly:**

1. **Keep existing BSIT template**
2. **Create ONE generic template** (copy BSIT, remove "BSIT" text)
3. **Update code:**

```php
// In generateAcceptanceLetter()
$programCode = $this->getProgramCode($studentProfile->course);
$templatePath = resource_path("templates/{$programCode}-acceptance-letter.pdf");

// Fallback to generic
if (!file_exists($templatePath)) {
    $templatePath = resource_path('templates/generic-acceptance-letter.pdf');
}

// Last resort: BSIT
if (!file_exists($templatePath)) {
    $templatePath = resource_path('templates/BSITacceptancelettertemplate.pdf');
}
```

4. **Done!** System now works for all programs.

---

## 💾 File Organization

```
resources/
└── templates/
    ├── acceptance-letters/
    │   ├── BSIT-acceptance-letter.pdf
    │   ├── BSCS-acceptance-letter.pdf
    │   ├── BSIS-acceptance-letter.pdf
    │   ├── BSCpE-acceptance-letter.pdf
    │   └── generic-acceptance-letter.pdf
    └── README.md (explains template naming convention)
```

**Alternative (simpler):**
```
resources/
└── templates/
    ├── BSIT-acceptance-letter.pdf
    ├── BSCS-acceptance-letter.pdf
    ├── generic-acceptance-letter.pdf
    └── BSITacceptancelettertemplate.pdf (legacy, keep for now)
```
