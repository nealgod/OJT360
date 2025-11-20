# OJT360 Attendance System Enhancement - Complete Specification

## 📋 Executive Summary

This document outlines the complete enhancement of the attendance tracking system to include schedule validation, late tracking, overtime/undertime calculation, and comprehensive monitoring for coordinators.

---

## 🎯 Core Requirements

### 1. Schedule-Based Time Validation

**Business Rule:**
- Students can time in **1 hour before** their scheduled start time
- Hours are counted **only from scheduled start time**, not actual time in
- Students can time out at scheduled end time or later
- Hours are counted **only up to scheduled end time** (no automatic overtime)

**Example Scenario:**
```
Acceptance Letter Schedule:
- Start: 8:00 AM
- End: 4:00 PM (16:00)
- Break: 1 hour
- Expected Hours: 7 hours/day

Scenario 1: Early Arrival
- Student times in: 7:30 AM ✅ (allowed, 30 min before)
- Counting starts: 8:00 AM (scheduled start)
- Student times out: 4:00 PM
- Hours counted: 8:00 AM - 4:00 PM = 8 hours - 1 hour break = 7 hours ✅

Scenario 2: Too Early
- Student times in: 6:30 AM ❌ (more than 1 hour before)
- System blocks: "Cannot time in before 7:00 AM. Your shift starts at 8:00 AM."

Scenario 3: Late Arrival
- Student times in: 9:15 AM ⚠️ (1 hour 15 min late)
- Counting starts: 9:15 AM (actual time in)
- Student times out: 4:00 PM
- Hours counted: 9:15 AM - 4:00 PM = 6.75 hours - 1 hour break = 5.75 hours
- Status: LATE (75 minutes late)
- Undertime: 1.25 hours

Scenario 4: Overtime
- Student times in: 8:00 AM
- Student times out: 6:00 PM (2 hours after schedule)
- Hours counted: 8:00 AM - 4:00 PM = 8 hours - 1 hour break = 7 hours
- Overtime: 2 hours (recorded separately, not auto-counted)
- Note: "Worked 2 hours beyond schedule"
```

---

## 🗄️ Database Schema Changes

### Migration 1: Add Tracking Fields to `attendance_logs`

```php
Schema::table('attendance_logs', function (Blueprint $table) {
    // Schedule information (from acceptance letter)
    $table->time('scheduled_start')->nullable()->after('work_date');
    $table->time('scheduled_end')->nullable()->after('scheduled_start');
    $table->unsignedSmallInteger('scheduled_break_minutes')->default(0)->after('scheduled_end');
    
    // Actual work time tracking
    $table->time('effective_start')->nullable()->after('time_in'); // When counting starts
    $table->time('effective_end')->nullable()->after('time_out'); // When counting ends
    
    // Late tracking
    $table->boolean('is_late')->default(false)->after('status');
    $table->unsignedSmallInteger('late_minutes')->default(0)->after('is_late');
    
    // Overtime tracking
    $table->unsignedSmallInteger('overtime_minutes')->default(0)->after('minutes_worked');
    $table->unsignedSmallInteger('regular_minutes')->default(0)->after('overtime_minutes');
    
    // Undertime tracking
    $table->boolean('is_undertime')->default(false)->after('overtime_minutes');
    $table->unsignedSmallInteger('undertime_minutes')->default(0)->after('is_undertime');
    
    // Expected hours (from schedule)
    $table->unsignedSmallInteger('expected_minutes')->default(0)->after('undertime_minutes');
    
    // Flags for coordinator monitoring
    $table->boolean('requires_review')->default(false)->after('status');
    $table->text('review_notes')->nullable()->after('requires_review');
});
```

### Migration 2: Create `attendance_flags` Table (for coordinator monitoring)

```php
Schema::create('attendance_flags', function (Blueprint $table) {
    $table->id();
    $table->foreignId('attendance_log_id')->constrained('attendance_logs')->cascadeOnDelete();
    $table->foreignId('student_user_id')->constrained('users')->cascadeOnDelete();
    $table->date('work_date');
    $table->enum('flag_type', [
        'late',
        'very_late',
        'undertime',
        'excessive_overtime',
        'missed_time_in',
        'missed_time_out',
        'absent',
        'suspicious_hours'
    ]);
    $table->string('severity')->default('warning'); // info, warning, critical
    $table->text('description');
    $table->boolean('is_resolved')->default(false);
    $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamp('resolved_at')->nullable();
    $table->text('resolution_notes')->nullable();
    $table->timestamps();
    
    $table->index(['student_user_id', 'work_date']);
    $table->index(['flag_type', 'is_resolved']);
});
```

