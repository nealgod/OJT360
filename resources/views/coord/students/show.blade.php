<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('coord.students.index') }}" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Student Details</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Student Header -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-8">
                <div class="flex items-start space-x-6">
                    <!-- Student Avatar -->
                    <div class="flex-shrink-0">
                        @if($student->getProfile() && $student->getProfile()->profile_image)
                            <img class="h-20 w-20 rounded-full object-cover border-4 border-ojt-primary" 
                                 src="{{ Storage::url($student->getProfile()->profile_image) }}" 
                                 alt="{{ $student->name }}">
                        @else
                            <div class="h-20 w-20 rounded-full {{ $student->getAvatarColor() }} flex items-center justify-center border-4 border-gray-200">
                                <span class="text-white font-bold text-2xl">{{ substr($student->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Student Info -->
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $student->name }}</h1>
                        <p class="text-gray-600">Student ID: {{ $student->studentProfile?->student_id ?? 'N/A' }}</p>
                        <p class="text-gray-600">{{ $student->studentProfile?->course ?? 'N/A' }}</p>
                        <p class="text-gray-600">{{ $student->studentProfile?->department ?? 'N/A' }}</p>
                        
                        @php
                            $status = $student->studentProfile?->ojt_status ?? 'pending';
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'active' => 'bg-green-100 text-green-800',
                                'completed' => 'bg-blue-100 text-blue-800'
                            ];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-800' }} mt-2">
                            {{ ucfirst($status) }}
                        </span>
                    </div>

                    <!-- Quick Actions -->
                    <div class="flex-shrink-0">
                        <div class="flex space-x-3">
                            <form method="POST" action="{{ route('coord.students.update-status', $student) }}" class="inline">
                                @csrf
                                <select name="ojt_status" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary">
                                    <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $milestones = [
                    ['label' => 'Pre-Placement', 'complete' => (bool) $student->studentProfile?->preplacement_complete, 'note' => $student->studentProfile?->preplacement_complete ? 'Checklist done' : 'Waiting submissions'],
                    ['label' => 'Company', 'complete' => (bool) $derivedCompanyName, 'note' => $derivedCompanyName ?? 'Not assigned'],
                    ['label' => 'Supervisor', 'complete' => (bool) $student->studentProfile?->supervisor_id, 'note' => $student->studentProfile?->supervisor?->name ?? 'Not assigned'],
                    ['label' => 'Activation', 'complete' => $student->studentProfile?->ojt_status === 'active', 'note' => ucfirst($student->studentProfile?->ojt_status ?? 'Pending')],
                ];
            @endphp

            <!-- OJT Hours Progress Bar -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
                @php
                    // Calculate progress
                    $totalMinutes = $student->attendanceLogs->sum('minutes_worked');
                    $completedHours = $totalMinutes > 0 ? round($totalMinutes / 60, 1) : 0;
                    $requiredHours = $acceptance?->total_hours
                        ?? $student->studentProfile?->required_hours
                        ?? $student->getRequiredHours()
                        ?? 500; // Default fallback
                    $progressPercentage = $requiredHours > 0 ? min(($completedHours / $requiredHours) * 100, 100) : 0;
                @endphp
                
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-gray-700">OJT Hours Progress</h3>
                    <span class="text-sm font-bold text-ojt-primary">{{ number_format($completedHours, 1) }} / {{ number_format($requiredHours) }} hours</span>
                </div>
                
                <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500 ease-out flex items-center justify-end pr-2
                        @if($progressPercentage >= 100) bg-green-500
                        @elseif($progressPercentage >= 75) bg-blue-500
                        @elseif($progressPercentage >= 50) bg-yellow-500
                        @else bg-orange-500
                        @endif"
                        style="width: {{ $progressPercentage }}%">
                        @if($progressPercentage > 10)
                            <span class="text-xs font-bold text-white">{{ number_format($progressPercentage, 1) }}%</span>
                        @endif
                    </div>
                </div>
                
                <div class="flex items-center justify-between mt-2 text-xs text-gray-500">
                    <span>{{ number_format($requiredHours - $completedHours, 1) }} hours remaining</span>
                    @if($progressPercentage >= 100)
                        <span class="text-green-600 font-semibold">✓ Completed!</span>
                    @elseif($progressPercentage >= 75)
                        <span class="text-blue-600 font-semibold">Almost there!</span>
                    @elseif($progressPercentage >= 50)
                        <span class="text-yellow-600 font-semibold">Halfway done</span>
                    @else
                        <span class="text-gray-600">Keep going!</span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                @foreach($milestones as $milestone)
                    <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">{{ $milestone['label'] }}</p>
                            <p class="text-sm font-semibold text-ojt-dark">
                                {{ $milestone['complete'] ? 'Complete' : 'Pending' }}
                            </p>
                            @if(!empty($milestone['note']))
                                <p class="text-xs text-gray-500 mt-1">{{ $milestone['note'] }}</p>
                            @endif
                        </div>
                        <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $milestone['complete'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            @if($milestone['complete'])
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Attendance Overview -->
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Attendance Overview</h3>
                                <p class="text-sm text-gray-500">Latest logs with photos and punctuality checks.</p>
                                </div>
                            <div class="flex items-center gap-4 text-xs text-gray-600">
                                <div><span class="font-semibold text-ojt-dark">{{ $attendanceStats['total_days'] }}</span> days logged</div>
                                <div><span class="font-semibold text-green-600">{{ $attendanceStats['completed_days'] }}</span> completed</div>
                                <div><span class="font-semibold text-yellow-600">{{ $attendanceStats['missing_checkout'] }}</span> pending out</div>
                            </div>
                        </div>
                        <div class="overflow-x-auto max-h-96 overflow-y-scroll border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Date</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Time In</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Time Out</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Hours</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Photos</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($student->attendanceLogs->take(5) as $log)
                                        @php
                                            $late = false;
                                            // Late detection removed - can be added back using acceptance letter data if needed
                                        @endphp
                                        <tr>
                                            <td class="px-3 py-2 text-gray-900">{{ $log->work_date?->format('M d, Y') ?? '—' }}</td>
                                            <td class="px-3 py-2 text-gray-700">{{ $log->time_in_formatted ?? '—' }}</td>
                                            <td class="px-3 py-2 text-gray-700">{{ $log->time_out_formatted ?? '—' }}</td>
                                            <td class="px-3 py-2">
                                                @if($log->minutes_worked)
                                                    <span class="inline-flex items-center gap-1 text-sm font-semibold text-ojt-primary">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        {{ round($log->minutes_worked / 60, 1) }}h
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 text-sm">—</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2">
                                                <div class="flex items-center gap-2">
                                                    @if($log->photo_in_path)
                                                        <a href="{{ Storage::url($log->photo_in_path) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 rounded-md hover:bg-blue-100 transition-colors">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            </svg>
                                                            In
                                                        </a>
                                                    @endif
                                                    @if($log->photo_out_path)
                                                        <a href="{{ Storage::url($log->photo_out_path) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium bg-green-50 text-green-700 border border-green-200 rounded-md hover:bg-green-100 transition-colors">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            </svg>
                                                            Out
                                                        </a>
                                                    @endif
                                                    @if(!$log->photo_in_path && !$log->photo_out_path)
                                                        <span class="text-xs text-gray-400">No photos</span>
                                                    @endif
                                            </div>
                                            </td>
                                            <td class="px-3 py-2">
                                                @if(!$log->time_in)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Missing Time-In</span>
                                                @elseif(!$log->time_out)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Needs Time-Out</span>
                                                @elseif($late)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">Late</span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">On Time</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-3 py-4 text-center text-sm text-gray-500">No attendance logs yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                                    </div>
                                </div>

                    <!-- Reports Overview -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Reports Overview</h3>
                                <p class="text-sm text-gray-500">Weekly and monthly submissions</p>
                            </div>
                            <div class="flex items-center gap-4 text-xs text-gray-600">
                                <div><span class="font-semibold text-blue-600">{{ $student->weeklyReports->count() }}</span> weekly</div>
                                <div><span class="font-semibold text-purple-600">{{ $student->monthlyEvaluations->count() }}</span> monthly</div>
                            </div>
                        </div>
                        
                        <!-- Weekly Reports -->
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Weekly Reports</h4>
                            <div class="overflow-x-auto max-h-64 overflow-y-scroll border border-gray-200 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Week</th>
                                            <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Period</th>
                                            <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Status</th>
                                            <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @forelse($student->weeklyReports->take(3) as $report)
                                            <tr>
                                                <td class="px-3 py-2 text-gray-900">Week {{ $report->week_number }}</td>
                                                <td class="px-3 py-2 text-gray-700">{{ $report->week_start_date?->format('M d') ?? '—' }} - {{ $report->week_end_date?->format('M d, Y') ?? '—' }}</td>
                                                <td class="px-3 py-2">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ ucfirst($report->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-3 py-2">
                                                    <a href="{{ route('coord.reports.show', $report) }}" target="_blank" class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-xs font-medium">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-3 py-4 text-center text-sm text-gray-500">No weekly reports yet</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Monthly Evaluations -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Monthly Evaluations</h4>
                            <div class="overflow-x-auto max-h-64 overflow-y-scroll border border-gray-200 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Month</th>
                                            <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Supervisor</th>
                                            <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Status</th>
                                            <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @forelse($student->monthlyEvaluations->take(3) as $evaluation)
                                            <tr>
                                                <td class="px-3 py-2 text-gray-900">{{ $evaluation->getMonthYearLabel() }}</td>
                                                <td class="px-3 py-2 text-gray-700">{{ $evaluation->supervisor_name }}</td>
                                                <td class="px-3 py-2">
                                                    <x-evaluation-status-badge :evaluation="$evaluation" />
                                                </td>
                                                <td class="px-3 py-2">
                                                    <a href="{{ route('coordinator.evaluations.show', $evaluation) }}" target="_blank" class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-xs font-medium">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-3 py-4 text-center text-sm text-gray-500">No monthly evaluations yet</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Company & Supervisor Summary -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Placement Summary</h3>
                            @if($companySource)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    {{ ucfirst($companySource) }}
                                </span>
                            @endif
                        </div>
                        <div class="space-y-4 text-sm text-gray-700">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-ojt-primary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Company</p>
                                    <p class="font-medium text-ojt-dark">
                                        {{ $derivedCompanyName ?? 'Not assigned' }}
                                    </p>
                                    @if($derivedCompanyAddress)
                                        <div class="flex items-start gap-1.5 mt-1">
                                            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <p class="text-xs text-gray-500">{{ $derivedCompanyAddress }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-3 text-xs text-gray-500 border-t border-gray-100 pt-4">
                                <div class="flex items-center justify-between">
                                    <span class="uppercase tracking-wide">Hours Completed</span>
                                    <span class="text-sm text-ojt-dark font-semibold">
                                        @php
                                            // Calculate total hours from attendance logs
                                            $totalMinutes = $student->attendanceLogs->sum('minutes_worked');
                                            $completedHours = $totalMinutes > 0 ? round($totalMinutes / 60, 1) : 0;
                                        @endphp
                                        {{ number_format($completedHours, 1) }}
                                    </span>
                                </div>
                                @php
                                    $requiredHours = $acceptance?->total_hours
                                        ?? $student->studentProfile?->required_hours
                                        ?? $student->getRequiredHours();
                                @endphp
                                <div class="flex items-center justify-between">
                                    <span class="uppercase tracking-wide">Required Hours</span>
                                    <span class="text-sm text-ojt-dark font-semibold">
                                        @if($requiredHours)
                                            {{ number_format($requiredHours) }}
                                        @else
                                            —
                                        @endif
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="uppercase tracking-wide">Activation</span>
                                    <span class="text-sm text-ojt-dark font-semibold">
                                        {{ ucfirst($student->studentProfile?->ojt_status ?? 'pending') }}
                                    </span>
                                </div>
                            </div>
                            @if($student->studentProfile?->supervisor)
                                <div class="border-t border-gray-100 pt-4">
                                    <p class="text-xs uppercase tracking-wide text-gray-500">Supervisor</p>
                                    <p class="mt-1 font-medium text-ojt-dark">{{ $student->studentProfile->supervisor->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $student->studentProfile->supervisor->email }}</p>
                                @else
                                    <div class="border-t border-gray-100 pt-4">
                                        <p class="text-xs uppercase tracking-wide text-gray-500">Supervisor</p>
                                        <p class="mt-1 text-gray-400">Not assigned</p>
                                    </div>
                            @endif
                            </div>
                        </div>
                    </div>


                    <!-- Supervisor Assignment Section -->
                    <div id="supervisor-assignment" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Supervisor Assignment</h3>
                            @if($student->studentProfile?->supervisor)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">✓ Assigned</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">⚠ Pending</span>
                            @endif
                        </div>

                        <!-- Current assignment -->
                        <div class="mb-4">
                            @if($student->studentProfile?->supervisor)
                                <div class="bg-gradient-to-r from-ojt-accent/10 to-ojt-primary/5 border border-ojt-accent/30 rounded-lg p-4">
                                    <div class="flex items-start gap-4">
                                        <!-- Supervisor Avatar -->
                                        <div class="flex-shrink-0">
                                            @if($student->studentProfile->supervisor->supervisorProfile?->profile_image)
                                                <img src="{{ Storage::url($student->studentProfile->supervisor->supervisorProfile->profile_image) }}" 
                                                     alt="{{ $student->studentProfile->supervisor->name }}" 
                                                     class="w-16 h-16 rounded-full object-cover border-2 border-ojt-accent shadow-sm">
                                            @else
                                                <div class="w-16 h-16 {{ $student->studentProfile->supervisor->getAvatarColor() }} rounded-full flex items-center justify-center text-white text-xl font-bold shadow-sm">
                                                    {{ substr($student->studentProfile->supervisor->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Supervisor Info -->
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                                    Assigned
                                                </span>
                                            </div>
                                            <h4 class="text-base font-semibold text-ojt-dark mb-1">{{ $student->studentProfile->supervisor->name }}</h4>
                                            
                                            @if($student->studentProfile->supervisor->supervisorProfile?->position)
                                                <p class="text-sm text-gray-600 mb-2">{{ $student->studentProfile->supervisor->supervisorProfile->position }}</p>
                                            @endif
                                            
                                            <div class="space-y-1.5">
                                                <!-- Email -->
                                                <div class="flex items-center text-sm text-gray-700">
                                                    <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                    </svg>
                                                    <a href="mailto:{{ $student->studentProfile->supervisor->email }}" class="hover:text-ojt-primary">
                                                        {{ $student->studentProfile->supervisor->email }}
                                                    </a>
                                                </div>
                                                
                                                <!-- Phone -->
                                                @if($student->studentProfile->supervisor->supervisorProfile?->phone)
                                                    <div class="flex items-center text-sm text-gray-700">
                                                        <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                        </svg>
                                                        <a href="tel:{{ $student->studentProfile->supervisor->supervisorProfile->phone }}" class="hover:text-ojt-primary">
                                                            {{ $student->studentProfile->supervisor->supervisorProfile->phone }}
                                                        </a>
                                                    </div>
                                                @endif
                                                
                                                <!-- Company -->
                                                @if($student->studentProfile->supervisor->supervisorProfile?->company)
                                                    <div class="flex items-start text-sm text-gray-700 mt-2 pt-2 border-t border-ojt-accent/20">
                                                        <svg class="w-4 h-4 mr-2 mt-0.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                        </svg>
                                                        <div>
                                                            <p class="font-medium">{{ $student->studentProfile->supervisor->supervisorProfile->company->name }}</p>
                                                            @if($student->studentProfile->supervisor->supervisorProfile->company->address)
                                                                <p class="text-xs text-gray-500 mt-0.5">{{ $student->studentProfile->supervisor->supervisorProfile->company->address }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <div class="flex items-start gap-3">
                                        <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-yellow-800">No supervisor assigned yet</p>
                                            <p class="text-xs text-yellow-700 mt-1">Student can submit supervisor details, or you can assign an existing supervisor below.</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        <!-- Assignment Options -->

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
