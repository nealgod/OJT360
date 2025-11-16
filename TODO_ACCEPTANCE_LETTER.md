# TODO: Acceptance Letter System Improvements

## 📋 Pending Tasks

### 1. Fine-tune PDF Field Positions
- [ ] Adjust Date field position (currently close but needs minor tweaking)
- [ ] Adjust Name field position
- [ ] Adjust Program field position
- [ ] Adjust Company field position
- [ ] Adjust Location field position
- [ ] Adjust Job Title (table) position
- [ ] Adjust Branch/Department (table) position
- [ ] Adjust Working hours (table) position
- [ ] Adjust Total hours (table) position
- [ ] Adjust Effective Date (table) position
- [ ] Adjust Company Representative Name position
- [ ] Adjust Position field position
- [ ] Adjust Department field position
- [ ] Adjust Contact field position

**Current Status:** Coordinates are calculated with correct margins (Left: 0.43", Top: 0.34"). Text is appearing but needs fine-tuning by ±2-3mm per field.

---

### 2. Make Template Dynamic for All Programs
- [ ] Remove hardcoded "BSIT" from template
- [ ] Update template to have blank space for program name
- [ ] Test with all 13 programs:
  - [ ] BSIT (Bachelor of Science in Information Technology)
  - [ ] BSCS (Bachelor of Science in Computer Science)
  - [ ] BSCpE (Bachelor of Science in Computer Engineering)
  - [ ] (Add other 10 programs here)
- [ ] Verify program name pulls from `student_profile.course` field
- [ ] Verify required hours pull from `student_profile.required_hours` field
- [ ] Verify department pulls from `student_profile.department` field

**Current Status:** System is ready to be dynamic - just need to update the PDF template file.

---

### 3. Testing & Validation
- [ ] Test complete flow with BSIT student
- [ ] Test complete flow with non-BSIT student
- [ ] Verify email notifications are sent
- [ ] Verify in-app notifications appear
- [ ] Verify PDF downloads correctly
- [ ] Verify letter is added to student documents
- [ ] Verify supervisor is linked to student
- [ ] Test expired request handling
- [ ] Test resend link functionality

---

### 4. Optional Enhancements
- [ ] Add Calibri font support (currently using Arial)
- [ ] Add signature image upload for supervisors
- [ ] Add preview before generating letter
- [ ] Add ability to regenerate/edit letter
- [ ] Add letter versioning

---

## 📝 Notes

### Current Margins (LibreOffice):
- Left: 0.43"
- Right: 0.39"
- Top: 0.34"
- Bottom: 0.39"

### Current Font:
- Font: Arial (Calibri not available in FPDF)
- Size: 13pt
- Color: Black (0, 0, 0)

### Formula for Coordinates:
```
Actual X (mm) = (LibreOffice X + 0.43) × 25.4
Actual Y (mm) = (LibreOffice Y + 0.34) × 25.4
```

---

## ✅ Completed Today (Nov 16, 2025)

1. ✅ Fixed supervisor registration flow (redirects to dashboard)
2. ✅ Added notifications for supervisors after registration
3. ✅ Fixed pending requests display on acceptance letters page
4. ✅ Removed expiration for logged-in supervisors
5. ✅ Fixed notification database conflicts (email-only for Laravel notifications)
6. ✅ Added "View" button in notifications for acceptance letter requests
7. ✅ Updated PDF generation with correct margins and coordinates
8. ✅ Changed font to Arial 13pt black text
9. ✅ Added all required fields to PDF (date, name, program, company, location, table fields, bottom fields)
10. ✅ Fixed notification badge colors for acceptance letter type

---

## 🔧 Files Modified Today

- `app/Http/Controllers/SupervisorAcceptanceController.php`
- `app/Http/Controllers/AcceptanceRequestController.php`
- `app/Notifications/AcceptanceLetterGenerated.php`
- `resources/views/notifications/index.blade.php`
- `resources/views/supervisor/acceptance/index.blade.php`
- `resources/views/supervisor/acceptance/resent.blade.php`
- `resources/views/dashboard.blade.php`

---

**Last Updated:** November 16, 2025