---

## 🔧 Implementation Details

### Phase 1: Schedule Extraction from Acceptance Letter

**Current Acceptance Letter `work_schedule` Structure:**
```json
{
    "monday": "8:00 AM - 5:00 PM",
    "tuesday": "8:00 AM - 5:00 PM",
    "wednesday": "8:00 AM - 5:00 PM",
    "thursday": "8:00 AM - 5:00 PM",
    "friday": "8:00 AM - 5:00 PM",
    "saturday": "Off",
    "sunday": "Off",
    "break_minutes": 60
}
```

**New Helper Method in AcceptanceLetter Model:**
```php
public function getScheduleForDate($date)
{
    $dayOfWeek = strtolower($date->format('l')); // monday, tuesday, etc.
    $schedule = $this->work_schedule[$dayOfWeek] ?? null;
    
    if (!$schedule || $schedule === 'Off') {
        return null; // No work scheduled
    }
    
    // Parse "8:00 AM - 5:00 PM" format
    preg_match('/(\d{1,2}:\d{2}\s*[AP]M)\s*-\s*(\d{1,2}:\d{2}\s*[AP]M)/', $schedule, $matches);
    
    if (count($matches) !== 3) {
        return null;
    }
    
    return [
        'start' => Carbon::createFromFormat('g:i A', trim($matches[1]))->format('H:i:s'),
        'end' => Carbon::createFromFormat('g:i A', trim($matches[2]))->format('H:i:s'),
        'break_minutes' => $this->work_schedule['break_minutes'] ?? 60,
    ];
}

public function getExpectedDailyMinutes()
{
    // Get a sample day schedule (use Monday as default)
    $schedule = $this->getScheduleForDate(now()->startOfWeek());
    
    if (!$schedule) {
        return 0;
    }
    
    $start = Carbon::createFromFormat('H:i:s', $schedule['start']);
    $end = Carbon::createFromFormat('H:i:s', $schedule['end']);
    $totalMinutes = $start->diffInMinutes($end);
    
    return max(0, $totalMinutes - $schedule['break_minutes']);
}
```

### Phase 2: Enhanced Time In Logic

```php
public function timeIn(Request $request)
{
    // ... existing validation ...
    
    $user = Auth::user();
    $today = now()->toDateString();
    
    // Get acceptance letter and schedule
    $acceptance = AcceptanceLetter::where('student_user_id', $user->id)
        ->where('start_date', '<=', $today)
        ->where('end_date', '>=', $today)
        ->latest()
        ->first();
    
    if (!$acceptance) {
        return back()->with('error', 'No active acceptance letter found. Please contact your coordinator.');
    }
    
    $schedule = $acceptance->getScheduleForDate(now());
    
    if (!$schedule) {
        return back()->with('error', 'No work scheduled for today (' . now()->format('l') . ').');
    }
    
    // Parse schedule times
    $scheduledStart = Carbon::createFromFormat('H:i:s', $schedule['start']);
    $earliestAllowed = $scheduledStart->copy()->subHour(); // 1 hour before
    $currentTime = now();
    
    // Validation: Cannot time in more than 1 hour before schedule
    if ($currentTime->lt($earliestAllowed)) {
        return back()->with('error', sprintf(
            'Cannot time in before %s. Your shift starts at %s.',
            $earliestAllowed->format('g:i A'),
            $scheduledStart->format('g:i A')
        ));
    }
    
    // Determine if late
    $isLate = $currentTime->gt($scheduledStart);
    $lateMinutes = $isLate ? $currentTime->diffInMinutes($scheduledStart) : 0;
    
    // Determine effective start time (when counting begins)
    $effectiveStart = $isLate ? $currentTime : $scheduledStart;
    
    // Create/update attendance log
    $log = AttendanceLog::updateOrCreate(
        [
            'student_user_id' => $user->id,
            'work_date' => $today,
        ],
        [
            'company_id' => $user->studentProfile?->assigned_company_id,
            'time_in' => $currentTime->format('H:i:s'),
            'scheduled_start' => $schedule['start'],
            'scheduled_end' => $schedule['end'],
            'scheduled_break_minutes' => $schedule['break_minutes'],
            'effective_start' => $effectiveStart->format('H:i:s'),
            'is_late' => $isLate,
            'late_minutes' => $lateMinutes,
            'expected_minutes' => $acceptance->getExpectedDailyMinutes(),
            'photo_in_path' => $request->file('photo_in')->store('attendance-photos', 'public'),
            'status' => 'approved',
        ]
    );
    
    // Create flag if significantly late (>15 minutes)
    if ($lateMinutes > 15) {
        AttendanceFlag::create([
            'attendance_log_id' => $log->id,
            'student_user_id' => $user->id,
            'work_date' => $today,
            'flag_type' => $lateMinutes > 60 ? 'very_late' : 'late',
            'severity' => $lateMinutes > 60 ? 'critical' : 'warning',
            'description' => sprintf(
                'Student arrived %d minutes late (scheduled: %s, actual: %s)',
                $lateMinutes,
                $scheduledStart->format('g:i A'),
                $currentTime->format('g:i A')
            ),
        ]);
    }
    
    return back()->with('success', $isLate 
        ? "Timed in successfully. Note: You are {$lateMinutes} minutes late."
        : 'Timed in successfully.');
}
```

