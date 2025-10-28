# OJT360 - Process Flow Documentation

## 🎯 Project Overview
**OJT360** is an end-to-end web-based internship monitoring and management system built with Laravel 9, designed to streamline the entire OJT process from student registration to completion.

---

## 📋 High-Level Process Flow

### 1. **System Initialization**
```
Admin Setup → Department Configuration → Program Setup → Document Requirements
```

### 2. **Student Onboarding Flow**
```
Student Registration → Email Verification → Profile Completion → Placement Request → Coordinator Review
```

### 3. **Placement Management Flow**
```
Placement Request → Coordinator Review → Approval/Rejection → Supervisor Assignment → OJT Activation
```

### 4. **OJT Execution Flow**
```
Daily Attendance → Daily Reports → Document Submissions → Weekly Reports → Progress Tracking
```

### 5. **Completion Flow**
```
Hours Completion → Final Document Review → Certificate Generation → Program Completion
```

---

## 🔄 Detailed Process Flows

### **A. Student Registration & Onboarding**

```mermaid
graph TD
    A[Student Visits Site] --> B[Register Account]
    B --> C[Email Verification]
    C --> D[Complete Profile]
    D --> E[Submit Placement Request]
    E --> F{Coordinator Reviews}
    F -->|Approved| G[OJT Activated]
    F -->|Rejected| H[Student Revises Request]
    H --> E
    G --> I[Begin OJT Activities]
```

**Key Steps:**
1. **Registration**: Student creates account with email
2. **Verification**: Email verification required
3. **Profile**: Complete student profile (course, department, etc.)
4. **Placement**: Submit placement request with company choice
5. **Review**: Coordinator reviews and approves/rejects
6. **Activation**: Upon approval, OJT status becomes 'active'

### **B. Placement Request Management**

```mermaid
graph TD
    A[Student Submits Request] --> B[Choose Company Type]
    B --> C{Company Type?}
    C -->|Listed| D[Select from Database]
    C -->|External| E[Provide Company Details]
    D --> F[Submit Request]
    E --> F
    F --> G[Coordinator Notification]
    G --> H[Coordinator Reviews]
    H --> I{Decision}
    I -->|Approve| J[Auto-create Supervisor Account]
    I -->|Reject| K[Notify Student]
    J --> L[Auto-approve Letter of Acceptance]
    L --> M[OJT Status: Active]
    K --> N[Student Can Revise]
```

**Key Features:**
- **Company Types**: Listed companies vs External companies
- **Auto-creation**: Supervisor accounts created automatically
- **Auto-approval**: Letter of Acceptance approved with placement
- **Notifications**: Real-time notifications to all parties

### **C. Daily OJT Activities**

```mermaid
graph TD
    A[Student Login] --> B[Time In with Photo]
    B --> C[Work Activities]
    C --> D[Time Out with Photo]
    D --> E[Submit Daily Report]
    E --> F[Coordinator Review]
    F --> G{Report Status}
    G -->|Approved| H[Add to Progress]
    G -->|Returned| I[Student Revises]
    I --> E
    H --> J[Update Hours Worked]
```

**Daily Activities:**
1. **Time In**: Photo capture with geolocation
2. **Work**: Student performs OJT activities
3. **Time Out**: Photo capture with geolocation
4. **Report**: Submit daily report with activities
5. **Review**: Coordinator reviews and provides feedback

### **D. Document Management Flow**

```mermaid
graph TD
    A[Document Requirements] --> B[Student Views Requirements]
    B --> C[Upload Documents]
    C --> D[Submit for Review]
    D --> E[Coordinator Reviews]
    E --> F{Document Status}
    F -->|Approved| G[Mark as Complete]
    F -->|Rejected| H[Provide Feedback]
    F -->|Under Review| I[Continue Review]
    H --> J[Student Revises]
    J --> C
    G --> K[Track Completion]
```

**Document Types:**
- **Pre-placement**: Letter of Acceptance, Medical Certificate
- **Post-placement**: Weekly Reports, Photo Documentation
- **Ongoing**: Attendance Records, Progress Reports

### **E. Weekly Report Generation**

```mermaid
graph TD
    A[Student Selects Week] --> B[Generate Weekly Report]
    B --> C[PDF Generation]
    C --> D[Preview Report]
    D --> E{Action}
    E -->|Download| F[Save PDF Locally]
    E -->|Submit to Documents| G[Auto-submit to Document System]
    G --> H[Coordinator Notification]
    H --> I[Coordinator Reviews]
    I --> J[Approve/Reject]
```

**Features:**
- **PDF Generation**: Using DomPDF
- **Auto-submission**: Direct integration with document system
- **Preview**: Before final submission
- **Notifications**: Automatic coordinator notification

---

## 🏗️ System Architecture

### **User Roles & Permissions**

