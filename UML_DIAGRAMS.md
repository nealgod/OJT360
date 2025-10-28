# OJT360 - UML Diagrams

## 🏗️ System Architecture UML

### **Class Diagram - Core Models**

```mermaid
classDiagram
    class User {
        +id: int
        +name: string
        +email: string
        +password: string
        +role: enum
        +email_verified_at: datetime
        +must_change_password: boolean
        +created_at: datetime
        +updated_at: datetime
        +isStudent(): boolean
        +isCoordinator(): boolean
        +isSupervisor(): boolean
        +hasActiveOJT(): boolean
        +hasCompletedProfile(): boolean
        +getRequiredHours(): int
        +getCompletedHours(): int
    }

    class StudentProfile {
        +id: int
        +user_id: int
        +student_id: string
        +course: string
        +department: string
        +ojt_status: enum
        +assigned_company_id: int
        +supervisor_id: int
        +required_hours: int
        +created_at: datetime
        +updated_at: datetime
    }

    class CoordinatorProfile {
        +id: int
        +user_id: int
        +employee_id: string
        +department: string
        +program_id: int
        +created_at: datetime
        +updated_at: datetime
    }

    class SupervisorProfile {
        +id: int
        +user_id: int
        +company_id: int
        +position: string
        +created_at: datetime
        +updated_at: datetime
    }

    class Company {
        +id: int
        +name: string
        +address: string
        +contact_email: string
        +contact_phone: string
        +department: string
        +is_active: boolean
        +created_at: datetime
        +updated_at: datetime
    }

    class PlacementRequest {
        +id: int
        +student_user_id: int
        +company_id: int
        +external_company_name: string
        +external_company_address: string
        +supervisor_name: string
        +supervisor_email: string
        +status: enum
        +proof_path: string
        +decided_by: int
        +decided_at: datetime
        +created_at: datetime
        +updated_at: datetime
    }

    class DailyReport {
        +id: int
        +student_user_id: int
        +work_date: date
        +summary: text
        +attachment_path: string
        +status: enum
        +created_at: datetime
        +updated_at: datetime
    }

    class AttendanceLog {
        +id: int
        +student_user_id: int
        +company_id: int
        +work_date: date
        +time_in: datetime
        +time_out: datetime
        +minutes_worked: int
        +photo_path: string
        +lat_in: decimal
        +lng_in: decimal
        +lat_out: decimal
        +lng_out: decimal
        +created_at: datetime
        +updated_at: datetime
    }

    class DocumentRequirement {
        +id: int
        +name: string
        +description: text
        +type: enum
        +file_types: json
        +max_file_size_mb: int
        +max_files_per_submission: int
        +is_required: boolean
        +is_active: boolean
        +created_at: datetime
        +updated_at: datetime
    }

    class StudentDocumentSubmission {
        +id: int
        +student_user_id: int
        +document_requirement_id: int
        +file_path: string
        +original_filename: string
        +file_size: int
        +mime_type: string
        +status: enum
        +feedback: text
        +reviewed_by: int
        +reviewed_at: datetime
        +created_at: datetime
        +updated_at: datetime
    }

    class Program {
        +id: int
        +name: string
        +department_id: int
        +required_hours: int
        +is_active: boolean
        +created_at: datetime
        +updated_at: datetime
    }

    class Department {
        +id: int
        +name: string
        +description: text
        +is_active: boolean
        +created_at: datetime
        +updated_at: datetime
    }

    class Notification {
        +id: int
        +user_id: int
        +type: string
        +title: string
        +message: text
        +data: json
        +read_at: datetime
        +created_at: datetime
        +updated_at: datetime
    }

    %% Relationships
    User ||--o{ StudentProfile : has
    User ||--o{ CoordinatorProfile : has
    User ||--o{ SupervisorProfile : has
    User ||--o{ PlacementRequest : submits
    User ||--o{ DailyReport : creates
    User ||--o{ AttendanceLog : records
    User ||--o{ StudentDocumentSubmission : submits
    User ||--o{ Notification : receives

    Company ||--o{ SupervisorProfile : employs
    Company ||--o{ PlacementRequest : hosts
    Company ||--o{ AttendanceLog : tracks

    DocumentRequirement ||--o{ StudentDocumentSubmission : requires

    Department ||--o{ Program : contains
    Program ||--o{ StudentProfile : enrolls
    Program ||--o{ CoordinatorProfile : manages
```

### **Use Case Diagram**

```mermaid
graph TD
    subgraph "Student Use Cases"
        A[Student] --> B[Register Account]
        A --> C[Complete Profile]
        A --> D[Submit Placement Request]
        A --> E[Track Attendance]
        A --> F[Submit Daily Reports]
        A --> G[Upload Documents]
        A --> H[Generate Weekly Reports]
        A --> I[View Progress]
    end

    subgraph "Coordinator Use Cases"
        J[Coordinator] --> K[Review Placement Requests]
        J --> L[Approve/Reject Placements]
        J --> M[Assign Supervisors]
        J --> N[Review Documents]
        J --> O[Review Reports]
        J --> P[Manage Students]
        J --> Q[Update Program Hours]
        J --> R[View Analytics]
    end

    subgraph "Supervisor Use Cases"
        S[Supervisor] --> T[Monitor Interns]
        S --> U[Review Attendance]
        S --> V[Provide Feedback]
        S --> W[Track Progress]
    end

    subgraph "Admin Use Cases"
        X[Admin] --> Y[Manage Users]
        X --> Z[Configure System]
        X --> AA[View System Reports]
        X --> BB[Manage Companies]
    end
```

