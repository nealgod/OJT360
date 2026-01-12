# OJT360 Database Schema Analysis (Final)

> **Generated:** 2026-01-12  
> **Total Migrations:** 71  
> **Database:** MySQL/MariaDB  
> **Active Tables:** 29 (Cleanup Completed)

---

## 🎯 Final Schema Overview

The OJT360 database has been optimized for the **Acceptance Letter** workflow. All legacy placement logic (placement_requests) has been deprecated and successfully removed from the active schema.

### Core Structure
1. **Users & Profiles**: Base `users` table linked to `student_profiles`, `coordinator_profiles`, and `supervisor_profiles`.
2. **Academic Org**: `departments` and `programs` with required hour tracking.
3. **Placements**: Managed via `companies` and the `acceptance_letters` system.
4. **Monitoring**: High-precision `attendance_logs` (Quad-system) and `weekly_reports`.
5. **Evaluations**: Detailed `monthly_evaluations` and `final_evaluations`.
6. **Documentation**: Requirements and submissions tracking.

---

## 📅 Maintenance Note
As of January 2026, the migration chain has been "Safety-Wrapped." This ensures that even if a deployment environment has an inconsistent table state, the migration process will complete successfully without foreign key violations.

**Total Active Tables:** 29
