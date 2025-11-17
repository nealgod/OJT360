# Profile Fields & Placeholders Reference

Complete reference for all user profile fields, placeholders, and validation rules.

---

## 👨‍🎓 STUDENT PROFILE

### Database Table: `student_profiles`

### Fields:

| Field | Type | Required | Editable | Placeholder/Example |
|-------|------|----------|----------|---------------------|
| **student_id** | string | ✅ Yes | ❌ No (Read-only) | `2021-00123` |
| **course** | string | ✅ Yes | ❌ No (Read-only) | `Bachelor of Science in Information Technology` |
| **department** | string | ✅ Yes | ❌ No (Read-only) | `Computer Studies Department` |
| **phone** | string | ❌ No | ✅ Yes | `+63 912 345 6789` |
| **address** | string | ❌ No | ✅ Yes | `Street, City, Postal Code, Country` |
| **profile_image** | file | ❌ No | ✅ Yes | `PNG, JPG, GIF up to 2MB` |
| **ojt_status** | enum | ✅ Yes | ❌ No (System) | `pending`, `active`, `completed` |
| **required_hours** | integer | ❌ No | ❌ No (Set by coordinator) | `486` |
| **completed_hours** | integer | ❌ No | ❌ No (System) | `0` |
| **supervisor_id** | foreign | ❌ No | ❌ No (System) | Links to supervisor |
| **company_id** | foreign | ❌ No | ❌ No (System) | Links to company |

### Registration Fields:
- Email (verified via student_verifications table)
- Password
- Name (from users table)

### Notes:
- Student ID, Course, and Department are set during registration and cannot be changed
- Phone and Address are optional and can be updated anytime
- OJT status is managed by the system
- Required hours are set by the coordinator for the program
- Supervisor and company are assigned when acceptance letter is generated

---

## 👨‍💼 SUPERVISOR PROFILE

### Database Table: `supervisor_profiles`

### Fields:

| Field | Type | Required | Editable | Placeholder/Example |
|-------|------|----------|----------|---------------------|
| **name** | string | ✅ Yes | ✅ Yes | `Lastname, Firstname Middlename` |
| **email** | string | ✅ Yes | ✅ Yes | `supervisor@company.com` |
| **phone** | string | ❌ No | ✅ Yes | `+63 912 345 6789` |
| **position** | string | ✅ Yes | ❌ No (Read-only) | `HR Manager`, `IT Supervisor` |
| **company_id** | foreign | ✅ Yes | ❌ No (Read-only) | Links to company |
| **profile_image** | file | ❌ No | ✅ Yes | `PNG, JPG, GIF up to 2MB` |
| **employee_id** | string | ❌ No | ❌ No | Internal use |
| **status** | enum | ✅ Yes | ❌ No (System) | `active`, `inactive` |

### Company Fields (Related):

| Field | Type | Required | Editable | Placeholder/Example |
|-------|------|----------|----------|---------------------|
| **company_name** | string | ✅ Yes | ❌ No (Read-only) | `ABC Corporation` |
| **company_address** | string | ✅ Yes | ❌ No (Read-only) | `Street, Barangay, City, Province` |

### Registration Fields:
1. **Email** - Verified via supervisor_registrations table
2. **Name** - `Lastname, Firstname Middlename`
3. **Phone** - `+63 912 345 6789`
4. **Company Name** - Full company name
5. **Company Address** - `Street, Barangay, City, Province`
6. **Position** - `e.g., HR Manager, IT Supervisor`
7. **Password** - With show/hide toggle
8. **Confirm Password** - Must match

### Notes:
- Email verification required (24-hour expiration)
- Company information is set during registration and cannot be changed
- Position is set during registration and cannot be changed
- Phone number can be updated anytime
- Profile picture is optional

---

## 👨‍🏫 COORDINATOR PROFILE

### Database Table: `coordinator_profiles`

### Fields:

| Field | Type | Required | Editable | Placeholder/Example |
|-------|------|----------|----------|---------------------|
| **employee_id** | string | ✅ Yes | ✅ Yes | `EMP-2024-001` |
| **department** | string | ❌ No | ❌ No (Read-only) | `Computer Studies Department` |
| **department_id** | foreign | ❌ No | ❌ No (System) | Links to department |
| **program_id** | foreign | ❌ No | ❌ No (System) | Links to program |
| **phone** | string | ❌ No | ✅ Yes | `+63 912 345 6789` |
| **profile_image** | file | ❌ No | ✅ Yes | `PNG, JPG, GIF up to 2MB` |
| **managed_departments** | text | ❌ No | ❌ No (System) | JSON array |
| **status** | enum | ✅ Yes | ❌ No (System) | `active`, `inactive` |