```mermaid
graph TD
    A[Admin] --> B[Full System Access]
    C[Coordinator] --> D[Department Management]
    D --> E[Student Oversight]
    D --> F[Document Review]
    D --> G[Placement Approval]
    H[Supervisor] --> I[Intern Management]
    I --> J[Attendance Monitoring]
    I --> K[Report Review]
    L[Student/Intern] --> M[OJT Activities]
    M --> N[Attendance Tracking]
    M --> O[Report Submission]
    M --> P[Document Upload]
```

### **Database Relationships**

```mermaid
erDiagram
    USERS ||--o{ STUDENT_PROFILES : has
    USERS ||--o{ COORDINATOR_PROFILES : has
    USERS ||--o{ SUPERVISOR_PROFILES : has
    USERS ||--o{ PLACEMENT_REQUESTS : submits
    USERS ||--o{ DAILY_REPORTS : creates
    USERS ||--o{ ATTENDANCE_LOGS : records
    USERS ||--o{ STUDENT_DOCUMENT_SUBMISSIONS : submits
    
    COMPANIES ||--o{ SUPERVISOR_PROFILES : employs
    COMPANIES ||--o{ PLACEMENT_REQUESTS : hosts
    
    DOCUMENT_REQUIREMENTS ||--o{ STUDENT_DOCUMENT_SUBMISSIONS : requires
    
    DEPARTMENTS ||--o{ PROGRAMS : contains
    PROGRAMS ||--o{ STUDENT_PROFILES : enrolls
```

---

## 🔧 Technical Process Flows

### **Authentication & Authorization**

```mermaid
graph TD
    A[User Login] --> B{User Role}
    B -->|Admin| C[Full Access]
    B -->|Coordinator| D[Department Access]
    B -->|Supervisor| E[Company Access]
    B -->|Student| F[Personal Access]
    C --> G[Admin Dashboard]
    D --> H[Coordinator Dashboard]
    E --> I[Supervisor Dashboard]
    F --> J[Student Dashboard]
```

### **Middleware Flow**

```mermaid
graph TD
    A[Request] --> B[Auth Middleware]
    B --> C[Verified Middleware]
    C --> D[Profile Complete Middleware]
    D --> E[Placement Started Middleware]
    E --> F[Controller Action]
    F --> G[Response]
```

**Middleware Chain:**
1. **Auth**: User must be logged in
2. **Verified**: Email must be verified
3. **Profile Complete**: Profile must be completed
4. **Placement Started**: OJT must be active (for students)

### **File Upload Process**

```mermaid
graph TD
    A[File Upload Request] --> B[Validation]
    B --> C{File Valid?}
    C -->|Yes| D[Store in Public Disk]
    C -->|No| E[Return Error]
    D --> F[Create Database Record]
    F --> G[Send Notification]
    G --> H[Success Response]
```

---

## 📊 Key Metrics & Tracking

### **Student Progress Tracking**
- **Hours Completed**: vs Required Hours
- **Reports Submitted**: Daily and Weekly
- **Documents Uploaded**: Required vs Submitted
- **Attendance Rate**: Time in/out consistency

### **Coordinator Dashboard Metrics**
- **Pending Placements**: Awaiting approval
- **Pending Reviews**: Documents and reports
- **Student Statistics**: By department and status
- **Completion Rates**: Program-wise statistics

### **System Performance Metrics**
- **Response Times**: API and page load times
- **Storage Usage**: File uploads and documents
- **User Activity**: Login patterns and usage
- **Error Rates**: System errors and exceptions

---

## 🔄 Integration Points

### **Email Notifications**
- **Registration**: Welcome emails
- **Placement**: Approval/rejection notifications
- **Document Review**: Status updates
- **Supervisor Creation**: Login credentials

### **File Management**
- **Storage**: Local public disk
- **Security**: File type validation
- **Backup**: Regular file backups
- **Cleanup**: Orphaned file removal

### **Reporting System**
- **Daily Reports**: Individual submissions
- **Weekly Reports**: Aggregated summaries
- **Progress Reports**: Completion tracking
- **Analytics**: System usage reports

---

## 🎯 Success Criteria

### **Student Success**
- ✅ Complete OJT requirements
- ✅ Submit all required documents
- ✅ Maintain good attendance
- ✅ Generate quality reports

### **Coordinator Success**
- ✅ Efficiently manage students
- ✅ Quick document reviews
- ✅ Effective communication
- ✅ Program oversight

### **System Success**
- ✅ High uptime and reliability
- ✅ Fast response times
- ✅ Secure data handling
- ✅ Scalable architecture

---

## 📈 Future Enhancements

### **Phase 2: Coordinator Module**
- Advanced analytics dashboard
- Bulk operations
- Report generation
- Communication tools

### **Phase 3: Supervisor Module**
- Intern management interface
- Attendance monitoring
- Performance tracking
- Feedback system

### **Phase 4: Admin Module**
- System-wide management
- Advanced reporting
- User management
- System configuration

---

**Last Updated**: Current Development Phase  
**Status**: Student Module Complete, Coordinator Module in Development  
**Next Phase**: Enhanced Coordinator Features
