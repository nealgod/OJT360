# OJT360

End-to-end web-based internship monitoring and management system with automated documentation and evaluation workflow.

## Setup

1. Copy .env.example to .env and configure:
   - APP_URL, APP_NAME
   - DB_* and MAIL_*
   - Timezone in config/app.php (e.g., Asia/Manila)
2. Install dependencies:
   - composer install
   - npm install
3. Generate key:
   - php artisan key:generate
4. Migrate and seed:
   - php artisan migrate --seed
5. Build assets:
   - npm run dev (or npm run build)
6. Link storage:
   - php artisan storage:link

## Roles

- Admin, Coordinator, Supervisor, Intern (student)
- Email verification and first password change are enforced

## Key Features

- Placement request + coordinator approval (activates OJT)
- Attendance with photo proof; Break Time auto-deducted once per day
- Messaging and notifications
- Student dashboard progress sums attendance minutes (overtime included)

## Quick Attendance Test (no waiting)

Use Tinker to set a student’s log for a date and compute minutes.

```php
$email = 'student@example.com';
$date = now()->format('Y-m-d');
$in   = '07:00:00';
$out  = '17:30:00';

$user = App\Models\User::where('email', $email)->firstOrFail();
$log = App\Models\AttendanceLog::firstOrCreate(
    ['student_user_id' => $user->id, 'work_date' => \Carbon\Carbon::parse($date)],
    ['company_id' => optional($user->studentProfile)->assigned_company_id]
);
$log->time_in = $in;
$log->time_out = $out;

$placement = App\Models\PlacementRequest::where('student_user_id', $user->id)
    ->where('status','approved')
    ->orderByDesc('decided_at')
    ->first();
$break = $placement ? (int)($placement->break_minutes ?? 0) : 0;

$tz = 'Asia/Manila';
$timeIn = \Carbon\Carbon::parse("{$date} {$in}", $tz);
$timeOut = \Carbon\Carbon::parse("{$date} {$out}", $tz);
$total = max(0, $timeIn->diffInMinutes($timeOut));
$minutes = max(0, $total - $break);

$log->minutes_worked = $minutes;
$log->save();
```

## Notes

- Shift Start/End and Working Days are hidden in the UI for now (kept in schema for future use).
