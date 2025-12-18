<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                Create Monthly Evaluation
            </h2>
            <a href="{{ route('supervisor.students.view', $student) }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium text-sm bg-white shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7" />
                </svg>
                Back to Student
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-ojt-dark mb-4">Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">Student Name</p>
                        <p class="font-medium text-gray-900">{{ $student->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Student ID</p>
                        <p class="font-medium text-gray-900">{{ $student->studentProfile->student_id ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Course/Program</p>
                        <p class="font-medium text-gray-900">{{ $student->studentProfile->course ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Department</p>
                        <p class="font-medium text-gray-900">{{ $student->studentProfile->department ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Host Training Establishment</p>
                        <p class="font-medium text-gray-900">
                            @if($acceptance && $acceptance->company)
                                {{ $acceptance->company->name }}
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600">Company Address</p>
                        <p class="font-medium text-gray-900">
                            @if($acceptance && $acceptance->company)
                                {{ $acceptance->company->address }}
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600">Work Schedule</p>
                        <p class="font-medium text-gray-900">
                            {{ $acceptance->formatted_work_schedule ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600">Supervisor Name</p>
                        <p class="font-medium text-gray-900">
                            @if($acceptance && $acceptance->immediate_supervisor)
                                {{ $acceptance->immediate_supervisor }}
                            @else
                                {{ Auth::user()->name }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600">Report For</p>
                        <p class="font-medium text-gray-900">
                            Monthly Progress Report
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('supervisor.evaluations.store') }}">
                @csrf
                
                <input type="hidden" name="student_user_id" value="{{ $student->id }}">

                <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-ojt-dark mb-4">Evaluation Period</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Month *</label>
                            <select name="evaluation_month" required 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-ojt-primary focus:ring-ojt-primary">
                                <option value="1" {{ old('evaluation_month', $currentMonth) == 1 ? 'selected' : '' }}>January</option>
                                <option value="2" {{ old('evaluation_month', $currentMonth) == 2 ? 'selected' : '' }}>February</option>
                                <option value="3" {{ old('evaluation_month', $currentMonth) == 3 ? 'selected' : '' }}>March</option>
                                <option value="4" {{ old('evaluation_month', $currentMonth) == 4 ? 'selected' : '' }}>April</option>
                                <option value="5" {{ old('evaluation_month', $currentMonth) == 5 ? 'selected' : '' }}>May</option>
                                <option value="6" {{ old('evaluation_month', $currentMonth) == 6 ? 'selected' : '' }}>June</option>
                                <option value="7" {{ old('evaluation_month', $currentMonth) == 7 ? 'selected' : '' }}>July</option>
                                <option value="8" {{ old('evaluation_month', $currentMonth) == 8 ? 'selected' : '' }}>August</option>
                                <option value="9" {{ old('evaluation_month', $currentMonth) == 9 ? 'selected' : '' }}>September</option>
                                <option value="10" {{ old('evaluation_month', $currentMonth) == 10 ? 'selected' : '' }}>October</option>
                                <option value="11" {{ old('evaluation_month', $currentMonth) == 11 ? 'selected' : '' }}>November</option>
                                <option value="12" {{ old('evaluation_month', $currentMonth) == 12 ? 'selected' : '' }}>December</option>
                            </select>
                            @error('evaluation_month')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Year *</label>
                            <select name="evaluation_year" required 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-ojt-primary focus:ring-ojt-primary">
                                @php
                                    $startYear = $internshipStart->year;
                                    $endYear = $internshipEnd->year;
                                @endphp
                                @for($year = $startYear; $year <= $endYear; $year++)
                                    <option value="{{ $year }}" {{ old('evaluation_year', $currentYear) == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                            @error('evaluation_year')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-ojt-dark mb-4">Work Assignment(s)</h3>
                    <textarea name="work_assignment" rows="3" required
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-ojt-primary focus:ring-ojt-primary"
                              placeholder="Describe the student's work assignments and responsibilities this month...">{{ old('work_assignment', $acceptance->job_title ?? '') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Describe the tasks and responsibilities assigned to the student</p>
                </div>

                <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-ojt-dark mb-2">Performance Ratings</h3>
                    <p class="text-sm text-gray-500 mb-4">Tap a score for each item. 1 = Needs Improvement, 5 = Excellent.</p>
                    @php
                        $scoreLabels = [
                            1 => 'Needs Improvement',
                            2 => 'Fair',
                            3 => 'Satisfactory',
                            4 => 'Very Good',
                            5 => 'Excellent',
                        ];
                        $ratingSections = [
                            [
                                'title' => 'RELATED SKILLS',
                                'items' => [
                                    ['name' => 'rating_row_1', 'label' => '1. Analytical Skills'],
                                    ['name' => 'rating_row_2', 'label' => '2. Communicative Competence'],
                                    ['name' => 'rating_row_3', 'label' => '3. Leadership Skills'],
                                    ['name' => 'rating_row_4', 'label' => '4. Time Management Skills'],
                                    ['name' => 'rating_row_5', 'label' => '5. Technical Competence'],
                                ],
                            ],
                            [
                                'title' => 'QUALITY OF WORK',
                                'items' => [
                                    ['name' => 'rating_row_6', 'label' => '6. Accuracy and Dependability'],
                                    ['name' => 'rating_row_7', 'label' => '7. Creativity'],
                                    ['name' => 'rating_row_8', 'label' => '8. Multi-Tasking Ability'],
                                    ['name' => 'rating_row_9', 'label' => '9. Productivity / Work Speed'],
                                    ['name' => 'rating_row_10', 'label' => '10. Professionalism'],
                                ],
                            ],
                            [
                                'title' => 'WORK APPROACH',
                                'items' => [
                                    ['name' => 'rating_row_11', 'label' => '11. Adaptability to Change'],
                                    ['name' => 'rating_row_12', 'label' => '12. Attendance and Punctuality'],
                                    ['name' => 'rating_row_13', 'label' => '13. Courtesy and Respect'],
                                    ['name' => 'rating_row_14', 'label' => '14. Professional Grooming'],
                                    ['name' => 'rating_row_15', 'label' => '15. Teamwork'],
                                ],
                            ],
                            [
                                'title' => 'JOB INTEREST',
                                'items' => [
                                    ['name' => 'rating_row_16', 'label' => '16. Adherence to Policies'],
                                    ['name' => 'rating_row_17', 'label' => '17. Attitude towards Work'],
                                    ['name' => 'rating_row_18', 'label' => '18. Work with Colleagues'],
                                    ['name' => 'rating_row_19', 'label' => '19. Initiative'],
                                    ['name' => 'rating_row_20', 'label' => '20. Participation in Activities'],
                                ],
                            ],
                        ];
                    @endphp

                    <div class="space-y-6">
                        @foreach($ratingSections as $sectionIndex => $section)
                            <div class="border border-gray-200 rounded-lg">
                                <div class="px-4 py-3 bg-gray-50 rounded-t-lg">
                                    <p class="text-sm font-semibold text-gray-700">{{ $section['title'] }}</p>
                                </div>
                                <div class="divide-y divide-gray-100">
                                    @foreach($section['items'] as $item)
                                        <div class="px-4 py-4">
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $item['label'] }}</p>
                                                </div>
                                                <span class="text-xs text-gray-500">Select 1-5</span>
                                            </div>
                                            <div class="mt-3 grid grid-cols-5 gap-2 sm:gap-3">
                                                @foreach($scoreLabels as $score => $label)
                                                    <label class="flex flex-col items-center text-xs font-medium text-gray-600 gap-1">
                                                        <input
                                                            type="radio"
                                                            name="{{ $item['name'] }}"
                                                            value="{{ $score }}"
                                                            class="sr-only peer"
                                                            {{ old($item['name']) == $score ? 'checked' : '' }}
                                                            {{ $score === 1 ? 'required' : '' }}
                                                        >
                                                        <span class="w-full text-center py-2 rounded-lg border border-gray-200 transition peer-checked:bg-ojt-primary peer-checked:text-white peer-checked:border-ojt-primary break-words">
                                                            {{ $score }}
                                                        </span>
                                                        <span class="text-[11px] text-gray-500">{{ $label }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                            @error($item['name'])
                                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @endforeach
                                    
                                    <!-- Category Average (inline after each section) -->
                                    <div class="px-4 py-3 bg-gray-50">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-semibold text-gray-700">{{ strtoupper($section['title']) }} AVERAGE:</span>
                                            <div class="text-right">
                                                <span class="text-lg font-bold text-ojt-primary" 
                                                      id="avg_{{ $sectionIndex }}">0.0 / 5.0</span>
                                                <span class="text-xs text-gray-500 ml-2" 
                                                      id="pct_{{ $sectionIndex }}">(0%)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                        <!-- Overall Average (at the end) -->
                        <div class="border-2 border-ojt-primary rounded-lg bg-ojt-primary/5 p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-base font-semibold text-gray-900">Overall Average:</span>
                                <div class="text-right">
                                    <span class="text-2xl font-bold text-ojt-primary" id="overallAvg">0.0 / 5.0</span>
                                    <span class="text-sm text-gray-600 ml-2" id="overallPct">(0%)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow sm:rounded-lg p-6 mb-6">

                    <h3 class="text-lg font-semibold text-ojt-dark mb-4">Comments and Recommendations</h3>
                    <textarea
                        name="comments_recommendations"
                        id="monthlyComments"
                        rows="4"
                        maxlength="300"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-ojt-primary focus:ring-ojt-primary"
                        placeholder="Share concise feedback or recommendations (optional, max 300 characters)">{{ old('comments_recommendations') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        Optional – Maximum 300 characters.
                        <span id="monthlyCommentsCounter">300 characters remaining</span>
                    </p>
                </div>

                <div class="bg-white shadow sm:rounded-lg p-6">
                    <div class="flex gap-3 justify-end">
                        <a href="{{ route('supervisor.students.view', $student) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" name="action" value="submit" class="px-6 py-2 bg-ojt-primary rounded-lg text-white hover:bg-maroon-700">
                            Submit Evaluation
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Comments counter
            const commentsField = document.getElementById('monthlyComments');
            const commentsCounter = document.getElementById('monthlyCommentsCounter');
            const maxChars = 300;

            if (commentsField && commentsCounter) {
                const updateCounter = () => {
                    const remaining = maxChars - commentsField.value.length;
                    commentsCounter.textContent = `${remaining} characters remaining`;
                    commentsCounter.classList.toggle('text-red-600', remaining <= 20);
                    commentsCounter.classList.toggle('text-gray-500', remaining > 20);
                };

                commentsField.addEventListener('input', updateCounter);
                updateCounter();
            }

            // Rating calculation
            const categories = [
                { start: 1, end: 5, avgId: 'avg_0' },      // Related Skills
                { start: 6, end: 10, avgId: 'avg_1' },     // Quality of Work
                { start: 11, end: 15, avgId: 'avg_2' },    // Work Approach
                { start: 16, end: 20, avgId: 'avg_3' }     // Job Interest
            ];

            function calculateAverages() {
                let totalSum = 0;
                let totalCount = 0;

                // Calculate each category average
                categories.forEach(category => {
                    let sum = 0;
                    let count = 0;

                    for (let i = category.start; i <= category.end; i++) {
                        const selected = document.querySelector(`input[name="rating_row_${i}"]:checked`);
                        if (selected) {
                            sum += parseInt(selected.value);
                            count++;
                        }
                    }

                    const average = count > 0 ? (sum / count) : 0;
                    const percentage = Math.round((average / 5) * 100);
                    
                    // Update category display
                    document.getElementById(category.avgId).textContent = average.toFixed(1) + ' / 5.0';
                    document.getElementById('pct_' + categories.indexOf(category)).textContent = '(' + percentage + '%)';

                    totalSum += sum;
                    totalCount += count;
                });

                // Calculate overall average
                const overallAverage = totalCount > 0 ? (totalSum / totalCount) : 0;
                const overallPercentage = Math.round((overallAverage / 5) * 100);
                
                document.getElementById('overallAvg').textContent = overallAverage.toFixed(1) + ' / 5.0';
                document.getElementById('overallPct').textContent = '(' + overallPercentage + '%)';
            }

            // Add event listeners to all rating inputs
            for (let i = 1; i <= 20; i++) {
                const radios = document.querySelectorAll(`input[name="rating_row_${i}"]`);
                radios.forEach(radio => {
                    radio.addEventListener('change', calculateAverages);
                });
            }

            // Initialize on load
            calculateAverages();
        });
    </script>
</x-app-layout>
