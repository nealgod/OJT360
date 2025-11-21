<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                    Weekly Report Details
                </h2>
                <p class="text-sm text-gray-500">Week {{ $report->week_number }} - {{ $report->student->name }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('reports.weekly.pdf', $report) }}" 
                   class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white rounded-lg shadow hover:bg-maroon-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download PDF
                </a>
                <a href="{{ route('coord.reports.index') }}" class="text-ojt-primary hover:text-maroon-700">
                    ← Back to Reports
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-md bg-green-50 border border-green-100 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Report Header -->
            <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-ojt-dark mb-4">Report Information</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Student</dt>
                                <dd class="text-sm text-ojt-dark font-medium">{{ $report->student->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Student ID</dt>
                                <dd class="text-sm text-ojt-dark">{{ $report->student->studentProfile->student_id ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Week</dt>
                                <dd class="text-sm text-ojt-dark">Week {{ $report->week_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date Range</dt>
                                <dd class="text-sm text-ojt-dark">{{ $report->week_start_date->format('M d') }} - {{ $report->week_end_date->format('M d, Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Supervisor</dt>
                                <dd class="text-sm text-ojt-dark">{{ $report->supervisor->name ?? 'Not Assigned' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                                        @if($report->status === 'reviewed') bg-green-100 text-green-800
                                        @elseif($report->status === 'submitted') bg-blue-100 text-blue-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold text-ojt-dark mb-4">Attendance Summary</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 border rounded-lg bg-gray-50">
                                <p class="text-sm text-gray-500">Days Present</p>
                                <p class="text-2xl font-bold text-green-600">{{ $report->days_present }}</p>
                            </div>
                            <div class="p-4 border rounded-lg bg-gray-50">
                                <p class="text-sm text-gray-500">Days Absent</p>
                                <p class="text-2xl font-bold text-red-600">{{ $report->days_absent }}</p>
                            </div>
                            <div class="p-4 border rounded-lg bg-gray-50">
                                <p class="text-sm text-gray-500">Days Late</p>
                                <p class="text-2xl font-bold text-orange-600">{{ $report->days_late }}</p>
                            </div>
                            <div class="p-4 border rounded-lg bg-gray-50">
                                <p class="text-sm text-gray-500">Total Hours</p>
                                <p class="text-2xl font-bold text-ojt-primary">{{ number_format($report->total_hours, 2) }}</p>
                            </div>
                        </div>
                        <div class="mt-4 text-sm text-gray-600">
                            <p><strong>Submitted:</strong> {{ $report->created_at->format('M d, Y g:i A') }}</p>
                            @if($report->coordinator_reviewed_at)
                                <p><strong>Reviewed:</strong> {{ $report->coordinator_reviewed_at->format('M d, Y g:i A') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Entries -->
            <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-ojt-dark mb-4">Daily Activities</h3>
                @if($report->entries && count($report->entries) > 0)
                    <div class="space-y-4">
                        @foreach($report->entries as $entry)
                            @if(!empty($entry['date']))
                                <div class="border rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-semibold text-ojt-dark">
                                            {{ \Carbon\Carbon::parse($entry['date'])->format('l, M d, Y') }}
                                        </h4>
                                        <span class="text-sm text-gray-600">{{ $entry['hours'] ?? '0' }} hours</span>
                                    </div>
                                    <p class="text-sm text-gray-700">{{ $entry['activity'] ?? 'No activity recorded' }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No daily entries recorded.</p>
                @endif
            </div>

            @if($report->problems_encountered)
            <!-- Problems Encountered -->
            <div class="bg-orange-50 border border-orange-100 shadow sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-ojt-dark mb-4">Problems Encountered</h3>
                <div class="text-sm text-gray-700 whitespace-pre-line">{{ $report->problems_encountered }}</div>
            </div>
            @endif

            @if($report->coordinator_feedback)
            <!-- Coordinator Feedback -->
            <div class="bg-blue-50 border border-blue-100 shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-ojt-dark mb-4">Coordinator Feedback</h3>
                <div class="text-sm text-gray-700 whitespace-pre-line">{{ $report->coordinator_feedback }}</div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