### Phase 3: Enhanced Time Out Logic

```php
public function timeOut(Request $request)
{
    // ... existing validation ...
    
    $user = Auth::user();
    $today = now()->toDateString();
    
    $log = AttendanceLog::where('student_user_id', $user->id)
        ->where('work_date', $today)
        ->first();
    
    if (!$log || !$log->time_in) {
        return back()->with('error', 'Please time in first.');
    }
    
    $currentTime = now();
    $scheduledEnd = Carbon::createFromFormat('H:i:s', $log->scheduled_end);
    
    // Determine effective end time (when counting stops)
    // If time out is after schedule, count only up to scheduled end
    $effectiveEnd = $currentTime->gt($scheduledEnd) ? $scheduledEnd : $currentTime;
    
    // Calculate overtime (time worked beyond schedule)
    $overtimeMinutes = $currentTime->gt($scheduledEnd) 
        ? $currentTime->diffInMinutes($scheduledEnd) 
        : 0;
    
    // Calculate actual work minutes (from effective start to effective end)
    $effectiveStart = Carbon::createFromFormat('H:i:s', $log->effective_start);
    $totalMinutes = $effectiveStart->diffInMinutes($effectiveEnd);
    
    // Deduct break time
    $minutesWorked = max(0, $totalMinutes - $log->scheduled_break_minutes);
    
    // Calculate regular minutes (should match expected)
    $regularMinutes = min($minutesWorked, $log->expected_minutes);
    
    // Check for undertime
    $isUndertime = $minutesWorked < $log->expected_minutes;
    $undertimeMinutes = $isUndertime ? $log->expected_minutes - $minutesWorked : 0;
    
    // Update log
    $log->update([
        'time_out' => $currentTime->format('H:i:s'),
        'effective_end' => $effectiveEnd->format('H:i:s'),
        'minutes_worked' => $minutesWorked,
        'regular_minutes' => $regularMinutes,
        'overtime_minutes' => $overtimeMinutes,
        'is_undertime' => $isUndertime,
        'undertime_minutes' => $undertimeMinutes,
        'photo_out_path' => $request->file('photo_out')->store('attendance-photos', 'public'),
    ]);
    
    // Create flags
    if ($isUndertime && $undertimeMinutes > 30) {
        AttendanceFlag::create([
            'attendance_log_id' => $log->id,
            'student_user_id' => $user->id,
            'work_date' => $today,
            'flag_type' => 'undertime',
            'severity' => $undertimeMinutes > 120 ? 'critical' : 'warning',
            'description' => sprintf(
                'Student left %d minutes early (expected: %s, actual: %s)',
                $undertimeMinutes,
                $scheduledEnd->format('g:i A'),
                $currentTime->format('g:i A')
            ),
        ]);
    }
    
    if ($overtimeMinutes > 120) { // More than 2 hours overtime
        AttendanceFlag::create([
            'attendance_log_id' => $log->id,
            'student_user_id' => $user->id,
            'work_date' => $today,
            'flag_type' => 'excessive_overtime',
            'severity' => 'info',
            'description' => sprintf(
                'Student worked %d minutes beyond schedule',
                $overtimeMinutes
            ),
        ]);
    }
    
    $message = 'Timed out successfully.';
    if ($overtimeMinutes > 0) {
        $message .= sprintf(' Note: You worked %.1f hours beyond your schedule.', $overtimeMinutes / 60);
    }
    if ($isUndertime) {
        $message .= sprintf(' Note: You left %.1f hours early.', $undertimeMinutes / 60);
    }
    
    return back()->with('success', $message);
}
```

### Phase 4: Missed Attendance Detection (Cron Job)

