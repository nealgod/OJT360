<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Weekly Accomplishment Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #8B0000; padding-bottom: 20px; }
        .header h1 { color: #8B0000; margin: 0; font-size: 24px; }
        .header p { margin: 5px 0; color: #666; }
        .section { margin-bottom: 25px; }
        .section h2 { color: #8B0000; border-bottom: 1px solid #ddd; padding-bottom: 5px; font-size: 18px; }
        .section h3 { color: #333; font-size: 16px; margin-top: 20px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px; }
        .info-item { padding: 8px; background: #f9f9f9; border-radius: 4px; }
        .info-label { font-weight: bold; color: #333; }
        .daily-report { margin-bottom: 15px; padding: 10px; border-left: 4px solid #8B0000; background: #f9f9f9; }
        .daily-date { font-weight: bold; color: #8B0000; margin-bottom: 5px; }
        .daily-status { font-size: 12px; color: #666; margin-bottom: 5px; }
        .daily-content { font-size: 14px; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin: 20px 0; }
        .stat-box { text-align: center; padding: 15px; background: #f0f0f0; border-radius: 8px; }
        .stat-number { font-size: 24px; font-weight: bold; color: #8B0000; }
        .stat-label { font-size: 12px; color: #666; }
        .list-item { margin-bottom: 8px; padding-left: 15px; position: relative; }
        .list-item:before { content: "•"; position: absolute; left: 0; color: #8B0000; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>WEEKLY ACCOMPLISHMENT REPORT</h1>
        <p><strong>Student:</strong> {{ Auth::user()->name }}</p>
        <p><strong>Period:</strong> {{ $weekStart->format('M d, Y') }} - {{ $weekEnd->format('M d, Y') }}</p>
    </div>

    <div class="section">
        <h2>Student Information</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Name:</span> {{ Auth::user()->name }}
            </div>
            <div class="info-item">
                <span class="info-label">Student ID:</span> {{ Auth::user()->studentProfile?->student_id ?? 'N/A' }}
            </div>
            <div class="info-item">
                <span class="info-label">Program:</span> {{ Auth::user()->studentProfile?->course ?? 'N/A' }}
            </div>
            <div class="info-item">
                <span class="info-label">Department:</span> {{ Auth::user()->studentProfile?->department ?? 'N/A' }}
            </div>
            <div class="info-item">
                <span class="info-label">Company:</span> {{ Auth::user()->studentProfile?->company?->name ?? 'N/A' }}
            </div>
            <div class="info-item">
                <span class="info-label">Week Period:</span> {{ $weekStart->format('M d, Y') }} - {{ $weekEnd->format('M d, Y') }}
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Daily Activities Summary</h2>
        @foreach($reports as $report)
            <div class="daily-report">
                <div class="daily-date">{{ $report->work_date->format('l, M d, Y') }}</div>
                <div class="daily-status">Status: {{ ucfirst($report->status) }}</div>
                <div class="daily-content">{{ $report->summary }}</div>
            </div>
        @endforeach
    </div>

    <div class="section">
        <h2>Weekly Summary</h2>
        <div class="info-item">
            <strong>Total Hours Worked This Week:</strong> {{ number_format($totalHours, 2) }} hours
        </div>
    </div>

    <div class="footer">
        <p><strong>Generated on:</strong> {{ now()->format('M d, Y g:i A') }}</p>
        <p><strong>Report Period:</strong> {{ $weekStart->format('M d, Y') }} - {{ $weekEnd->format('M d, Y') }}</p>
        <p><strong>Total Daily Reports:</strong> {{ $totalDays }}</p>
    </div>
</body>
</html>