### Registration Fields:
- Invited by admin
- Email verification
- Complete profile with employee ID
- Department and program assigned by admin

### Notes:
- Coordinators are invited by admins
- Department and program are assigned during invitation
- Employee ID can be updated
- Phone number is optional and editable
- Manages students in their department/program

---

## 📋 COMMON FIELDS (All Roles)

### From `users` table:

| Field | Type | Required | Editable | Placeholder/Example |
|-------|------|----------|----------|---------------------|
| **name** | string | ✅ Yes | ✅ Yes | `Juan Dela Cruz` |
| **email** | string | ✅ Yes | ✅ Yes | `user@example.com` |
| **password** | string | ✅ Yes | ✅ Yes | Minimum 8 characters |
| **role** | enum | ✅ Yes | ❌ No | `intern`, `supervisor`, `coordinator`, `admin` |

### Profile Picture:
- **Accepted formats:** PNG, JPG, GIF
- **Max size:** 2MB
- **Display:** Circular avatar
- **Fallback:** First letter of name in colored circle

---

## 🔐 VALIDATION RULES

### Phone Numbers:
- Format: `+63 912 345 6789`
- Optional for all roles
- Can include spaces and dashes

### Email:
- Must be valid email format
- Must be unique in system
- Verification required for students and supervisors

### Passwords:
- Minimum 8 characters
- Must be confirmed during registration
- Show/hide toggle available

### Profile Images:
- Max 2MB file size
- Accepted: PNG, JPG, GIF
- Automatically resized/optimized
- Optional for all roles

### Names:
- Supervisor format: `Lastname, Firstname Middlename`
- Student/Coordinator: Any format
- Required for all roles

### Addresses:
- Student: `Street, City, Postal Code, Country`
- Supervisor (Company): `Street, Barangay, City, Province`
- Free text format

---

## 📝 FIELD STATES

### Read-Only Fields:
**Student:**
- Student ID
- Course
- Department
- OJT Status
- Required Hours
- Completed Hours

**Supervisor:**
- Position
- Company Name
- Company Address

**Coordinator:**
- Department
- Program

### Editable Fields:
**All Roles:**
- Name
- Email
- Phone
- Profile Picture

**Student:**
- Address

**Coordinator:**
- Employee ID

### System-Managed:
- User ID
- Role
- Status
- Timestamps
- Foreign keys (supervisor_id, company_id, etc.)

---

## 🎨 UI PLACEHOLDERS

### Text Inputs:
```
Name: "Lastname, Firstname Middlename"
Email: "user@example.com"
Phone: "+63 912 345 6789"
Address: "Street, Barangay, City, Province"
Position: "e.g., HR Manager, IT Supervisor"
Student ID: "2021-00123"
Employee ID: "EMP-2024-001"
```

### File Inputs:
```
Profile Picture: "PNG, JPG, GIF up to 2MB"
```

### Disabled Fields:
```
Background: bg-gray-50
Cursor: cursor-not-allowed
Border: border-gray-200
Text: text-gray-700
```

---

## 📊 SUMMARY TABLE

| Role | Total Fields | Required | Editable | Read-Only | System |
|------|--------------|----------|----------|-----------|--------|
| **Student** | 12 | 3 | 3 | 4 | 5 |
| **Supervisor** | 10 | 6 | 3 | 3 | 2 |
| **Coordinator** | 9 | 2 | 3 | 2 | 4 |

---

## 🔗 RELATIONSHIPS

### Student:
- `supervisor_id` → Supervisor (nullable)
- `company_id` → Company (nullable)
- `user_id` → User

### Supervisor:
- `company_id` → Company
- `user_id` → User

### Coordinator:
- `department_id` → Department (nullable)
- `program_id` → Program (nullable)
- `user_id` → User

---

This reference document provides complete information about all profile fields, their validation rules, placeholders, and editability status for each role in the OJT360 system.