```php
// app/Console/Commands/DetectMissedAttendance.php

public function handle()
{
    $yesterday = now()->subDay()->toDateString();
    
    // Get all students with active OJT
    $activeStudents = User::whereHas('studentProfile', function($q) {
        $q->where('ojt_status', 'active');
    })->with('studentProfile')->get();
    
    foreach ($activeStudents as $student) {
        // Get acceptance letter
        $acceptance = AcceptanceLetter::where('student_user_id', $student->id)
            ->where('start_date', '<=', $yesterday)
            ->where('end_date', '>=', $yesterday)
            ->latest()
            ->first();
        
        if (!$acceptance) {
            continue; // No active acceptance
        }
        
        // Check if yesterday was a work day
        $schedule = $acceptance->getScheduleForDate(Carbon::parse($yesterday));
        if (!$schedule) {
            continue; // Not a work day
        }
        
        // Check attendance log
        $log = AttendanceLog::where('student_user_id', $student->id)
            ->where('work_date', $yesterday)
            ->first();
        
        if (!$log) {
            // Completely absent
            AttendanceFlag::create([
                'attendance_log_id' => null,
                'student_user_id' => $student->id,
                'work_date' => $yesterday,
                'flag_type' => 'absent',
                'severity' => 'critical',
                'description' => 'Student did not time in for scheduled work day',
            ]);
        } elseif ($log->time_in && !$log->time_out) {
            // Missed time out
            AttendanceFlag::create([
                'attendance_log_id' => $log->id,
                'student_user_id' => $student->id,
                'work_date' => $yesterday,
                'flag_type' => 'missed_time_out',
                'severity' => 'warning',
                'description' => 'Student timed in but forgot to time out',
            ]);
        }
    }
}
```

---

## 📊 Coordinator Monitoring Dashboard

### Features:

1. **Real-time Attendance Overview**
   - Students currently at work
   - Students who are late today
   - Students who haven't timed in yet (past schedule)
   - Students with incomplete attendance

2. **Flags & Alerts**
   - Critical: Absent, Very Late (>1 hour), Excessive Undertime
   - Warning: Late, Undertime, Missed Time Out
   - Info: Overtime

3. **Reports**
   - Daily attendance summary
   - Weekly attendance patterns
   - Student attendance history
   - Late frequency report
   - Undertime/Overtime analysis

4. **Actions**
   - Review and resolve flags
   - Add notes to attendance records
   - Export attendance data
   - Send reminders to students

---

## 🎓 Defense Points & Justifications

### 1. **Why 1 hour early allowance?**
- **Real-world scenario:** Students may arrive early due to transportation
- **Flexibility:** Allows students to settle in before work starts
- **Fair counting:** Hours only count from scheduled time, preventing abuse

### 2. **Why not count overtime automatically?**
- **Company policy:** Overtime usually requires approval
- **Prevents abuse:** Students can't inflate hours by staying late
- **Tracking:** System still records overtime for supervisor review

### 3. **Why track late/undertime?**
- **Accountability:** Ensures students meet required hours
- **Coordinator oversight:** Identifies patterns of tardiness
- **Intervention:** Allows early intervention for struggling students

### 4. **Why flag system?**
- **Scalability:** Coordinators manage many students
- **Prioritization:** Critical issues highlighted first
- **Audit trail:** Complete history of attendance issues

### 5. **Why schedule-based validation?**
- **Accuracy:** Ensures hours match company expectations
- **Compliance:** Meets OJT hour requirements
- **Fairness:** Consistent rules for all students

---

## 📝 Implementation Checklist

### Database
- [ ] Create migration for attendance_logs enhancements
- [ ] Create migration for attendance_flags table
- [ ] Run migrations
- [ ] Seed test data

### Models
- [ ] Update AttendanceLog model with new fields
- [ ] Create AttendanceFlag model
- [ ] Add helper methods to AcceptanceLetter model
- [ ] Add relationships

### Controllers
- [ ] Update AttendanceController::timeIn()
- [ ] Update AttendanceController::timeOut()
- [ ] Update AttendanceController::recovery()
- [ ] Create AttendanceFlagController for coordinator
- [ ] Create AttendanceReportController

### Views
- [ ] Update attendance/index.blade.php (student view)
- [ ] Create coord/attendance/dashboard.blade.php
- [ ] Create coord/attendance/flags.blade.php
- [ ] Create coord/attendance/reports.blade.php
- [ ] Add attendance stats to coordinator dashboard

### Commands
- [ ] Create DetectMissedAttendance command
- [ ] Schedule command in Kernel.php (daily at 6 AM)

