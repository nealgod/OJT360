<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Weekly Report</h2>
            <p class="text-sm text-gray-500">
                {{ $weekStart->format('M d, Y') }} - {{ $weekEnd->format('M d, Y') }}
                @php
                    $days = $weekStart->diffInDays($weekEnd) + 1;
                @endphp
                <span class="text-gray-400">•</span> {{ $days }} {{ $days == 1 ? 'day' : 'days' }}
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="flex-1">
                                <h4 class="font-semibold text-red-900 mb-1">Unable to Submit Report</h4>
                                <ul class="text-sm text-red-800 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('reports.weekly.store') }}" class="space-y-8">
                    @csrf

                    <input type="hidden" name="week_start_date" value="{{ $weekStart->toDateString() }}">
                    <input type="hidden" name="week_end_date" value="{{ $weekEnd->toDateString() }}">

                    <div>
                        <h3 class="text-lg font-semibold text-ojt-dark mb-4">Attendance Summary</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="p-4 border rounded-lg bg-gray-50">
                                <p class="text-sm text-gray-500">Days Present</p>
                                <p class="text-2xl font-bold text-green-600">{{ $attendanceSummary['days_present'] }}</p>
                            </div>
                            <div class="p-4 border rounded-lg bg-gray-50">
                                <p class="text-sm text-gray-500">Days Absent</p>
                                <p class="text-2xl font-bold text-red-500">{{ $attendanceSummary['days_absent'] }}</p>
                            </div>
                            <div class="p-4 border rounded-lg bg-gray-50">
                                <p class="text-sm text-gray-500">Days Late</p>
                                <p class="text-2xl font-bold text-yellow-500">{{ $attendanceSummary['days_late'] }}</p>
                            </div>
                            <div class="p-4 border rounded-lg bg-gray-50">
                                <p class="text-sm text-gray-500">Total Hours</p>
                                <p class="text-2xl font-bold text-blue-600">{{ $attendanceSummary['total_hours'] }}</p>
                            </div>
                        </div>
                    </div>

                    @php
                        $hasAnyAttendance = collect($entries)->contains('has_attendance', true);
                    @endphp

                    @if(!$hasAnyAttendance)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center mb-6">
                            <svg class="w-12 h-12 text-yellow-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-yellow-800 mb-2">No Attendance Records Found</h3>
                            <p class="text-sm text-yellow-700 mb-4">
                                You don't have any attendance records for this period ({{ $weekStart->format('M d, Y') }} - {{ $weekEnd->format('M d, Y') }}).
                            </p>
                            <div class="flex items-center justify-center gap-3">
                                <a href="{{ route('attendance.index') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                                    Go to Attendance
                                </a>
                                <a href="{{ route('reports.weekly.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                    Choose Different Dates
                                </a>
                            </div>
                        </div>
                    @endif
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-lg font-semibold text-ojt-dark">Daily Activities</h3>
                            <p class="text-xs text-gray-500">
                                @php
                                    $daysWithAttendance = collect($entries)->where('has_attendance', true)->count();
                                @endphp
                                {{ $daysWithAttendance }} of {{ count($entries) }} {{ count($entries) == 1 ? 'day' : 'days' }} with attendance
                            </p>
                        </div>

                        <div class="border rounded-lg overflow-hidden">
                            <div class="grid grid-cols-12 bg-gray-50 px-4 py-2 text-sm font-semibold text-gray-600">
                                <div class="col-span-3">Date</div>
                                <div class="col-span-6">Daily Activities / Job Work</div>
                                <div class="col-span-3 text-right pr-4">No. of Hours</div>
                            </div>

                            <div class="divide-y">
                                @foreach ($entries as $index => $entry)
                                    <div class="grid grid-cols-12 items-start gap-3 px-4 py-3 {{ !$entry['has_attendance'] ? 'bg-gray-50' : '' }}">
                                        <div class="col-span-3">
                                            <div class="text-sm font-medium {{ $entry['has_attendance'] ? 'text-gray-700' : 'text-gray-400' }}">
                                                {{ $entry['label'] }}
                                            </div>
                                            @if(!$entry['has_attendance'])
                                                <span class="text-xs text-red-500">No attendance</span>
                                            @endif
                                            <input type="hidden" name="entries[{{ $index }}][date]" value="{{ $entry['date'] }}">
                                            <input type="hidden" name="entries[{{ $index }}][hours]" value="{{ $entry['hours'] }}">
                                        </div>
                                        <div class="col-span-6">
                                            <textarea name="entries[{{ $index }}][activity]" rows="2" maxlength="50"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-ojt-primary focus:ring-ojt-primary {{ !$entry['has_attendance'] ? 'bg-gray-100' : '' }}"
                                                placeholder="{{ $entry['has_attendance'] ? 'Describe your tasks for this day (Max 50 chars)...' : 'No attendance record for this day' }}"
                                                {{ !$entry['has_attendance'] ? 'readonly' : '' }}>{{ old("entries.$index.activity", $entry['activity']) }}</textarea>
                                            @if($entry['has_attendance'])
                                                <p class="text-xs text-gray-500 mt-1 text-right">Max 50 characters</p>
                                            @endif
                                        </div>
                                        <div class="col-span-3 text-right pr-4">
                                            <div class="text-lg font-semibold {{ $entry['has_attendance'] ? 'text-gray-700' : 'text-gray-400' }}">
                                                {{ $entry['hours'] > 0 ? $entry['hours'] : '—' }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            <span class="text-red-500">*</span> Days without attendance are shown but cannot be edited. Only days with attendance records can have activities added.
                        </p>
                        <x-input-error class="mt-2" :messages="$errors->get('entries')" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Problems Encountered (Optional)</label>
                        <textarea name="problems_encountered" rows="4" maxlength="450"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-ojt-primary focus:ring-ojt-primary"
                                  placeholder="Describe any problems or challenges you encountered during the week...">{{ old('problems_encountered') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('problems_encountered')" />
                        <div class="flex justify-between mt-1">
                            <p class="text-xs text-gray-500">This will appear on your PDF report.</p>
                            <p class="text-xs text-gray-500">Max 450 characters</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-ojt-primary text-white rounded-lg shadow hover:bg-maroon-700 transition" {{ !$hasAnyAttendance ? 'disabled' : '' }}>
                            Save as Draft
                        </button>
                        <a href="{{ route('reports.weekly.index') }}" class="text-sm text-gray-600 hover:text-ojt-primary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>



