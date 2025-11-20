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
            <!-- Status & Hours Overview -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div class="flex-1">
                        <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">OJT Status</p>
                        <p class="text-2xl font-bold text-ojt-dark mb-2">
                            {{ ucfirst($profile?->ojt_status ?? 'Pending') }}
                        </p>
                        <p class="text-sm text-gray-600">
                            @if($profile?->ojt_status === 'active')
                                You're cleared to log attendance and submit reports
                            @else
                                Waiting for coordinator activation
                            @endif
                        </p>
                    </div>
                    
                    @php
                        $completedMinutes = Auth::user()->attendanceLogs()->sum('minutes_worked');
                        $completedHours = round(($completedMinutes ?? 0) / 60, 1);
                        $requiredHours = $acceptance?->total_hours
                            ?? $profile?->required_hours
                            ?? Auth::user()->getRequiredHours();
                        $percentage = $requiredHours > 0 ? round(($completedHours / $requiredHours) * 100, 1) : 0;
                    @endphp
                    
                    <div class="flex items-center gap-4">
                        <!-- Hours Completed Card -->
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4 min-w-[140px]">
                            <p class="text-xs uppercase tracking-wide text-green-700 mb-2">Completed</p>
                            <p class="text-3xl font-bold text-green-900">{{ number_format($completedHours, 1) }}</p>
                            <p class="text-xs text-green-600 mt-1">hours logged</p>
                        </div>
                        
                        <!-- Required Hours Card -->
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 min-w-[140px]">
                            <p class="text-xs uppercase tracking-wide text-blue-700 mb-2">Required</p>
                            <p class="text-3xl font-bold text-blue-900">
                                @if($requiredHours)
                                    {{ number_format($requiredHours) }}
                                @else
                                    —
                                @endif
                            </p>
                            <p class="text-xs text-blue-600 mt-1">total hours</p>
                        </div>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                @if($requiredHours > 0)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Overall Progress</span>
                            <span class="text-sm font-bold text-ojt-primary">{{ $percentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-ojt-primary h-2.5 rounded-full transition-all duration-300" 
                                 style="width: {{ min($percentage, 100) }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            {{ number_format(max(0, $requiredHours - $completedHours), 1) }} hours remaining
                        </p>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-ojt-dark">Company</h3>
                        @if($company)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                Assigned
                            </span>

                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                Pending
                            </span>
                        @endif
                    </div>
                    @if($company)
                        <div class="space-y-3">
                            <p class="text-xl font-semibold text-ojt-dark">
                                {{ $company->name }}
                            </p>
                            <div class="space-y-2">
                                <p class="text-sm text-gray-600 flex items-start">
                                    <svg class="w-4 h-4 mr-2 mt-0.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>{{ $company->address ?? 'Address not set' }}</span>
                                </p>
                                @if($acceptance?->start_date)
                                    <p class="text-sm text-gray-600 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Started {{ $acceptance->start_date->format('M d, Y') }}
                                    </p>
                                @endif
                                @if($company?->department)
                                    <p class="text-sm text-gray-600 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        {{ $company->department }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Your coordinator will assign your company after your requirements are complete.</p>
                    @endif
                </div>

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between mb-4">
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
                        <div class="flex items-start space-x-4">
                            @if($supervisor->supervisorProfile?->profile_image)
                                <img src="{{ Storage::url($supervisor->supervisorProfile->profile_image) }}" 
                                     alt="{{ $supervisor->name }}" 
                                     class="w-16 h-16 rounded-full object-cover border-2 border-ojt-primary flex-shrink-0">
                            @else
                                <div class="w-16 h-16 {{ $supervisor->getAvatarColor() }} rounded-full flex items-center justify-center text-white text-xl font-bold flex-shrink-0">
                                    {{ substr($supervisor->name, 0, 1) }}
                                </div>
                            @endif
                            <div class="space-y-2 flex-1">
                                <p class="text-xl font-semibold text-ojt-dark">{{ $supervisor->name }}</p>
                                @if($supervisor->supervisorProfile?->position)
                                    <p class="text-sm text-gray-500">{{ $supervisor->supervisorProfile->position }}</p>
                                @endif
                                <div class="space-y-1">
                                    <p class="text-sm text-gray-600 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        {{ $supervisor->email }}
                                    </p>
                                    @if($supervisor->supervisorProfile?->phone)
                                        <p class="text-sm text-gray-600 flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            {{ $supervisor->supervisorProfile->phone }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Your supervisor will be assigned once they generate your acceptance letter.</p>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-ojt-dark">Work Schedule</h3>
                    @if($acceptance)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                            Set
                        </span>
                    @endif
                </div>
                    @if($acceptance && $acceptance->work_schedule)
                        @php
                            $schedule = $acceptance->work_schedule;
                            $days = [];
                            
                            // Extract working days from schedule
                            foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
                                if (isset($schedule[$day]['enabled']) && $schedule[$day]['enabled']) {
                                    $days[] = ucfirst($day);
                                }
                            }
                            
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-700">
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Shift Hours</p>
                                    <p class="text-base font-medium">{{ isset($schedule['shift_start']) ? $formatTime($schedule['shift_start']) : '—' }} – {{ isset($schedule['shift_end']) ? $formatTime($schedule['shift_end']) : '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Break Time</p>
                                    <p class="text-base font-medium">
                                        @if(isset($schedule['break_minutes']) && $schedule['break_minutes'] > 0)
                                            @php
                                                $breakMinutes = $schedule['break_minutes'];
                                                $hours = floor($breakMinutes / 60);
                                                $mins = $breakMinutes % 60;
                                            @endphp
                                            @if($hours > 0 && $mins > 0)
                                                {{ $hours }} {{ $hours == 1 ? 'hour' : 'hours' }} {{ $mins }} {{ $mins == 1 ? 'minute' : 'minutes' }}
                                            @elseif($hours > 0)
                                                {{ $hours }} {{ $hours == 1 ? 'hour' : 'hours' }}
                                            @else
                                                {{ $mins }} {{ $mins == 1 ? 'minute' : 'minutes' }}
                                            @endif
                                        @else
                                            No break
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Total Hours Required</p>
                                    <p class="text-base font-medium">{{ $acceptance->total_hours ?? 'Not specified' }} hours</p>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Working Days</p>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    @if(!empty($days))
                                        @foreach($days as $day)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-ojt-primary/10 text-ojt-primary">
                                                {{ $day }}
                                            </span>
                                        @endforeach
                                    @else
                                        <p class="text-sm text-gray-500">Not specified</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                @else
                    <p class="text-sm text-gray-500">Schedule details will appear once your supervisor generates your acceptance letter.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