### Testing
- [ ] Test early time in (within 1 hour)
- [ ] Test too early time in (>1 hour)
- [ ] Test late time in
- [ ] Test on-time time out
- [ ] Test early time out (undertime)
- [ ] Test late time out (overtime)
- [ ] Test missed time out detection
- [ ] Test absent detection
- [ ] Test flag creation
- [ ] Test coordinator dashboard

### Documentation
- [ ] Update user manual
- [ ] Create coordinator guide
- [ ] Document flag types and severities
- [ ] Create API documentation (if needed)

---

## 🚀 Deployment Plan

1. **Backup database**
2. **Run migrations**
3. **Update code**
4. **Test on staging**
5. **Deploy to production**
6. **Monitor for issues**
7. **Train coordinators**

---

## 📈 Success Metrics

- ✅ 100% of time-ins validated against schedule
- ✅ Late arrivals automatically flagged
- ✅ Missed attendance detected within 24 hours
- ✅ Coordinator can view all flags in one dashboard
- ✅ Accurate hour counting (only scheduled hours)
- ✅ Zero manual hour adjustments needed

---

## 🔮 Future Enhancements

1. **Mobile app notifications** for missed time in/out
2. **Geofencing** to ensure students are at company location
3. **Biometric integration** for enhanced verification
4. **Automated reports** sent to coordinators weekly
5. **Student self-service** to explain late/undertime reasons
6. **Integration with payroll** (if applicable)


---

## 🔄 UPDATED: Overtime Management & Complete Recovery System

### Overtime Handling - Revised Approach

**Problem:** Students sometimes legitimately work overtime (urgent tasks, deadlines, etc.)

**Solution:** Record overtime but require coordinator approval to count toward total hours.

#### Overtime Workflow:

```
1. Student works beyond schedule
   ↓
2. System records overtime_minutes (not counted yet)
   ↓
3. Creates flag for coordinator review
   ↓
4. Coordinator reviews:
   - Approves: Overtime added to minutes_worked
   - Adjusts: Coordinator sets approved overtime amount
   - Rejects: Overtime not counted
   ↓
5. Student sees updated hours in their record
```

#### Database Changes for Overtime:

```php
Schema::table('attendance_logs', function (Blueprint $table) {
    // Add overtime approval fields
    $table->unsignedSmallInteger('overtime_approved_minutes')->default(0)->after('overtime_minutes');
    $table->boolean('overtime_requires_approval')->default(false)->after('overtime_approved_minutes');
    $table->foreignId('overtime_approved_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamp('overtime_approved_at')->nullable();
    $table->text('overtime_reason')->nullable(); // Student can explain why
});
```

#### Updated Time Out Logic with Overtime:

```php
public function timeOut(Request $request)
{
    // ... existing code ...
    
    $currentTime = now();
    $scheduledEnd = Carbon::createFromFormat('H:i:s', $log->scheduled_end);
    
    // Calculate overtime
    $overtimeMinutes = $currentTime->gt($scheduledEnd) 
        ? $currentTime->diffInMinutes($scheduledEnd) 
        : 0;
    
    // Base calculation (scheduled hours only)
    $effectiveEnd = $currentTime->gt($scheduledEnd) ? $scheduledEnd : $currentTime;
    $effectiveStart = Carbon::createFromFormat('H:i:s', $log->effective_start);
    $totalMinutes = $effectiveStart->diffInMinutes($effectiveEnd);
    $minutesWorked = max(0, $totalMinutes - $log->scheduled_break_minutes);
    
    // Update log
    $log->update([
        'time_out' => $currentTime->format('H:i:s'),
        'effective_end' => $effectiveEnd->format('H:i:s'),
        'minutes_worked' => $minutesWorked, // Base hours only
        'overtime_minutes' => $overtimeMinutes,
        'overtime_requires_approval' => $overtimeMinutes > 0,
        'photo_out_path' => $request->file('photo_out')->store('attendance-photos', 'public'),
    ]);
    
    // Create flag if overtime
    if ($overtimeMinutes > 0) {
        AttendanceFlag::create([
            'attendance_log_id' => $log->id,
            'student_user_id' => $user->id,
            'work_date' => $today,
            'flag_type' => $overtimeMinutes > 120 ? 'excessive_overtime' : 'overtime',
            'severity' => $overtimeMinutes > 120 ? 'warning' : 'info',
            'description' => sprintf(
                'Student worked %d minutes (%s hours) beyond schedule. Requires approval.',
                $overtimeMinutes,
                number_format($overtimeMinutes / 60, 1)
            ),
        ]);
    }
    
    $message = 'Timed out successfully.';
    if ($overtimeMinutes > 0) {
        $message .= sprintf(
            ' You worked %.1f hours overtime. This will be reviewed by your coordinator.',
            $overtimeMinutes / 60
        );
    }
    
    return back()->with('success', $message);
}
```

