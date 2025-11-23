# 📄 Monthly Progress Evaluation Form — Enhanced Integration Plan

This document provides a **fully enhanced, clearer, and developer-friendly technical plan** for integrating the **Monthly Progress Evaluation Form** into your system using the PDF template:

**`monthlyprogressevaulationformtemplate(final)`**

The structure and wording are optimized so that **AI coding assistants (Cursor, Kiro, Claude, ChatGPT)** can read, parse, and execute the instructions **without confusion**.

---

# 1. 🔎 Form Overview
This form requires precise placement of text, ratings, comments, and signatures using **absolute X/Y coordinates (in inches)**.

Your system must:
- Auto-fill date, month, and year
- Insert student and company info
- Choose *one* rating per row (5–1)
- Fill multi-line comments
- Place two signatures
- Apply required font sizes per field

All coordinates below are exact and must not be changed.

---

# 2. 🔤 Font Size Rules
| Field Type | Expected Font Size |
|------------|---------------------|
| **Date**, **Month & Year** | **10 pt** |
| **Personal Info Fields** (Student Name, HTE, Address, Assignment, Schedule, Manager Name) | **9 pt** |
| **Rating Table Check Marks** | **8 pt** |
| **Comments & Recommendations** | **8 pt** |
| **Signatures** | **9 pt** |

---

# 3. 🧍 Auto-Filled Personal Information Fields
Below are all fields that your system must populate programmatically.

### **Date**
- **X:** 6.15″
- **Y:** 2.34″
- **Font:** 10 pt

### **Month & Year** (Auto-generated)
- **X:** 2.38″
- **Y:** 2.69″
- **Font:** 10 pt
- Automatically generate: **MONTH (uppercase)** + **YEAR**

### **Name of Student/Trainee**
- **X:** 2.66″
- **Y:** 3.01″
- **Font:** 9 pt

### **Host Training Establishment (HTE)**
- **X:** 3.25″
- **Y:** 3.18″
- **Font:** 9 pt

### **Address of HTE**
- **X:** 2.17″
- **Y:** 3.36″
- **Font:** 9 pt

### **Work Assignment(s)**
- **X:** 2.33″
- **Y:** 3.53″
- **Font:** 9 pt

### **Work Schedule**
- **X:** 2.11″
- **Y:** 3.67″
- **Font:** 9 pt

### **Name of Manager/Supervisor**
- **X:** 2.88″
- **Y:** 3.83″
- **Font:** 9 pt

---

# 4. 🟩 Rating Table (20 Rows × 5 Columns)
Each row represents one evaluation attribute.  
**Only one (1) rating is checked per row.**

## 4.1 Column X Coordinates
| Rating | Meaning | X Position |
|--------|----------|-------------|
| **5** | Excellent | **4.45″** |
| **4** | Very Satisfactory | **5.11″** |
| **3** | Satisfactory | **5.86″** |
| **2** | Fair | **6.50″** |
| **1** | Needs Improvement | **7.12″** |

## 4.2 Row Y Coordinates
| Row | Y Position |
|-----|-------------|
| 1 | 4.90″ |
| 2 | 5.05″ |
| 3 | 5.20″ |
| 4 | 5.35″ |
| 5 | 5.50″ |
| 6 | 5.80″ |
| 7 | 5.95″ |
| 8 | 6.10″ |
| 9 | 6.25″ |
| 10 | 6.40″ |
| 11 | 6.70″ |
| 12 | 6.85″ |
| 13 | 7.00″ |
| 14 | 7.15″ |
| 15 | 7.30″ |
| 16 | 7.60″ |
| 17 | 7.75″ |
| 18 | 7.95″ |
| 19 | 8.10″ |
| 20 | 8.25″ |

## 4.3 Checkmark Placement Logic
Your system must:
1. Receive a numeric rating (1–5) per row.
2. Pick the correct **X position** based on rating.
3. Pick the correct **Y position** based on row number.
4. Place exactly **one check mark**.

---

# 5. 💬 Comments & Recommendations (Multi-Line)
**Starting position:**  
- **X:** 1.04″  
- **Y:** 8.70″  
- **Font:** 8 pt

If comments exceed one line, follow this line sequence:

| Line # | X | Y |
|--------|-------|-------|
| 1 | 1.04″ | 8.70″ |
| 2 | 1.04″ | 8.82″ |
| 3 | 1.04″ | 8.94″ |
| 4 | 1.04″ | 9.06″ |

Maximum: **4 lines**.

---

# 6. ✒️ Signature Areas
### **Signature of Manager/Supervisor**
- **X:** 1.04″
- **Y:** 9.66″
- **Font:** 9 pt

### **Signature of Student/Trainee**
- **X:** 4.99″
- **Y:** 9.66″
- **Font:** 9 pt

---

# 7. 🧠 System Logic Summary
### Auto-Detect Time
- Convert current system date to **MONTH YEAR** (uppercase)

### Rating Data Structure
Example JSON expected by PDF filler:
```json
{
  "row1": 5,
  "row2": 4,
  "row3": 3,
  "row4": 2,
  "row5": 1,
  "row6": 5,
  "row7": 4,
  "row8": 3,
  "row9": 2,
  "row10": 1,
  "row11": 5,
  "row12": 4,
  "row13": 3,
  "row14": 2,
  "row15": 1,
  "row16": 5,
  "row17": 4,
  "row18": 3,
  "row19": 2,
  "row20": 1
}
```

### Text Auto-Fill Data
Your back-end should provide:
```json
{
  "studentName": "",
  "hte": "",
  "address": "",
  "assignment": "",
  "schedule": "",
  "managerName": "",
  "comments": ""
}
```

### Comment Wrapping
- Split by max line width
- Place using the Y sequence
- Stop after 4 lines

---

# 8. 🛠️ Implementation Strategy (PDF Generation)
Recommended workflow:
1. Load **monthlyprogressevaulationformtemplate(final)** as background PDF.
2. Convert inches → PDF points (× 72).
3. Draw all text fields using defined coordinates + font sizes.
4. Draw checkmark using `X`, `•`, or `✔` with **8pt font**.
5. Add multi-line comments.
6. Insert signature text fields.
7. Export final filled PDF.

This structure ensures Cursor/Kiro can convert instructions directly into working code.

---

# ✅ Document Enhanced and Ready
This version is fully cleaned, readable, and optimized for both humans and AI development tools.
