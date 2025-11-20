# Acceptance Letter Templates

## 📁 Template Files

This folder contains program-specific acceptance letter templates. The system automatically selects the appropriate template based on the student's course/program.

### Required Templates (13 total):

1. **BSIT-acceptance-letter.pdf** - Bachelor of Science in Information Technology
2. **BEED-acceptance-letter.pdf** - Bachelor of Elementary Education
3. **BIT-CA-acceptance-letter.pdf** - Bachelor of Industrial Technology major in Culinary Arts
4. **BIT-ET-acceptance-letter.pdf** - Bachelor of Industrial Technology major in Electronics
5. **BPEd-acceptance-letter.pdf** - Bachelor of Physical Education
6. **BSCE-acceptance-letter.pdf** - Bachelor of Science in Civil Engineering
7. **BSEE-acceptance-letter.pdf** - Bachelor of Science in Electrical Engineering
8. **BSHM-acceptance-letter.pdf** - Bachelor of Science in Hospitality Management
9. **BSME-acceptance-letter.pdf** - Bachelor of Science in Mechanical Engineering
10. **BSEd-Math-acceptance-letter.pdf** - Bachelor of Secondary Education major in Mathematics
11. **BSEd-Science-acceptance-letter.pdf** - Bachelor of Secondary Education major in Science
12. **BTVTEd-acceptance-letter.pdf** - Bachelor of Technical-Vocational Teacher Education
13. **DTS-acceptance-letter.pdf** - Diploma in Teaching Secondary

## 🔍 How It Works

The system matches student's course name to the appropriate template:

```
Student Course: "Bachelor of Science in Information Technology"
→ Detects: "information technology"
→ Uses: BSIT-acceptance-letter.pdf

Student Course: "Bachelor of Industrial Technology major in Culinary Arts"
→ Detects: "culinary arts"
→ Uses: BIT-CA-acceptance-letter.pdf

Student Course: "Bachelor of Secondary Education major in Mathematics"
→ Detects: "mathematics"
→ Uses: BSEd-Math-acceptance-letter.pdf
```

## 📝 Naming Convention

**Format:** `{PROGRAM_CODE}-acceptance-letter.pdf`

- Use **hyphens** (-) not underscores (_)
- Use **consistent capitalization** (e.g., BSEd not BSED or bsed)
- Always end with **-acceptance-letter.pdf**

## ⚠️ Important Notes

### Template Design Requirements:
- All templates must have the **same page size** (Letter or A4)
- All templates must have **fillable areas at the same coordinates**
- The code fills in dynamic data (student name, company, dates, etc.) at fixed positions
- **DO NOT change coordinate positions** in the code without updating ALL templates

### Fallback Behavior:
If a student's program doesn't match any template:
1. System tries to use **BSIT-acceptance-letter.pdf** as fallback
2. If that doesn't exist, uses **BSITacceptancelettertemplate.pdf** (legacy)
3. Logs a warning for administrator review

## 🆕 Adding New Programs

To add a new program template:

1. **Create the PDF template** following the same design as existing templates
2. **Name it correctly:** `{PROGRAM_CODE}-acceptance-letter.pdf`
3. **Place it in this folder**
4. **Update the code** in `SupervisorAcceptanceController.php`:
   - Add to `getProgramCode()` method
   - Add keyword matching for the new program

Example:
```php
// In getProgramCode() method, add:
'new program name' => 'NEWCODE',
```

## 🧪 Testing

After adding templates, test by:

1. Generate acceptance letter for a student in that program
2. Check `storage/logs/laravel.log` for:
   ```
   Using template for {PROGRAM_CODE}: ...
   ```
3. Verify the PDF generated correctly with program-specific branding

## 📊 Current Status

- [x] BSIT template (existing)
- [ ] BEED template (add PDF file)
- [ ] BIT-CA template (add PDF file)
- [ ] BIT-ET template (add PDF file)
- [ ] BPEd template (add PDF file)
- [ ] BSCE template (add PDF file)
- [ ] BSEE template (add PDF file)
- [ ] BSHM template (add PDF file)
- [ ] BSME template (add PDF file)
- [ ] BSEd-Math template (add PDF file)
- [ ] BSEd-Science template (add PDF file)
- [ ] BTVTEd template (add PDF file)
- [ ] DTS template (add PDF file)

## 🔧 Maintenance

### To Update a Template:
1. Replace the PDF file with the new version
2. Keep the same filename
3. Ensure coordinates match existing templates
4. Test generation

### To Rename a Template:
1. Rename the PDF file
2. Update `getProgramCode()` method in controller
3. Test all affected programs

## 📞 Support

If you encounter issues:
- Check `storage/logs/laravel.log` for template selection logs
- Verify file names match exactly (case-sensitive)
- Ensure PDF files are not corrupted
- Test with a known working template first
