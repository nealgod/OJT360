<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Acceptance Letter - EVSU OJT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <a href="{{ route('supervisor.acceptance.index') }}" class="text-ojt-primary hover:text-maroon-700 text-sm font-medium flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Acceptance Letters
                    </a>
                    <img src="{{ asset('images/evsu-logo.png') }}" alt="EVSU" class="h-16" onerror="this.style.display='none'">
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Generate Acceptance Letter</h1>
                    <p class="text-sm text-gray-600 mt-1">Fill in the details to create the official acceptance letter</p>
                </div>
            </div>

            <!-- Student Info & Documents -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h3 class="text-sm font-medium text-blue-900 mb-3">Student Information</h3>
                <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                    <div>
                        <span class="text-blue-700">Name:</span>
                        <span class="font-medium text-blue-900">{{ $acceptanceRequest->student->name }}</span>
                    </div>
                    <div>
                        <span class="text-blue-700">Course:</span>
                        <span class="font-medium text-blue-900">{{ $acceptanceRequest->student->studentProfile->course ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-blue-700">Department:</span>
                        <span class="font-medium text-blue-900">{{ $acceptanceRequest->student->studentProfile->department ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-blue-700">Required Hours:</span>
                        <span class="font-medium text-blue-900">{{ $acceptanceRequest->student->studentProfile->required_hours ?? 486 }} hours</span>
                    </div>
                </div>

                @php
                    $resume = \App\Models\Resume::where('user_id', $acceptanceRequest->student_user_id)->first();
                    
                    // Look for application letter - check multiple possible requirement names
                    $application = \App\Models\StudentDocumentSubmission::where('student_user_id', $acceptanceRequest->student_user_id)
                        ->whereHas('requirement', function($q) {
                            $q->where('name', 'LIKE', '%Application Letter%')
                              ->orWhere('name', 'LIKE', '%Application Letter and PDS/Resume%');
                        })
                        ->first();
                    
                    // If no separate resume in Resume table, check if it's in the combined submission
                    if (!$resume) {
                        $resumeSubmission = \App\Models\StudentDocumentSubmission::where('student_user_id', $acceptanceRequest->student_user_id)
                            ->whereHas('requirement', function($q) {
                                $q->where('name', 'LIKE', '%Resume%')
                                  ->orWhere('name', 'LIKE', '%PDS%')
                                  ->orWhere('name', 'LIKE', '%Application Letter and PDS/Resume%');
                            })
                            ->first();
                    } else {
                        $resumeSubmission = null;
                    }
                @endphp

                <div class="border-t border-blue-200 pt-3 mt-3">
                    <h4 class="text-sm font-medium text-blue-900 mb-2">Student Documents:</h4>
                    @if($resume || $resumeSubmission || $application)
                        <div class="flex flex-wrap gap-2">
                            {{-- Resume from Resume Builder --}}
                            @if($resume)
                                <a href="{{ route('resume.download', $resume) }}"
                                   class="inline-flex items-center px-3 py-1.5 bg-white border border-blue-300 rounded text-xs font-medium text-blue-700 hover:bg-blue-50 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download Resume
                                </a>
                            @elseif($resumeSubmission)
                                <a href="{{ route('documents.download', $resumeSubmission) }}"
                                   class="inline-flex items-center px-3 py-1.5 bg-white border border-blue-300 rounded text-xs font-medium text-blue-700 hover:bg-blue-50 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download Resume/PDS
                                </a>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 bg-gray-100 border border-gray-300 rounded text-xs font-medium text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Resume Not Available
                                </span>
                            @endif
                            
                            {{-- Application Letter --}}
                            @if($application)
                                <a href="{{ route('documents.download', $application) }}"
                                   class="inline-flex items-center px-3 py-1.5 bg-white border border-blue-300 rounded text-xs font-medium text-blue-700 hover:bg-blue-50 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download Application Letter
                                </a>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 bg-gray-100 border border-gray-300 rounded text-xs font-medium text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Application Letter Not Available
                                </span>
                            @endif
                        </div>
                    @else
                        <p class="text-xs text-gray-600 bg-gray-50 border border-gray-200 rounded px-3 py-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Student has not submitted resume or application letter yet. You can still proceed with generating the acceptance letter.
                        </p>
                    @endif
                </div>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('supervisor.acceptance.store', $token) }}" id="acceptanceForm">
                @csrf

                <div class="bg-white shadow-sm rounded-lg p-6 space-y-6">
                    
                    <!-- Job Details -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Job Assignment Details</h3>
                        
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Job Title <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="job_title" value="{{ old('job_title', $acceptanceRequest->position) }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-ojt-primary focus:border-ojt-primary bg-gray-50" 
                                       readonly required>
                                <p class="text-xs text-gray-500 mt-1">Based on student's request</p>
                                @error('job_title')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Branch/Department/Section <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="department" 
                                       value="{{ old('department', $acceptanceRequest->student->studentProfile->department ?? 'Computer Studies Department') }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-ojt-primary focus:border-ojt-primary bg-gray-50" 
                                       readonly required>
                                <p class="text-xs text-gray-500 mt-1">Based on student's program</p>
                                @error('department')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Immediate Supervisor <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="immediate_supervisor" value="{{ old('immediate_supervisor', Auth::user()->name) }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-ojt-primary focus:border-ojt-primary"
                                       placeholder="Lastname, Firstname Middlename" required>
                                @error('immediate_supervisor')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Total Hours Required <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="total_hours" 
                                       value="{{ old('total_hours', $acceptanceRequest->student->studentProfile->required_hours ?? 486) }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-ojt-primary focus:border-ojt-primary bg-gray-50" 
                                       readonly required>
                                <p class="text-xs text-gray-500 mt-1">Based on student's program requirements</p>
                                @error('total_hours')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Schedule -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Work Schedule</h3>
                        
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Effective On <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="effective_date" value="{{ old('effective_date') }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-ojt-primary focus:border-ojt-primary" required>
                                <p class="text-xs text-gray-500 mt-1">When the student will start their OJT</p>
                                @error('effective_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Working Days <span class="text-red-500">*</span>
                            </label>
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                <label class="inline-flex items-center px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-100">
                                    <input type="checkbox" name="work_schedule[{{ strtolower($day) }}][enabled]" value="1" 
                                           class="rounded border-gray-300 text-ojt-primary focus:ring-ojt-primary"
                                           {{ in_array($day, ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm font-medium text-gray-700">{{ $day }}</span>
                                </label>
                                @endforeach
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Time In <span class="text-red-500">*</span>
                                    </label>
                                    <input type="time" name="shift_start" id="shift_start" value="08:00"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-ojt-primary focus:border-ojt-primary" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Time Out <span class="text-red-500">*</span>
                                    </label>
                                    <input type="time" name="shift_end" id="shift_end" value="17:00"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-ojt-primary focus:border-ojt-primary" required>
                                </div>
                            </div>

                            <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="text-sm text-blue-800">
                                    <strong>Hours per day:</strong> <span id="hoursPerDay">8</span> hours
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Signature -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Company Representative Signature</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Signature Type</label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="signature_type" value="typed" checked
                                               class="text-ojt-primary focus:ring-ojt-primary">
                                        <span class="ml-2 text-sm">Typed Name</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="signature_type" value="uploaded"
                                               class="text-ojt-primary focus:ring-ojt-primary">
                                        <span class="ml-2 text-sm">Upload Image</span>
                                    </label>
                                </div>
                            </div>

                            <div id="typedSignature">
                                <p class="text-sm text-gray-600">Your typed name will appear as the signature:</p>
                                <div class="mt-2 p-4 bg-gray-50 border border-gray-200 rounded">
                                    <p class="font-signature text-2xl text-center">{{ Auth::user()->name }}</p>
                                </div>
                            </div>

                            <div id="uploadedSignature" class="hidden">
                                <input type="file" accept="image/*" id="signatureUpload"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-ojt-primary file:text-white hover:file:bg-maroon-700">
                                <input type="hidden" name="signature_data" id="signatureData">
                                <div id="signaturePreview" class="mt-2 hidden">
                                    <img src="" alt="Signature" class="max-h-24 border border-gray-200 rounded">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Additional Notes (Optional)
                        </label>
                        <textarea name="additional_notes" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-ojt-primary focus:border-ojt-primary">{{ old('additional_notes') }}</textarea>
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center justify-between pt-6 border-t">
                        <p class="text-sm text-gray-600">
                            By submitting, you confirm all information is accurate.
                        </p>
                        <button type="submit" 
                                class="px-6 py-2 bg-ojt-primary text-white rounded-md hover:bg-maroon-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ojt-primary">
                            Generate Acceptance Letter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Signature type toggle
        document.querySelectorAll('input[name="signature_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('typedSignature').classList.toggle('hidden', this.value !== 'typed');
                document.getElementById('uploadedSignature').classList.toggle('hidden', this.value !== 'uploaded');
            });
        });

        // Signature upload preview
        document.getElementById('signatureUpload')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('signaturePreview');
                    preview.querySelector('img').src = e.target.result;
                    preview.classList.remove('hidden');
                    document.getElementById('signatureData').value = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Calculate hours per day
        function calculateHours() {
            const startInput = document.getElementById('shift_start');
            const endInput = document.getElementById('shift_end');
            const hoursSpan = document.getElementById('hoursPerDay');
            
            const start = startInput.value.split(':');
            const end = endInput.value.split(':');
            
            const startMinutes = parseInt(start[0]) * 60 + parseInt(start[1]);
            const endMinutes = parseInt(end[0]) * 60 + parseInt(end[1]);
            
            let diffMinutes = endMinutes - startMinutes;
            if (diffMinutes < 0) diffMinutes += 24 * 60; // Handle overnight shifts
            
            const hours = Math.floor(diffMinutes / 60);
            const minutes = diffMinutes % 60;
            
            if (minutes > 0) {
                hoursSpan.textContent = `${hours}.${Math.round(minutes/60*10)}`;
            } else {
                hoursSpan.textContent = hours;
            }
        }

        // Add event listeners
        document.getElementById('shift_start').addEventListener('change', calculateHours);
        document.getElementById('shift_end').addEventListener('change', calculateHours);

        // Initial calculation
        calculateHours();
    </script>
</body>
</html>