### **Sequence Diagram - Placement Approval Process**

```mermaid
sequenceDiagram
    participant S as Student
    participant C as Coordinator
    participant DB as Database
    participant E as Email System
    participant SP as Supervisor

    S->>DB: Submit Placement Request
    DB->>C: Send Notification
    C->>DB: Review Request
    C->>DB: Approve Request
    DB->>SP: Create Supervisor Account
    DB->>E: Send Login Credentials
    E->>SP: Email with Password
    DB->>S: Update OJT Status to Active
    DB->>S: Auto-approve Letter of Acceptance
    DB->>S: Send Approval Notification
```

### **Activity Diagram - Daily OJT Workflow**

```mermaid
graph TD
    A[Student Login] --> B[Time In with Photo]
    B --> C[Capture Location]
    C --> D[Start Work Activities]
    D --> E[Work Throughout Day]
    E --> F[Time Out with Photo]
    F --> G[Capture Location]
    G --> H[Submit Daily Report]
    H --> I[Coordinator Notification]
    I --> J[Coordinator Reviews Report]
    J --> K{Report Approved?}
    K -->|Yes| L[Update Progress]
    K -->|No| M[Return for Revision]
    M --> H
    L --> N[Update Hours Worked]
    N --> O[Check Completion Status]
    O --> P{Hours Complete?}
    P -->|Yes| Q[Mark OJT Complete]
    P -->|No| R[Continue Next Day]
    R --> A
```

### **State Diagram - Document Submission States**

```mermaid
stateDiagram-v2
    [*] --> Draft: Student Uploads
    Draft --> Submitted: Student Submits
    Submitted --> Under_Review: Coordinator Reviews
    Under_Review --> Approved: Coordinator Approves
    Under_Review --> Rejected: Coordinator Rejects
    Rejected --> Draft: Student Revises
    Approved --> [*]: Process Complete
    Submitted --> Cancelled: Student Cancels
    Cancelled --> Draft: Student Restarts
```

### **Component Diagram - System Architecture**

```mermaid
graph TB
    subgraph "Frontend Layer"
        A[Blade Templates]
        B[Tailwind CSS]
        C[Alpine.js]
        D[JavaScript]
    end

    subgraph "Application Layer"
        E[Controllers]
        F[Models]
        G[Middleware]
        H[Services]
    end

    subgraph "Business Logic Layer"
        I[Authentication]
        J[Authorization]
        K[File Management]
        L[Email Service]
        M[PDF Generation]
    end

    subgraph "Data Layer"
        N[MySQL Database]
        O[File Storage]
        P[Session Storage]
    end

    subgraph "External Services"
        Q[Email Provider]
        R[File System]
    end

    A --> E
    B --> A
    C --> A
    D --> A
    E --> F
    E --> G
    E --> H
    F --> N
    H --> I
    H --> J
    H --> K
    H --> L
    H --> M
    I --> N
    J --> N
    K --> O
    L --> Q
    M --> R
    G --> P
```

### **Deployment Diagram**

```mermaid
graph TB
    subgraph "Production Server"
        A[Web Server - Apache/Nginx]
        B[PHP 8.1+]
        C[Laravel 9 Application]
        D[MySQL Database]
        E[File Storage]
    end

    subgraph "Development Environment"
        F[XAMPP/WAMP]
        G[Local Database]
        H[Development Files]
    end

    subgraph "Client Devices"
        I[Desktop Browser]
        J[Mobile Browser]
        K[Tablet Browser]
    end

    I --> A
    J --> A
    K --> A
    A --> B
    B --> C
    C --> D
    C --> E
    F --> G
    F --> H
```

---

## 📊 Data Flow Diagrams

### **Level 0 - Context Diagram**

```mermaid
graph LR
    A[Student] --> B[OJT360 System]
    C[Coordinator] --> B
    D[Supervisor] --> B
    E[Admin] --> B
    B --> F[Email Service]
    B --> G[File Storage]
    B --> H[Database]
```

### **Level 1 - Main Processes**

```mermaid
graph TD
    A[Student Registration] --> B[Profile Management]
    B --> C[Placement Request]
    C --> D[Document Management]
    D --> E[Attendance Tracking]
    E --> F[Report Generation]
    F --> G[Progress Monitoring]
    
    H[Coordinator Review] --> C
    H --> D
    H --> F
    
    I[Supervisor Monitoring] --> E
    I --> F
    
    J[Admin Management] --> K[User Management]
    J --> L[System Configuration]
```

---

## 🔄 Process Flow Summary

### **Student Journey**
1. **Registration** → Email Verification → Profile Completion
2. **Placement Request** → Coordinator Review → Approval
3. **OJT Activities** → Daily Attendance → Daily Reports
4. **Document Submission** → Weekly Reports → Progress Tracking
5. **Completion** → Final Review → Certificate

### **Coordinator Workflow**
1. **Student Management** → Review Profiles → Approve Placements
2. **Document Review** → Approve/Reject → Provide Feedback
3. **Report Monitoring** → Track Progress → Ensure Compliance
4. **Program Management** → Update Requirements → Monitor Completion

### **System Operations**
1. **Authentication** → Authorization → Session Management
2. **File Handling** → Storage → Security → Backup
3. **Notifications** → Email → In-app → Real-time Updates
4. **Reporting** → Data Aggregation → PDF Generation → Analytics

---

**These UML diagrams provide a comprehensive view of your OJT360 system architecture, processes, and data flows. They can be used for documentation, development planning, and system understanding.**
