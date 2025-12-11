<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                    Create Final Evaluation
                </h2>
                <p class="text-sm text-gray-500">{{ $student->name }} - Final OJT Performance Evaluation</p>
            </div>
            <a href="{{ route('supervisor.students.view', $student) }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="mb-4 rounded-md bg-red-50 border-2 border-red-200 px-4 py-3">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-600 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-red-800 mb-2">Please fix the following errors:</h3>
                            <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 rounded-md bg-red-50 border-2 border-red-200 px-4 py-3">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('supervisor.final-evaluations.store') }}" method="POST" id="evaluationForm">
                @csrf
                <input type="hidden" name="student_user_id" value="{{ $student->id }}">

                <!-- Student Information (Read-only) -->
                <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-ojt-dark mb-4">Student Information</h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="font-medium text-gray-500">Student Name</dt>
                            <dd class="text-ojt-dark">{{ $student->name }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Student ID</dt>
                            <dd class="text-ojt-dark">{{ $profile->student_id ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Course</dt>
                            <dd class="text-ojt-dark">{{ $profile->course ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Department</dt>
                            <dd class="text-ojt-dark">{{ $profile->department ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Company</dt>
                            <dd class="text-ojt-dark">{{ $company->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Total Hours Rendered</dt>
                            <dd class="text-ojt-dark">{{ number_format($totalHours, 2) }} hours</dd>
                        </div>
                    </dl>
                </div>

                <!-- Rating Criteria -->
                <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-ojt-dark mb-4">Performance Ratings</h3>
                    <p class="text-sm text-gray-600 mb-6">Rate each criterion based on the student's performance. Enter the percentage value (not exceeding the maximum).</p>

                    @php
                        $criteria = [
                            ['name' => 'rating_quality_thoroughness', 'label' => 'Quality of work', 'description' => 'Thoroughness, Accuracy, Neat, & Effectiveness', 'max' => 20],
                            ['name' => 'rating_dependability', 'label' => 'Dependability, Reliability, and Resourcefulness', 'description' => 'Ability to work with maximum amount of supervision', 'max' => 15],
                            ['name' => 'rating_quality_completion', 'label' => 'Quality of work', 'description' => 'Able to complete work in allotted time', 'max' => 20],
                            ['name' => 'rating_attendance', 'label' => 'Attendance', 'description' => 'Regularity and punctuality in attendance and proper observation of break/meet period', 'max' => 15],
                            ['name' => 'rating_cooperation', 'label' => 'Cooperation', 'description' => 'Works well with everyone, good teamwork', 'max' => 10],
                            ['name' => 'rating_judgement', 'label' => 'Judgement', 'description' => 'Sound decisions, ability to identify and evaluate pertinent factor', 'max' => 10],
                            ['name' => 'rating_personality', 'label' => 'Personality', 'description' => 'Personal grooming and pleasant disposition', 'max' => 5],
                        ];
                    @endphp

                    <div class="space-y-4">
                        @foreach($criteria as $index => $criterion)
                            <div class="border rounded-lg p-4">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <label class="block text-sm font-semibold text-gray-900">
                                            {{ $index + 1 }}. {{ $criterion['label'] }}
                                        </label>
                                        <p class="text-xs text-gray-600 mt-1">{{ $criterion['description'] }}</p>
                                    </div>
                                    <span class="text-sm font-medium text-gray-500 ml-4">Max: {{ $criterion['max'] }}%</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <input type="number" 
                                           name="{{ $criterion['name'] }}" 
                                           id="{{ $criterion['name'] }}"
                                           step="0.01" 
                                           min="0" 
                                           max="{{ $criterion['max'] }}"
                                           class="rating-input w-32 rounded-md border-gray-300 shadow-sm focus:border-ojt-primary focus:ring-ojt-primary"
                                           data-max="{{ $criterion['max'] }}"
                                           required>
                                    <span class="text-sm text-gray-500">%</span>
                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                        <div id="{{ $criterion['name'] }}_bar" class="bg-ojt-primary h-2 rounded-full transition-all" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Total Rating Display -->
                    <div class="mt-6 p-4 bg-ojt-primary/10 border border-ojt-primary/20 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-base font-semibold text-ojt-dark">Total Rating</span>
                            <span id="totalRating" class="text-2xl font-bold text-ojt-primary">0.00%</span>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">Maximum: 95%</p>
                    </div>
                </div>

                <!-- Comments -->
                <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-ojt-dark mb-4">Comments and Recommendations</h3>
                    <textarea name="comments_recommendations" 
                              rows="4" 
                              maxlength="300"
                              id="commentsField"
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-ojt-primary focus:ring-ojt-primary"
                              placeholder="Share concise comments or recommendations (optional, max 300 characters)">{{ old('comments_recommendations') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        Optional – Maximum 300 characters.
                        <span id="commentsCounter">300 characters remaining</span>
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('supervisor.students.view', $student) }}" 
                       class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            id="submitBtn"
                            class="px-6 py-3 bg-ojt-primary text-white rounded-lg hover:bg-maroon-700 transition-colors font-medium">
                        Submit Final Evaluation
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ratingInputs = document.querySelectorAll('.rating-input');
            const totalRatingDisplay = document.getElementById('totalRating');
            const submitBtn = document.getElementById('submitBtn');
            const commentsField = document.getElementById('commentsField');
            const commentsCounter = document.getElementById('commentsCounter');
            const commentsMax = 300;

            function updateCommentsCounter() {
                if (!commentsField || !commentsCounter) {
                    return;
                }
                const remaining = commentsMax - commentsField.value.length;
                commentsCounter.textContent = `${remaining} characters remaining`;
                commentsCounter.classList.toggle('text-red-600', remaining <= 20);
                commentsCounter.classList.toggle('text-gray-500', remaining > 20);
            }

            function updateTotal() {
                let total = 0;
                let allValid = true;
                let hasErrors = false;

                ratingInputs.forEach(input => {
                    const value = parseFloat(input.value) || 0;
                    const max = parseFloat(input.dataset.max);
                    const bar = document.getElementById(input.id + '_bar');
                    const parent = input.closest('.border');
                    
                    // Update progress bar
                    const percentage = (value / max) * 100;
                    bar.style.width = Math.min(percentage, 100) + '%';
                    
                    // Check if exceeds max
                    if (value > max) {
                        input.classList.add('border-red-500', 'border-2');
                        input.classList.remove('border-gray-300');
                        parent.classList.add('border-red-300', 'bg-red-50');
                        bar.classList.remove('bg-ojt-primary');
                        bar.classList.add('bg-red-500');
                        allValid = false;
                        hasErrors = true;
                        
                        // Show error message
                        let errorMsg = input.parentElement.querySelector('.error-message');
                        if (!errorMsg) {
                            errorMsg = document.createElement('span');
                            errorMsg.className = 'error-message text-xs text-red-600 mt-1';
                            input.parentElement.appendChild(errorMsg);
                        }
                        errorMsg.textContent = `Cannot exceed ${max}%`;
                    } else {
                        input.classList.remove('border-red-500', 'border-2');
                        input.classList.add('border-gray-300');
                        parent.classList.remove('border-red-300', 'bg-red-50');
                        bar.classList.add('bg-ojt-primary');
                        bar.classList.remove('bg-red-500');
                        
                        // Remove error message
                        const errorMsg = input.parentElement.querySelector('.error-message');
                        if (errorMsg) {
                            errorMsg.remove();
                        }
                        
                        // Check if empty
                        if (value === 0 || input.value === '') {
                            allValid = false;
                        }
                    }
                    
                    total += value;
                });

                totalRatingDisplay.textContent = (total % 1 === 0 ? total.toFixed(0) : total.toFixed(2)) + '%';
                
                // Change total color if exceeds 95%
                if (total > 95) {
                    totalRatingDisplay.classList.add('text-red-600');
                    totalRatingDisplay.classList.remove('text-ojt-primary');
                } else {
                    totalRatingDisplay.classList.remove('text-red-600');
                    totalRatingDisplay.classList.add('text-ojt-primary');
                }
                
                // Enable/disable submit button
                submitBtn.disabled = !allValid || hasErrors;
                if (!allValid || hasErrors) {
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    submitBtn.title = hasErrors ? 'Please fix the errors before submitting' : 'Please fill all ratings';
                } else {
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    submitBtn.title = '';
                }
            }

            ratingInputs.forEach(input => {
                input.addEventListener('input', updateTotal);
                input.addEventListener('blur', updateTotal);
            });

            // Prevent form submission if invalid
            document.getElementById('evaluationForm').addEventListener('submit', function(e) {
                let hasErrors = false;
                ratingInputs.forEach(input => {
                    const value = parseFloat(input.value) || 0;
                    const max = parseFloat(input.dataset.max);
                    if (value > max) {
                        hasErrors = true;
                    }
                });
                
                if (hasErrors) {
                    e.preventDefault();
                    alert('Please fix the rating errors before submitting. Some ratings exceed their maximum values.');
                    return false;
                }
            });

            // Initial update
            updateTotal();
            updateCommentsCounter();

            if (commentsField) {
                commentsField.addEventListener('input', updateCommentsCounter);
            }
        });
    </script>
</x-app-layout>
