# OFFICIAL OJT ACCEPTANCE FORM - EVSU Template Structure

## VISUAL LAYOUT (From Image)

### Header Section
```
┌─────────────────────────────────────────────────────────┐
│  [EVSU LOGO]    Republic of the Philippines             │
│                 Eastern Visayas State University         │
│                 Ormoc City Campus                        │
│                 Ormoc City                               │
├─────────────────────────────────────────────────────────┤
│                                                          │
│              OJT ACCEPTANCE FORM                         │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

### Body Section

**DATE:** _______________

**Approval Text:**
"This is to signify the approval of on-the-job request allowing"

**Student Information (Fill-in blanks):**
- _________________________________, a Bachelor of Science in
- Information Technology 4th year student from the Computer Studies Department,
- to render his/her practicum in _________________________________ located at
- _________________________________.

**Assignment Details Table:**
```
┌──────────────────────────────┬──────────────────────────┐
│ Job Title                    │                          │
├──────────────────────────────┼──────────────────────────┤
│ Branch/Department/Section    │                          │
├──────────────────────────────┼──────────────────────────┤
│ Immediate Supervisor         │                          │
├──────────────────────────────┼──────────────────────────┤
│ Working hours and days       │                          │
├──────────────────────────────┼──────────────────────────┤
│ Total hours required         │                          │
├──────────────────────────────┼──────────────────────────┤
│ Effective Date               │                          │
└──────────────────────────────┴──────────────────────────┘
```

### Signature Section

**Left Side - "Noted by:"**
```
_______________________
Company Representative
Signature over Printed Name

_______________________
Position

_______________________
Department

_______________________
Contact no:/Email Address
```

**Right Side - "CONFROME:"**
```
_______________________
Student
Signature over Printed Name
```

---

## FIELDS TO POPULATE DYNAMICALLY

### From Student Profile:
1. **Student Name** - Full name
2. **Course** - "Bachelor of Science in Information Technology" (or other courses)
3. **Year Level** - "4th year" (or current year)
4. **Department** - "Computer Studies Department" (or student's department)

### From Supervisor Form:
5. **Company Name** - Where practicum will be rendered
6. **Company Location** - Full address
7. **Job Title** - Position/role
8. **Branch/Department/Section** - Specific department
9. **Immediate Supervisor** - Supervisor's name
10. **Working Hours and Days** - Schedule (e.g., "Monday-Friday, 8:00 AM - 5:00 PM")
11. **Total Hours Required** - Number of hours (e.g., "486 hours")
12. **Effective Date** - Start and end dates (e.g., "January 15, 2025 - May 15, 2025")

### Company Representative (Supervisor):
13. **Name** - Supervisor's full name
14. **Position** - Job title
15. **Department** - Department name
16. **Contact** - Phone/Email

### Signatures:
17. **Company Representative Signature** - Digital signature
18. **Student Signature** - Digital signature (or signed later)

### System Generated:
19. **DATE** - Current date when form is generated

---

## PDF GENERATION APPROACH

### Option 1: FPDI (Overlay on Template) - RECOMMENDED
```php
use setasign\Fpdi\Fpdi;

$pdf = new Fpdi();
$pdf->AddPage('P', [215.9, 330.2]); // Long bond in mm (8.5" x 13")
$pdf->setSourceFile('storage/app/templates/OJT ACCEPTANCE FORMtemplate.pdf');
$tplId = $pdf->importPage(1);
$pdf->useTemplate($tplId);

// Set font
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(0, 0, 0);

// Fill in fields at specific coordinates
// (Coordinates need to be measured from template)

// DATE
$pdf->SetXY(50, 165); // Adjust based on actual position
$pdf->Write(0, date('F d, Y'));

// Student Name
$pdf->SetXY(50, 235); // Adjust
$pdf->Write(0, $studentName);

// Company Name
$pdf->SetXY(50, 305); // Adjust
$pdf->Write(0, $companyName);

// Company Location
$pdf->SetXY(50, 340); // Adjust
$pdf->Write(0, $companyLocation);

// Table fields (right column)
$pdf->SetXY(320, 440); // Job Title
$pdf->Write(0, $jobTitle);

$pdf->SetXY(320, 467); // Department
$pdf->Write(0, $department);

$pdf->SetXY(320, 494); // Supervisor
$pdf->Write(0, $supervisor);

$pdf->SetXY(320, 521); // Working hours
$pdf->Write(0, $workingHours);

$pdf->SetXY(320, 548); // Total hours
$pdf->Write(0, $totalHours);

$pdf->SetXY(320, 575); // Effective date
$pdf->Write(0, $effectiveDate);

// Company Representative
$pdf->SetXY(50, 705); // Name
$pdf->Write(0, $companyRepName);

$pdf->SetXY(50, 820); // Position
$pdf->Write(0, $companyRepPosition);

$pdf->SetXY(50, 905); // Department
$pdf->Write(0, $companyRepDepartment);

$pdf->SetXY(50, 965); // Contact
$pdf->Write(0, $companyRepContact);

// Signatures (if digital)
if ($companySignature) {
    $pdf->Image($companySignature, 50, 730, 50, 20);
}

if ($studentSignature) {
    $pdf->Image($studentSignature, 400, 730, 50, 20);
}

return $pdf->Output('S');
```

### Option 2: HTML to PDF (Recreate Template)
Create HTML version matching the exact layout, then convert to PDF using DomPDF or Snappy.

---

## COORDINATE MEASUREMENT NEEDED

To accurately fill the template, we need to measure:
1. X,Y position of each blank line
2. X,Y position of each table cell
3. X,Y position of signature areas

**Measurement Units:**
- PDF uses points (1 inch = 72 points)
- Long bond: 612 x 936 points (8.5" x 13")

**Tools to Measure:**
1. Adobe Acrobat (Form Field tool)
2. PDF-XChange Editor
3. Trial and error with test PDFs

---

## IMPLEMENTATION STRATEGY

### Phase 1: Create Test Generator
```php
// Create a test script to generate sample PDF
// Adjust coordinates until text aligns perfectly
```

### Phase 2: Build Form Interface
```php
// Supervisor fills form with all required data
// Preview before generating
```

### Phase 3: Generate Final PDF
```php
// Use measured coordinates
// Overlay text on template
// Add signatures
// Save and submit
```

---

## IMPORTANT NOTES

1. **Course Flexibility:** Template says "Information Technology" but should support all courses
2. **Year Level:** Should be dynamic (1st, 2nd, 3rd, 4th year)
3. **Department:** Should match student's actual department
4. **Signature Placement:** Need exact coordinates for proper alignment
5. **Font Matching:** Use Arial or similar to match template style
6. **Text Size:** Adjust font size to fit within blank lines

---

## NEXT STEPS

1. ✅ Template structure documented
2. ⏳ Move template to storage/app/templates/
3. ⏳ Create coordinate measurement script
4. ⏳ Build supervisor form
5. ⏳ Implement PDF generation
6. ⏳ Test with sample data
7. ⏳ Fine-tune coordinates
8. ⏳ Deploy

Ready to start implementation!