#### Coordinator Overtime Approval:

```php
// CoordinatorAttendanceController.php

public function approveOvertime(Request $request, AttendanceLog $log)
{
    $request->validate([
        'approved_minutes' => 'required|integer|min:0|max:' . $log->overtime_minutes,
        'notes' => 'nullable|string|max:500',
    ]);
    
    $approvedMinutes = $request->approved_minutes;
    
    // Add approved overtime to total worked minutes
    $newTotalMinutes = $log->minutes_worked + $approvedMinutes;
    
    $log->update([
        'overtime_approved_minutes' => $approvedMinutes,
        'minutes_worked' => $newTotalMinutes,
        'overtime_requires_approval' => false,
        'overtime_approved_by' => Auth::id(),
        'overtime_approved_at' => now(),
        'review_notes' => $request->notes,
    ]);
    
    // Resolve the flag
    AttendanceFlag::where('attendance_log_id', $log->id)
        ->where('flag_type', 'LIKE', '%overtime%')
        ->update([
            'is_resolved' => true,
            'resolved_by' => Auth::id(),
            'resolved_at' => now(),
            'resolution_notes' => sprintf(
                'Approved %d of %d overtime minutes. %s',
                $approvedMinutes,
                $log->overtime_minutes,
                $request->notes ?? ''
            ),
        ]);
    
    return back()->with('success', sprintf(
        'Approved %.1f hours of overtime for %s',
        $approvedMinutes / 60,
        $log->student->name
    ));
}
```

---

## 🔧 COMPLETE Recovery System

### Recovery Scenarios:

1. **Missed Time In** - Student forgot to time in
2. **Missed Time Out** - Student forgot to time out (existing)
3. **Missed Both** - Student forgot both time in and time out
4. **Wrong Time** - Student timed in/out at wrong time (needs correction)

### Enhanced Recovery Feature:

#### Database Changes:

```php
Schema::table('attendance_logs', function (Blueprint $table) {
    // Recovery tracking
    $table->boolean('is_recovered')->default(false)->after('status');
    $table->enum('recovery_type', [
        'none',
        'missed_time_in',
        'missed_time_out',
        'missed_both',
        'correction'
    ])->default('none')->after('is_recovered');
    $table->text('recovery_reason')->nullable()->after('recovery_type');
    $table->string('recovery_proof_path')->nullable()->after('recovery_reason');
    $table->timestamp('recovered_at')->nullable()->after('recovery_proof_path');
});
```

#### Recovery Controller Methods:

```php
// AttendanceController.php

public function showRecoveryForm(Request $request)
{
    $user = Auth::user();
    
    // Get incomplete or missing attendance records
    $incompleteRecords = AttendanceLog::where('student_user_id', $user->id)
        ->where(function($q) {
            $q->whereNull('time_in')
              ->orWhereNull('time_out');
        })
        ->where('work_date', '>=', now()->subDays(7)) // Last 7 days
        ->orderByDesc('work_date')
        ->get();
    
    // Get dates with no records (should have worked)
    $acceptance = AcceptanceLetter::where('student_user_id', $user->id)
        ->latest()
        ->first();
    
    $missedDates = [];
    if ($acceptance) {
        for ($i = 1; $i <= 7; $i++) {
            $date = now()->subDays($i);
            $schedule = $acceptance->getScheduleForDate($date);
            
            if ($schedule) { // Was a work day
                $hasRecord = AttendanceLog::where('student_user_id', $user->id)
                    ->where('work_date', $date->toDateString())
                    ->exists();
                
                if (!$hasRecord) {
                    $missedDates[] = [
                        'date' => $date,
                        'schedule' => $schedule,
                    ];
                }
            }
        }
    }
    
    return view('attendance.recovery', compact('incompleteRecords', 'missedDates'));
}

public function recoverMissedTimeIn(Request $request)
{
    $request->validate([
        'work_date' => 'required|date|before_or_equal:today',
        'time_in' => 'required|date_format:H:i',
        'time_out' => 'required|date_format:H:i|after:time_in',
        'reason' => 'required|string|max:500',
        'proof_photo' => 'required|image|mimes:jpg,jpeg,png|max:5120',
    ]);
    
    $user = Auth::user();
    $workDate = Carbon::parse($request->work_date);
    
    // Get acceptance letter and schedule
    $acceptance = AcceptanceLetter::where('student_user_id', $user->id)
        ->where('start_date', '<=', $workDate)
        ->where('end_date', '>=', $workDate)
        ->latest()
        ->first();
    
    if (!$acceptance) {
        return back()->withErrors(['work_date' => 'No active acceptance letter for this date.']);
    }
    
    $schedule = $acceptance->getScheduleForDate($workDate);
    
    if (!$schedule) {
        return back()->withErrors(['work_date' => 'Not a scheduled work day.']);
    }
    
    // Check if record already exists
    $existing = AttendanceLog::where('student_user_id', $user->id)
        ->where('work_date', $workDate->toDateString())
        ->first();
    
    if ($existing && $existing->time_in && $existing->time_out) {
        return back()->withErrors(['work_date' => 'Attendance already recorded for this date.']);
    }
    
    // Parse times
    $timeIn = Carbon::createFromFormat('H:i', $request->time_in);
    $timeOut = Carbon::createFromFormat('H:i', $request->time_out);
    $scheduledStart = Carbon::createFromFormat('H:i:s', $schedule['start']);
    $scheduledEnd = Carbon::createFromFormat('H:i:s', $schedule['end']);
    
    // Validate times are reasonable
    if ($timeIn->diffInMinutes($timeOut) > 960) { // Max 16 hours
        return back()->withErrors(['time_out' => 'Work duration exceeds maximum allowed (16 hours).']);
    }
    
    // Calculate late
    $isLate = $timeIn->gt($scheduledStart);
    $lateMinutes = $isLate ? $timeIn->diffInMinutes($scheduledStart) : 0;
    
    // Determine effective times
    $effectiveStart = $isLate ? $timeIn : $scheduledStart;
    $effectiveEnd = $timeOut->gt($scheduledEnd) ? $scheduledEnd : $timeOut;
    
    // Calculate minutes
    $totalMinutes = $effectiveStart->diffInMinutes($effectiveEnd);
    $minutesWorked = max(0, $totalMinutes - $schedule['break_minutes']);
    
    // Calculate overtime
    $overtimeMinutes = $timeOut->gt($scheduledEnd) 
        ? $timeOut->diffInMinutes($scheduledEnd) 
        : 0;
    
    // Calculate undertime
    $expectedMinutes = $acceptance->getExpectedDailyMinutes();
    $isUndertime = $minutesWorked < $expectedMinutes;
    $undertimeMinutes = $isUndertime ? $expectedMinutes - $minutesWorked : 0;
    
    // Store proof photo
    $proofPath = $request->file('proof_photo')->store('attendance-recovery', 'public');
    
    // Create or update record
    $log = AttendanceLog::updateOrCreate(
        [
            'student_user_id' => $user->id,
            'work_date' => $workDate->toDateString(),
        ],
        [
            'company_id' => $user->studentProfile?->assigned_company_id,
            'time_in' => $timeIn->format('H:i:s'),
            'time_out' => $timeOut->format('H:i:s'),
            'scheduled_start' => $schedule['start'],
            'scheduled_end' => $schedule['end'],
            'scheduled_break_minutes' => $schedule['break_minutes'],
            'effective_start' => $effectiveStart->format('H:i:s'),
            'effective_end' => $effectiveEnd->format('H:i:s'),
            'minutes_worked' => $minutesWorked,
            'expected_minutes' => $expectedMinutes,
            'is_late' => $isLate,
            'late_minutes' => $lateMinutes,
            'overtime_minutes' => $overtimeMinutes,
            'overtime_requires_approval' => $overtimeMinutes > 0,
            'is_undertime' => $isUndertime,
            'undertime_minutes' => $undertimeMinutes,
            'is_recovered' => true,
            'recovery_type' => $existing ? 'missed_time_out' : 'missed_both',
            'recovery_reason' => $request->reason,
            'recovery_proof_path' => $proofPath,
            'recovered_at' => now(),
            'status' => 'pending', // Requires coordinator approval
            'requires_review' => true,
        ]
    );
    
    // Create flag for coordinator review
    AttendanceFlag::create([
        'attendance_log_id' => $log->id,
        'student_user_id' => $user->id,
        'work_date' => $workDate->toDateString(),
        'flag_type' => 'recovery_pending',
        'severity' => 'warning',
        'description' => sprintf(
            'Student recovered %s attendance. Reason: %s',
            $log->recovery_type === 'missed_both' ? 'complete' : 'partial',
            $request->reason
        ),
    ]);
    
    return back()->with('success', 
        'Recovery submitted successfully. Your coordinator will review and approve it.'
    );
}

public function recoverMissedTimeOut(Request $request)
{
    // Similar to existing recovery() method but with enhanced tracking
    // ... (keep existing logic, add recovery tracking fields)
}
```

