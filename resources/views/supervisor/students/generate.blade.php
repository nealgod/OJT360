<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Generate Acceptance Letter') }}
            </h2>
            <a href="{{ route('supervisor.students.view', $student->id) }}" class="text-ojt-primary hover:text-maroon-700 text-sm font-medium">
                ← Back to Student Profile
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Student Info Summary -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mb-6">
                <div class="flex items-center gap-4">
                    @if($student->studentProfile && $student->studentProfile->profile_image)
                        <img src="{{ Storage::url($student->studentProfile->profile_image) }}" 
                             alt="{{ $student->name }}" 
                             class="w-16 h-16 rounded-full object-cover">
                    @else
                        <div class="w-16 h-16 rounded-full bg-ojt-primary flex items-center justify-center text-white text-xl font-bold">
                            {{ substr($student->name, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $student->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $student->studentProfile->student_id ?? 'N/A' }} • {{ $student->studentProfile->course ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Acceptance Letter Details</h3>

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <ul class="text-sm text-red-600 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-800">{{ session('error') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('supervisor.students.generate', $student->id) }}" class="space-y-6">
                    @csrf

                    <!-- Company Info (Read-only) -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Company Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Company:</span>
                                <span class="font-medium text-gray-900 ml-2">{{ $company->name ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Address:</span>
                                <span class="font-medium text-gray-900 ml-2">{{ $company->address ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Job Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="job_title" class="block text-sm font-medium text-gray-700 mb-2">
                                Job Title / Position *
                            </label>
                            <input type="text" name="job_title" id="job_title" value="{{ old('job_title') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-ojt-primary focus:border-ojt-primary"
                                   placeholder="e.g., Web Developer Intern">
                            @error('job_title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                                Department (Student's Department) *
                            </label>
                            <input type="text" name="department" id="department" 
                                   value="{{ old('department', $student->studentProfile->department ?? '') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 focus:ring-ojt-primary focus:border-ojt-primary"
                                   placeholder="e.g., IT Department">
                            @error('department')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Pre-filled from student's department</p>
                        </div>
                    </div>

                    <div>
                        <label for="immediate_supervisor" class="block text-sm font-medium text-gray-700 mb-2">
                            Immediate Supervisor *
                        </label>
                        <input type="text" name="immediate_supervisor" id="immediate_supervisor" 
                               value="{{ old('immediate_supervisor', $supervisor->name) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-ojt-primary focus:border-ojt-primary"
                               placeholder="Enter supervisor name">
                        @error('immediate_supervisor')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Dates and Hours -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="effective_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Start Date *
                            </label>
                            <input type="date" name="effective_date" id="effective_date" value="{{ old('effective_date') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-ojt-primary focus:border-ojt-primary">
                            @error('effective_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="total_hours" class="block text-sm font-medium text-gray-700 mb-2">
                                Total Required Hours (From Program) *
                            </label>
                            @php
                                // Get required hours from student's program
                                $requiredHours = 486; // Default
                                if($student->studentProfile && $student->studentProfile->required_hours) {
                                    $requiredHours = $student->studentProfile->required_hours;
                                }
                            @endphp
                            <input type="number" name="total_hours" id="total_hours" value="{{ old('total_hours', $requiredHours) }}" required min="1" readonly
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700 cursor-not-allowed">
                            @error('total_hours')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Set by coordinator for {{ $student->studentProfile->course ?? 'program' }}</p>
                        </div>
                    </div>

                    <!-- Work Schedule -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Work Schedule *
                        </label>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @php
                                    $days = [
                                        'monday' => 'Mon',
                                        'tuesday' => 'Tue',
                                        'wednesday' => 'Wed',
                                        'thursday' => 'Thu',
                                        'friday' => 'Fri',
                                        'saturday' => 'Sat',
                                        'sunday' => 'Sun'
                                    ];
                                @endphp
                                @foreach($days as $day => $label)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="work_schedule[{{ $day }}][enabled]" value="1"
                                               {{ in_array($day, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-ojt-primary focus:ring-ojt-primary">
                                        <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-3 border-t border-gray-200">
                                <div>
                                    <label for="shift_start" class="block text-sm text-gray-600 mb-1">Shift Start *</label>
                                    <input type="time" name="shift_start" id="shift_start" value="{{ old('shift_start', '08:00') }}" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-ojt-primary focus:border-ojt-primary">
                                </div>
                                <div>
                                    <label for="shift_end" class="block text-sm text-gray-600 mb-1">Shift End *</label>
                                    <input type="time" name="shift_end" id="shift_end" value="{{ old('shift_end', '17:00') }}" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-ojt-primary focus:border-ojt-primary">
                                </div>
                                <div>
                                    <label for="break_minutes" class="block text-sm text-gray-600 mb-1">Break Time (minutes) *</label>
                                    <input type="number" name="break_minutes" id="break_minutes" value="{{ old('break_minutes', 60) }}" required min="0" max="240"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-ojt-primary focus:border-ojt-primary"
                                           placeholder="60">
                                    <p class="mt-1 text-xs text-gray-500">e.g., 60 for 1 hour</p>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Hours per Day</label>
                                    <div id="hours_per_day" class="w-full px-3 py-2 border border-gray-200 rounded-md bg-gray-50 text-gray-700 font-medium">
                                        9 hours
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Signature (Hidden - automatically uses supervisor's name) -->
                    <input type="hidden" name="signature_type" value="typed">

                    <!-- Additional Notes -->
                    <div>
                        <label for="additional_notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Additional Notes (Optional)
                        </label>
                        <textarea name="additional_notes" id="additional_notes" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-ojt-primary focus:border-ojt-primary"
                                  placeholder="Any additional information...">{{ old('additional_notes') }}</textarea>
                        @error('additional_notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('supervisor.students.view', $student->id) }}" 
                           class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-3 bg-ojt-primary text-white rounded-lg hover:bg-maroon-700 transition-colors font-medium">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Generate Acceptance Letter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const shiftStart = document.getElementById('shift_start');
            const shiftEnd = document.getElementById('shift_end');
            const breakMinutes = document.getElementById('break_minutes');
            const hoursPerDayDisplay = document.getElementById('hours_per_day');

            function calculateHoursPerDay() {
                // Get shift times
                const start = shiftStart.value;
                const end = shiftEnd.value;
                const breakTime = parseInt(breakMinutes.value) || 0;

                if (!start || !end) {
                    hoursPerDayDisplay.textContent = '— hours';
                    return;
                }

                // Calculate total shift minutes
                const [startHour, startMin] = start.split(':').map(Number);
                const [endHour, endMin] = end.split(':').map(Number);
                
                const startMinutes = startHour * 60 + startMin;
                const endMinutes = endHour * 60 + endMin;
                let totalMinutes = endMinutes - startMinutes;
                
                if (totalMinutes <= 0) {
                    hoursPerDayDisplay.textContent = '0 hours';
                    return;
                }
                
                // Subtract break time
                totalMinutes = totalMinutes - breakTime;
                
                if (totalMinutes <= 0) {
                    hoursPerDayDisplay.textContent = '0 hours';
                    return;
                }
                
                // Convert to hours with decimal
                const totalHours = totalMinutes / 60;
                
                // Display with 1 decimal place if needed
                if (totalHours % 1 === 0) {
                    hoursPerDayDisplay.textContent = `${totalHours} hours`;
                } else {
                    hoursPerDayDisplay.textContent = `${totalHours.toFixed(1)} hours`;
                }
            }

            // Add event listeners
            shiftStart.addEventListener('change', calculateHoursPerDay);
            shiftEnd.addEventListener('change', calculateHoursPerDay);
            breakMinutes.addEventListener('input', calculateHoursPerDay);
            breakMinutes.addEventListener('change', calculateHoursPerDay);
            shiftStart.addEventListener('input', calculateHoursPerDay);
            shiftEnd.addEventListener('input', calculateHoursPerDay);

            // Calculate on page load
            calculateHoursPerDay();
        })();
    </script>
</x-app-layout>
