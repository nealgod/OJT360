<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Accept Student') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="text-center mb-8">
                        <div class="w-20 h-20 bg-ojt-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-ojt-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-ojt-dark mb-2">Search for Student</h3>
                        <p class="text-gray-600">Enter the student's Name or ID to view their profile and generate an acceptance letter</p>
                    </div>

                    @if (session('error'))
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm text-red-800">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('supervisor.students.search.post') }}" class="space-y-6" id="searchForm">
                        @csrf

                        <div class="relative">
                            <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Student Name or ID *
                            </label>
                            <input 
                                type="text" 
                                name="student_id" 
                                id="student_id" 
                                value="{{ old('student_id') }}"
                                required
                                autofocus
                                autocomplete="off"
                                placeholder="e.g., Juan Dela Cruz or 2022-31481"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-ojt-primary focus:border-transparent text-lg"
                            >
                            
                            <!-- Autocomplete Dropdown -->
                            <div id="autocomplete-results" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg hidden max-h-80 overflow-y-auto">
                                <!-- Results will be inserted here -->
                            </div>

                            @error('student_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500">
                                Start typing to see suggestions (minimum 2 characters)
                            </p>
                        </div>

                        <div class="flex items-center justify-between pt-4">
                            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-800">
                                ← Back to Dashboard
                            </a>
                            <button 
                                type="submit" 
                                class="bg-ojt-primary text-white px-6 py-3 rounded-lg hover:bg-maroon-700 focus:outline-none focus:ring-2 focus:ring-ojt-primary focus:ring-offset-2 transition-colors font-medium"
                            >
                                Search Student
                            </button>
                        </div>
                    </form>

                    <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-1">How it works:</p>
                                <ol class="list-decimal list-inside space-y-1">
                                    <li>Enter the student's Name or ID number</li>
                                    <li>Review their profile and documents</li>
                                    <li>Click "Accept & Generate Letter"</li>
                                    <li>Fill in the job details</li>
                                    <li>Letter will be generated and sent to the student</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const input = document.getElementById('student_id');
            const resultsDiv = document.getElementById('autocomplete-results');
            const form = document.getElementById('searchForm');
            let debounceTimer;

            input.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const query = this.value.trim();

                if (query.length < 2) {
                    resultsDiv.classList.add('hidden');
                    return;
                }

                debounceTimer = setTimeout(() => {
                    fetch(`{{ route('supervisor.students.autocomplete') }}?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(students => {
                            if (students.length === 0) {
                                resultsDiv.innerHTML = '<div class="p-4 text-sm text-gray-500 text-center">No students found</div>';
                                resultsDiv.classList.remove('hidden');
                                return;
                            }

                            resultsDiv.innerHTML = students.map(student => {
                                const profileImageHtml = student.profile_image 
                                    ? `<img src="${student.profile_image}" alt="${student.name}" class="w-10 h-10 rounded-full object-cover">`
                                    : `<div class="w-10 h-10 rounded-full bg-ojt-primary flex items-center justify-center text-white font-semibold">${student.initials}</div>`;
                                
                                return `
                                    <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 student-result" 
                                         data-student-id="${student.student_id}"
                                         data-user-id="${student.id}">
                                        <div class="flex items-center gap-3">
                                            ${profileImageHtml}
                                            <div class="flex-1">
                                                <div class="font-medium text-gray-900">${student.student_id}</div>
                                                <div class="text-sm text-gray-600">${student.name}</div>
                                                <div class="text-xs text-gray-500">${student.course} • ${student.department}</div>
                                            </div>
                                            ${student.has_supervisor ? 
                                                '<span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full whitespace-nowrap">Has Supervisor</span>' : 
                                                '<span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full whitespace-nowrap">Available</span>'
                                            }
                                        </div>
                                    </div>
                                `;
                            }).join('');

                            resultsDiv.classList.remove('hidden');

                            // Add click handlers to results
                            document.querySelectorAll('.student-result').forEach(result => {
                                result.addEventListener('click', function() {
                                    input.value = this.dataset.studentId;
                                    resultsDiv.classList.add('hidden');
                                    // Auto-submit when clicking a suggestion
                                    form.submit();
                                });
                            });
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                            resultsDiv.innerHTML = '<div class="p-4 text-sm text-red-500 text-center">Error loading results</div>';
                            resultsDiv.classList.remove('hidden');
                        });
                }, 150); // 150ms debounce for faster live search
            });

            // Hide results when clicking outside
            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !resultsDiv.contains(e.target)) {
                    resultsDiv.classList.add('hidden');
                }
            });

            // Show results when focusing on input if there's a query
            input.addEventListener('focus', function() {
                if (this.value.trim().length >= 2) {
                    this.dispatchEvent(new Event('input'));
                }
            });
        })();
    </script>
</x-app-layout>
