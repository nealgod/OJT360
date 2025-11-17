<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-ojt-dark leading-tight">My Placement</h2>
                <p class="text-sm text-gray-500">Company details, supervisor contacts, and schedule</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Status</p>
                        <p class="text-2xl font-semibold text-ojt-dark">
                            {{ ucfirst($profile?->ojt_status ?? 'Pending') }}
                        </p>
                        <p class="text-sm text-gray-500">
                            @if($profile?->ojt_status === 'active')
                                You're cleared to log attendance and submit reports.
                            @else
                                Waiting for coordinator activation.
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="text-center">
                            <p class="text-xs uppercase tracking-wide text-gray-500">Hours Completed</p>
                            <p class="text-lg font-semibold text-ojt-dark">{{ number_format($profile?->completed_hours ?? 0) }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs uppercase tracking-wide text-gray-500">Required Hours</p>
                            <p class="text-lg font-semibold text-ojt-dark">
                                @if($acceptance?->total_hours)
                                    {{ number_format($acceptance->total_hours) }}
                                @elseif($profile?->required_hours)
                                    {{ number_format($profile->required_hours) }}
                                @else
                                    —
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                @if($acceptance)
                    <div class="rounded-xl bg-gray-50 border border-gray-100 p-4">
                        <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Acceptance Letter</p>
                        <p class="text-sm text-gray-700">
                            Generated {{ $acceptance->created_at?->format('M d, Y') ?? 'recently' }} by {{ $acceptance->immediate_supervisor ?? 'your supervisor' }}.
                        </p>
                        <a href="{{ route('acceptance-letters.download', $acceptance) }}" target="_blank" class="inline-flex items-center text-xs text-ojt-primary font-medium mt-2 hover:text-maroon-700">
                            Download Letter
                        </a>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-ojt-dark">Company</h3>
                        @if($company)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                Assigned
                            </span>
                        @elseif($externalCompanyName)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                External
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                Pending
                            </span>
                        @endif
                    </div>
                    @if($company || $externalCompanyName)
                        <div class="space-y-2">
                            <p class="text-xl font-semibold text-ojt-dark">
                                {{ $company?->name ?? $externalCompanyName }}
                            </p>
                            <p class="text-sm text-gray-600">
                                {{ $company?->address ?? $externalCompanyAddress ?? 'Address not set' }}
                            </p>
                            @if($placement?->start_date)
                                <p class="text-sm text-gray-500">
                                    Effective {{ $placement->start_date->format('M d, Y') }}
                                    @if($placement?->end_date)
                                        – {{ $placement->end_date->format('M d, Y') }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Your coordinator will assign your company after your requirements are complete.</p>
                    @endif
                </div>

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-ojt-dark">Supervisor</h3>
                        @if($supervisor)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                Assigned
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                Pending
                            </span>
                        @endif
                    </div>
                    @if($supervisor)
                        <div class="space-y-2">
                            <p class="text-xl font-semibold text-ojt-dark">{{ $supervisor->name }}</p>
                            <p class="text-sm text-gray-600">{{ $supervisor->email }}</p>
                            @if($supervisor->supervisorProfile?->phone)
                                <p class="text-sm text-gray-600">{{ $supervisor->supervisorProfile->phone }}</p>
                            @endif
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Waiting for your coordinator to assign your supervisor.</p>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-ojt-dark">Work Schedule</h3>
                    @if($placement?->working_days)
                        <span class="text-xs uppercase tracking-wide text-gray-500">Company Provided</span>
                    @endif
                </div>
                    @if($acceptance && $acceptance->work_schedule)
                        @php
                            $schedule = $acceptance->work_schedule;
                            $days = $schedule['days'] ?? ($schedule['working_days'] ?? ['mon','tue','wed','thu','fri']);
                            $weekdayMap = [
                                'mon' => 'Monday',
                                'tue' => 'Tuesday',
                                'wed' => 'Wednesday',
                                'thu' => 'Thursday',
                                'fri' => 'Friday',
                                'sat' => 'Saturday',
                                'sun' => 'Sunday',
                            ];
                            $normalizedDays = array_map(function ($day) use ($weekdayMap) {
                                $key = strtolower($day);
                                return $weekdayMap[$key] ?? ucfirst($day);
                            }, $days);
                            $formatTime = function ($time) {
                                try {
                                    return \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A');
                                } catch (\Exception $e) {
                                    try {
                                        return \Carbon\Carbon::createFromFormat('H:i:s', $time)->format('g:i A');
                                    } catch (\Exception $e) {
                                        return $time;
                                    }
                                }
                            };
                        @endphp
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-700">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Shift</p>
                                <p>{{ isset($schedule['shift_start']) ? $formatTime($schedule['shift_start']) : '—' }} – {{ isset($schedule['shift_end']) ? $formatTime($schedule['shift_end']) : '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Break (mins)</p>
                                <p>{{ $schedule['break_minutes'] ?? 'Not specified' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Working Days</p>
                                <p>
                                    @if(!empty($normalizedDays))
                                        {{ implode(', ', $normalizedDays) }}
                                    @else
                                        Not specified
                                    @endif
                                </p>
                            </div>
                        </div>
                    @elseif($placement)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-700">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Shift</p>
                            <p>{{ $placement->shift_start }} – {{ $placement->shift_end }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Break (mins)</p>
                            <p>{{ $placement->break_minutes ?? 60 }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Working Days</p>
                            <p>
                                @if($placement->working_days)
                                    {{ implode(', ', array_map('ucfirst', $placement->working_days)) }}
                                @else
                                    Not specified
                                @endif
                            </p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500">Schedule details will appear once your placement is approved.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