#### Coordinator Recovery Approval:

```php
// CoordinatorAttendanceController.php

public function reviewRecovery(AttendanceLog $log)
{
    return view('coord.attendance.review-recovery', compact('log'));
}

public function approveRecovery(Request $request, AttendanceLog $log)
{
    $request->validate([
        'action' => 'required|in:approve,adjust,reject',
        'adjusted_time_in' => 'nullable|date_format:H:i',
        'adjusted_time_out' => 'nullable|date_format:H:i',
        'notes' => 'nullable|string|max:500',
    ]);
    
    if ($request->action === 'reject') {
        $log->update([
            'status' => 'flagged',
            'requires_review' => false,
            'review_notes' => $request->notes,
        ]);
        
        AttendanceFlag::where('attendance_log_id', $log->id)
            ->where('flag_type', 'recovery_pending')
            ->update([
                'is_resolved' => true,
                'resolved_by' => Auth::id(),
                'resolved_at' => now(),
                'resolution_notes' => 'Recovery rejected: ' . $request->notes,
            ]);
        
        return back()->with('error', 'Recovery rejected.');
    }
    
    if ($request->action === 'adjust') {
        // Recalculate with adjusted times
        // ... (similar calculation logic)
    }
    
    // Approve
    $log->update([
        'status' => 'approved',
        'requires_review' => false,
        'review_notes' => $request->notes,
    ]);
    
    AttendanceFlag::where('attendance_log_id', $log->id)
        ->where('flag_type', 'recovery_pending')
        ->update([
            'is_resolved' => true,
            'resolved_by' => Auth::id(),
            'resolved_at' => now(),
            'resolution_notes' => 'Recovery approved: ' . $request->notes,
        ]);
    
    return back()->with('success', 'Recovery approved successfully.');
}
```

---

## 📊 Updated Coordinator Dashboard Features

### 1. Overtime Management Tab
- List of all pending overtime approvals
- Quick approve/adjust/reject actions
- Bulk approval for multiple students
- Overtime history and patterns

### 2. Recovery Management Tab
- List of all pending recovery requests
- View proof photos and reasons
- Approve/adjust/reject with notes
- Recovery history

### 3. Enhanced Flags Dashboard
- **Critical:** Absent, Very Late (>1hr), Excessive Undertime
- **Warning:** Late, Undertime, Recovery Pending, Overtime Pending
- **Info:** Approved Overtime, Resolved Issues

### 4. Student Attendance Detail View
- Complete attendance history
- All flags and resolutions
- Overtime approved vs pending
- Recovery requests history
- Edit/adjust any attendance record

---

## 🎯 Updated Implementation Checklist

### Phase 1: Core + Overtime
- [ ] Add overtime approval fields to attendance_logs
- [ ] Update timeOut() to handle overtime properly
- [ ] Create coordinator overtime approval interface
- [ ] Test overtime workflow

### Phase 2: Complete Recovery System
- [ ] Add recovery tracking fields
- [ ] Create recovery form for students (all scenarios)
- [ ] Create coordinator recovery review interface
- [ ] Test all recovery scenarios

### Phase 3: Coordinator Dashboard
- [ ] Overtime management tab
- [ ] Recovery management tab
- [ ] Enhanced flags dashboard
- [ ] Student detail view with edit capability

---

## 💡 Updated Example Scenarios

### Scenario: Legitimate Overtime
```
Schedule: 8:00 AM - 4:00 PM, 1 hour break
Time In: 8:00 AM
Time Out: 6:30 PM (2.5 hours overtime)

Initial Calculation:
- Minutes Worked: 420 (7 hours - scheduled only)
- Overtime Minutes: 150 (2.5 hours - pending approval)
- Status: Requires coordinator review

Coordinator Reviews:
- Approves 2 hours (120 minutes)
- Reason: "Project deadline, supervisor confirmed"

Final Result:
- Minutes Worked: 540 (9 hours total)
- Overtime Approved: 120 minutes
- Status: Approved ✅
```

### Scenario: Complete Recovery (Missed Both)
```
Student forgot to time in/out on Monday

Recovery Request:
- Date: Monday
- Time In: 8:15 AM (15 min late)
- Time Out: 4:00 PM
- Reason: "Phone died, couldn't access app"
- Proof: Photo of dead phone + work output

Coordinator Reviews:
- Checks with supervisor
- Verifies student was present
- Approves recovery

Result:
- Attendance recorded
- Marked as recovered
- Late flag created (15 min)
- Status: Approved ✅
```
