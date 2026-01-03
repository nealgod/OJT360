<x-app-layout>
    @php
        $pendingLogsCount = \App\Models\AttendanceLog::where('student_user_id', $student->id)
            ->where('is_recovered', true)
            ->whereNull('recovery_approved')
            ->count();
    @endphp

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Student Profile') }}
            </h2>
            <a href="{{ route('supervisor.students') }}" class="text-ojt-primary hover:text-maroon-700 text-sm font-medium">
                ← Back to My Students
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
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

            <!-- Student Profile Card -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 mb-6">
                <div class="flex items-start gap-6">
                    <!-- Profile Image -->
                    <div class="flex-shrink-0">
                        @if($student->studentProfile && $student->studentProfile->profile_image)
                            <img src="{{ Storage::url($student->studentProfile->profile_image) }}" 
                                 alt="{{ $student->name }}" 
                                 class="w-24 h-24 rounded-full object-cover border-4 border-ojt-primary">
                        @else
                            <div class="w-24 h-24 rounded-full {{ $student->getAvatarColor() }} flex items-center justify-center text-white text-3xl font-bold border-4 border-gray-200">
                                {{ substr($student->name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <!-- Student Info -->
                    <div class="flex-1">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $student->name }}</h3>
                                <p class="text-lg text-gray-600">{{ $student->studentProfile->student_id ?? 'N/A' }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                @if($student->studentProfile && $student->studentProfile->supervisor_id)
                                    @if((int)$student->studentProfile->supervisor_id === (int)Auth::id())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        Your Trainee
                                    </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                            Has Supervisor
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        Available
                                    </span>
                                @endif
                                
                                {{-- OJT Status Badge --}}
                                @if($student->studentProfile?->ojt_status === 'completed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Completed
                                    </span>
                                @endif
                                
                                <!-- Pending Attendance Button -->
                                @if($pendingLogsCount > 0)
                                    <button onclick="document.getElementById('recoveryReviewModal').classList.remove('hidden')" 
                                            class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded-md hover:bg-red-700 transition-colors shadow-sm animate-pulse">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Review Pending Logs ({{ $pendingLogsCount }})
                                    </button>
                                @else
                                    <button onclick="document.getElementById('attendanceModal').classList.remove('hidden')" 
                                            class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-md hover:bg-gray-200 transition-colors border border-gray-300">
                                        View Attendance History
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="text-sm font-medium text-gray-900">{{ $student->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Course</p>
                                <p class="text-sm font-medium text-gray-900">{{ $student->studentProfile->course ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Department</p>
                                <p class="text-sm font-medium text-gray-900">{{ $student->studentProfile->department ?? 'N/A' }}</p>
                            </div>
                            @if($student->studentProfile && $student->studentProfile->phone)
                            <div>
                                <p class="text-sm text-gray-500">Phone</p>
                                <p class="text-sm font-medium text-gray-900">{{ $student->studentProfile->phone }}</p>
                            </div>
                            @endif
                            @if($student->studentProfile && $student->studentProfile->address)
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-500">Address</p>
                                <p class="text-sm font-medium text-gray-900">{{ $student->studentProfile->address }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Section -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Required Documents for Review</h3>
                
                @php
                    $applicationLetter = $student->documentSubmissions->filter(function($sub) {
                        return $sub->requirement && str_contains(strtolower($sub->requirement->name), 'application');
                    })->first();
                    
                    $resume = $student->documentSubmissions->filter(function($sub) {
                        return $sub->requirement && (
                            str_contains(strtolower($sub->requirement->name), 'resume') ||
                            str_contains(strtolower($sub->requirement->name), 'pds') ||
                            str_contains(strtolower($sub->requirement->name), 'personal data')
                        );
                    })->first();
                    
                    $recommendation = $student->documentSubmissions->filter(function($sub) {
                        return $sub->requirement && str_contains(strtolower($sub->requirement->name), 'recommendation');
                    })->first();
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Application Letter -->
                    <div class="border border-gray-200 rounded-lg p-4 {{ $applicationLetter ? 'bg-white' : 'bg-gray-50' }}">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900 text-sm flex items-center">
                                    Application Letter
                                    <span class="ml-2 text-red-500">*</span>
                                </h4>
                            </div>
                            @if($applicationLetter)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Submitted
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Missing
                                </span>
                            @endif
                        </div>
                        @if($applicationLetter)
                            <p class="text-xs text-gray-500 mb-3 break-all">{{ $applicationLetter->original_filename }}</p>
                            <a href="{{ route('documents.stream', $applicationLetter) }}" target="_blank"
                               class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-ojt-primary rounded-md hover:bg-maroon-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                View Document
                            </a>
                        @else
                            <p class="text-xs text-gray-500">Student hasn't submitted this document yet</p>
                        @endif
                    </div>

                    <!-- Resume/PDS -->
                    <div class="border border-gray-200 rounded-lg p-4 {{ $resume ? 'bg-white' : 'bg-gray-50' }}">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900 text-sm flex items-center">
                                    Resume / PDS
                                    <span class="ml-2 text-red-500">*</span>
                                </h4>
                            </div>
                            @if($resume)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Submitted
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Missing
                                </span>
                            @endif
                        </div>
                        @if($resume)
                            <p class="text-xs text-gray-500 mb-3 break-all">{{ $resume->original_filename }}</p>
                            <a href="{{ route('documents.stream', $resume) }}" target="_blank"
                               class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-ojt-primary rounded-md hover:bg-maroon-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                View Document
                            </a>
                        @else
                            <p class="text-xs text-gray-500">Student hasn't submitted this document yet</p>
                        @endif
                    </div>

                    <!-- Recommendation Letter (Optional) -->
                    <div class="border border-gray-200 rounded-lg p-4 {{ $recommendation ? 'bg-white' : 'bg-gray-50' }}">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900 text-sm flex items-center">
                                    Recommendation Letter
                                    <span class="ml-2 text-gray-400 text-xs">(Optional)</span>
                                </h4>
                            </div>
                            @if($recommendation)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Submitted
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    Not Submitted
                                </span>
                            @endif
                        </div>
                        @if($recommendation)
                            <p class="text-xs text-gray-500 mb-3 break-all">{{ $recommendation->original_filename }}</p>
                            <a href="{{ route('documents.stream', $recommendation) }}" target="_blank"
                               class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-ojt-primary rounded-md hover:bg-maroon-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                View Document
                            </a>
                        @else
                            <p class="text-xs text-gray-500">This document is optional</p>
                        @endif
                    </div>
                </div>

                @if(!$applicationLetter || !$resume)
                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="text-sm text-yellow-800">
                                <strong>Note:</strong> Student is missing required documents. You can still proceed, but it's recommended to review their application materials first.
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            @php
                $isSupervised = $student->studentProfile && (int)$student->studentProfile->supervisor_id === (int)Auth::id();
            @endphp

            @if($isSupervised)
                <!-- Final Evaluation Section -->
                @php
                    $finalEvaluation = \App\Models\FinalEvaluation::where('student_user_id', $student->id)->first();
                    $acceptance = \App\Models\AcceptanceLetter::where('student_user_id', $student->id)->first();
                    $canCreateFinal = !$finalEvaluation && $acceptance;
                @endphp
                
                @if($canCreateFinal || $finalEvaluation)
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Final Evaluation</h3>
                                <p class="text-sm text-gray-500">One-time final OJT performance evaluation</p>
                            </div>
                            @if($canCreateFinal)
                                <a href="{{ route('supervisor.final-evaluations.create', $student->id) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Create Final Evaluation
                                </a>
                            @endif
                        </div>
                        
                        @if($finalEvaluation)
                            <div class="border rounded-lg p-4 bg-gray-50">
                                <div class="flex flex-col gap-3 md:gap-4 md:flex-row md:items-center md:justify-between">
                                    <div>
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="font-medium text-gray-900">Control No: {{ $finalEvaluation->control_number }}</span>
                                            <x-final-evaluation-status-badge :evaluation="$finalEvaluation" />
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            <span>Total Rating: <strong>{{ number_format($finalEvaluation->total_rating, 2) }}%</strong></span>
                                            <span class="mx-2">•</span>
                                            <span>Submitted: {{ $finalEvaluation->submitted_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                                        <a href="{{ route('supervisor.final-evaluations.show', $finalEvaluation) }}"
                                           class="inline-flex justify-center items-center px-3 py-2 bg-ojt-primary text-white text-sm rounded-lg hover:bg-maroon-700 transition-colors">
                                            View
                                        </a>
                                        <a href="{{ route('supervisor.final-evaluations.pdf', $finalEvaluation) }}"
                                           class="inline-flex justify-center items-center px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                                            Download PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-6 text-gray-500 text-sm">
                                <p>Final evaluation can be created when the student completes their OJT period.</p>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Monthly Progress Evaluations</h3>
                        <a href="{{ route('supervisor.evaluations.create', $student->id) }}" 
                           class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white text-sm font-medium rounded-lg hover:bg-maroon-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create Evaluation
                        </a>
                    </div>

                    @php
                        $evaluations = $student->monthlyEvaluations()
                            ->where('supervisor_user_id', Auth::id())
                            ->orderByDesc('evaluation_year')
                            ->orderByDesc('evaluation_month')
                            ->get();
                        
                        $totalEvals = $evaluations->count();
                        $pendingReview = $evaluations->whereNull('reviewed_at')->count();
                        $reviewed = $evaluations->whereNotNull('reviewed_at')->count();
                    @endphp

                    @if($totalEvals > 0)
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="text-center p-3 rounded-lg bg-blue-50 border border-blue-200">
                                <p class="text-xs font-medium text-blue-800 mb-1">Total</p>
                                <p class="text-2xl font-bold text-blue-900">{{ $totalEvals }}</p>
                            </div>
                            <div class="text-center p-3 rounded-lg bg-yellow-50 border border-yellow-200">
                                <p class="text-xs font-medium text-yellow-800 mb-1">Pending</p>
                                <p class="text-2xl font-bold text-yellow-900">{{ $pendingReview }}</p>
                            </div>
                            <div class="text-center p-3 rounded-lg bg-green-50 border border-green-200">
                                <p class="text-xs font-medium text-green-800 mb-1">Reviewed</p>
                                <p class="text-2xl font-bold text-green-900">{{ $reviewed }}</p>
                            </div>
                        </div>
                    @endif

                    @if($evaluations->count() > 0)
                        <div class="max-h-96 overflow-y-auto space-y-3 pr-2">
                            @foreach($evaluations as $evaluation)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-ojt-primary transition-colors">
                                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <h4 class="font-medium text-gray-900">{{ $evaluation->getMonthYearLabel() }}</h4>
                                                <span class="text-xs px-2 py-1 rounded-full font-medium {{ $evaluation->reviewed_at ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                    {{ $evaluation->reviewed_at ? 'Reviewed' : 'Pending Review' }}
                                                </span>
                                                <span class="text-xs text-gray-500">Month {{ $evaluation->month_number }}</span>
                                            </div>
                                            @if($evaluation->submitted_at)
                                                <p class="text-xs text-gray-600">
                                                    Submitted {{ $evaluation->submitted_at->diffForHumans() }} 
                                                    ({{ $evaluation->submitted_at->format('M d, Y g:i A') }})
                                                </p>
                                            @else
                                                <p class="text-xs text-gray-600">Draft - Not yet submitted</p>
                                            @endif
                                        </div>
                                        <div class="flex flex-col sm:flex-row w-full md:w-auto gap-2">
                                        <a href="{{ route('supervisor.evaluations.show', $evaluation) }}" 
                                           class="inline-flex justify-center items-center px-3 py-2 text-sm font-medium text-ojt-primary border border-ojt-primary rounded-lg hover:bg-ojt-primary hover:text-white transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View
                                        </a>
                                        <a href="{{ route('supervisor.evaluations.pdf', $evaluation) }}" 
                                           class="inline-flex justify-center items-center px-3 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Download PDF
                                        </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-gray-500 mb-3">No monthly evaluations yet</p>
                            <a href="{{ route('supervisor.evaluations.create', $student->id) }}" 
                               class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white text-sm font-medium rounded-lg hover:bg-maroon-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create First Evaluation
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex items-center justify-center gap-4">
                @if(!$isSupervised)
                    <!-- Show Accept button if not yet supervised -->
                    <a href="{{ route('supervisor.students.accept', $student->id) }}" 
                       class="inline-flex items-center px-6 py-3 bg-ojt-primary text-white rounded-lg hover:bg-maroon-700 transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Accept & Generate Letter
                    </a>
                @endif
                
                <a href="{{ $isSupervised ? route('supervisor.students') : route('supervisor.students.search') }}" 
                   class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    {{ $isSupervised ? 'Back to My Students' : 'Back to Search' }}
                </a>
            </div>
        </div>
    </div>

    @php
        $allLogs = \App\Models\AttendanceLog::where('student_user_id', $student->id)
            ->orderBy('work_date', 'desc')
            ->get();
            
        $pendingLogs = $allLogs->filter(function($log) {
            return $log->is_recovered && is_null($log->recovery_approved);
        });
    @endphp

    <!-- Recovery Review Modal (PENDING only) -->
    <div id="recoveryReviewModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-start justify-center min-h-screen p-4 pt-10">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('recoveryReviewModal').classList.add('hidden')"></div>

            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col">
                <div class="bg-red-800 px-4 pt-5 pb-4 sm:p-6 border-b border-red-700 rounded-t-lg">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg leading-6 font-bold text-white flex items-center" id="modal-title">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Review Pending Recoveries
                        </h3>
                        <button onclick="document.getElementById('recoveryReviewModal').classList.add('hidden')" class="text-white/80 hover:text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto px-4 sm:px-6 py-4 space-y-4 bg-gray-50">
                    @if($pendingLogs->count() > 0)
                        @foreach($pendingLogs as $log)
                            <div id="review-card-{{ $log->id }}" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="p-5">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <span class="inline-block px-2 py-1 mb-1 text-xs font-bold tracking-wide uppercase text-red-600 bg-red-50 rounded-sm">Shift Date</span>
                                            <h4 class="text-lg font-bold text-gray-900">{{ $log->work_date->format('M d, Y') }} <span class="text-gray-400 font-normal">({{ $log->work_date->format('l') }})</span></h4>
                                        </div>
                                        <div class="text-right">
                                            <span class="block text-[10px] uppercase font-bold text-gray-400 tracking-wider mb-0.5">Hours Claimed</span>
                                            <div class="text-2xl font-bold text-ojt-primary leading-none">{{ $log->hours_worked_formatted }}<span class="text-sm text-gray-500 font-medium ml-1">hrs</span></div>
                                            @if($log->overtime_minutes > 0)
                                                <span class="inline-block mt-1 text-xs font-bold text-green-600 bg-green-50 px-1.5 py-0.5 rounded border border-green-100">+{{ number_format($log->overtime_minutes/60, 2) }}h OT</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <p class="text-[11px] font-bold text-gray-500 uppercase mb-3 tracking-widest flex items-center">
                                            <svg class="w-3 h-3 mr-1 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/></svg>
                                            Timeline Comparison
                                        </p>
                                        
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                            @php
                                                $isModalWholeDay = $log->pm_out_time && $log->is_recovered && ($log->pm_in_photo === $log->am_out_photo);
                                            @endphp

                                            <!-- AM IN -->
                                            <div class="p-2 rounded border bg-green-50 border-green-100">
                                                <span class="block text-[9px] font-bold text-green-600 uppercase tracking-tight">AM In (Regular)</span>
                                                <span class="block text-green-900 font-bold text-sm">{{ $log->am_in_time ? \Carbon\Carbon::parse($log->am_in_time)->format('g:i A') : '—' }}</span>
                                            </div>

                                            <!-- AM OUT -->
                                            @php $isModalAmRec = $log->is_recovered && $log->am_out_time && ($isModalWholeDay || !$log->pm_in_time); @endphp
                                            <div class="p-2 rounded border {{ $isModalAmRec ? 'bg-blue-50 border-blue-200 ring-1 ring-blue-500/20' : 'bg-green-50 border-green-100' }}">
                                                <span class="block text-[9px] font-bold {{ $isModalAmRec ? 'text-blue-600' : 'text-green-600' }} uppercase tracking-tight">
                                                    AM Out {{ $isModalAmRec ? '(Recovery)' : '(Regular)' }}
                                                </span>
                                                <span class="block {{ $isModalAmRec ? 'text-blue-900' : 'text-green-900' }} font-bold text-sm">
                                                    {{ $log->am_out_time ? \Carbon\Carbon::parse($log->am_out_time)->format('g:i A') : '—' }}
                                                </span>
                                            </div>

                                            <!-- PM IN -->
                                            @php $isModalPmInRec = $log->is_recovered && $isModalWholeDay; @endphp
                                            <div class="p-2 rounded border {{ $isModalPmInRec ? 'bg-blue-50 border-blue-200 ring-1 ring-blue-500/20' : ($log->pm_in_time ? 'bg-green-50 border-green-100' : 'bg-gray-50 border-gray-100') }}">
                                                <span class="block text-[9px] font-bold {{ $isModalPmInRec ? 'text-blue-600' : ($log->pm_in_time ? 'text-green-600' : 'text-gray-400') }} uppercase tracking-tight">
                                                    PM In {{ $isModalPmInRec ? '(Recovery)' : ($log->pm_in_time ? '(Regular)' : '') }}
                                                </span>
                                                <span class="block {{ $isModalPmInRec ? 'text-blue-900' : ($log->pm_in_time ? 'text-green-900' : 'text-gray-400') }} font-bold text-sm">
                                                    {{ $log->pm_in_time ? \Carbon\Carbon::parse($log->pm_in_time)->format('g:i A') : '—' }}
                                                </span>
                                            </div>

                                            <!-- PM OUT -->
                                            @php $isModalPmOutRec = $log->is_recovered && $log->pm_out_time; @endphp
                                            <div class="p-2 rounded border {{ $isModalPmOutRec ? 'bg-blue-50 border-blue-200 ring-1 ring-blue-500/20' : ($log->pm_out_time ? 'bg-green-50 border-green-100' : 'bg-gray-50 border-gray-100') }}">
                                                <span class="block text-[9px] font-bold {{ $isModalPmOutRec ? 'text-blue-600' : ($log->pm_out_time ? 'text-green-600' : 'text-gray-400') }} uppercase tracking-tight">
                                                    PM Out {{ $isModalPmOutRec ? '(Recovery)' : ($log->pm_out_time ? '(Regular)' : '') }}
                                                </span>
                                                <span class="block {{ $isModalPmOutRec ? 'text-blue-900' : ($log->pm_out_time ? 'text-green-900' : 'text-gray-400') }} font-bold text-sm">
                                                    {{ $log->pm_out_time ? \Carbon\Carbon::parse($log->pm_out_time)->format('g:i A') : '—' }}
                                                </span>
                                            </div>
                                        </div>

                                        @if($isModalWholeDay)
                                            <div class="mt-2 text-center">
                                                <span class="px-2 py-0.5 rounded text-[9px] font-black bg-blue-600 text-white uppercase italic tracking-widest shadow-sm">Whole Day Request</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mb-4">
                                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Reason</p>
                                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 text-sm text-gray-700 italic border-l-2 border-l-gray-300 max-h-32 overflow-y-auto whitespace-pre-wrap break-words custom-scrollbar">
                                            "{{ $log->recovery_reason ?? 'No reason provided.' }}"
                                        </div>
                                    </div>

                                    @php
                                        $recoveryPhoto = $log->am_out_photo ?? $log->pm_out_photo;
                                        $recoveryLat = $log->am_out_lat ?? $log->pm_out_lat ?? null;
                                        $recoveryLng = $log->am_out_lng ?? $log->pm_out_lng ?? null;
                                    @endphp
                                    @if($recoveryPhoto)
                                        <div class="mb-5">
                                            <div class="flex justify-between items-end mb-2">
                                                 <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Proof Photo (Full View)</p>
                                                 <div class="flex items-center gap-2">
                                                     @if($recoveryLat && $recoveryLng)
                                                         <button onclick="showRecoveryPhotoMap('{{ Storage::url($recoveryPhoto) }}', '{{ $recoveryLat }}', '{{ $recoveryLng }}', 'Recovery Photo')" 
                                                                 class="text-[10px] text-blue-600 cursor-pointer hover:underline flex items-center">
                                                             <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                             View on Map
                                                         </button>
                                                     @endif
                                                     <span class="text-[10px] text-blue-600 cursor-pointer hover:underline flex items-center" onclick="window.open('{{ Storage::url($recoveryPhoto) }}', '_blank')">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z"/></svg>
                                                        Open Original
                                                     </span>
                                                 </div>
                                            </div>
                                            <!-- Enhanced Image Container -->
                                            <div class="h-auto min-h-[250px] bg-black rounded-lg border border-gray-300 overflow-hidden relative group cursor-pointer shadow-inner" onclick="window.open('{{ Storage::url($recoveryPhoto) }}', '_blank')">
                                                <img src="{{ Storage::url($recoveryPhoto) }}" class="w-full h-full object-contain max-h-[400px] mx-auto transition-transform duration-300">
                                            </div>
                                            <p class="text-[10px] text-gray-400 mt-1 italic text-center">Click image to view in full resolution</p>
                                        </div>
                                    @else
                                        <div class="mb-5 p-4 bg-red-50 text-red-700 text-sm rounded-lg border border-red-200 flex items-center justify-center font-medium">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            No proof photo provided.
                                        </div>
                                    @endif

                                    <div class="flex gap-3 pt-3 border-t border-gray-100">
                                        <button onclick="submitDecision(this, {{ $log->id }}, 'reject')" class="flex-1 py-2.5 bg-white border border-red-200 text-red-700 rounded-lg hover:bg-red-50 font-medium text-sm transition-colors">
                                            Reject
                                        </button>
                                        <button onclick="submitDecision(this, {{ $log->id }}, 'approve')" class="flex-1 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold text-sm shadow-sm transition-colors flex items-center justify-center">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Approve
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-10">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <p class="text-lg font-medium text-gray-500">All pending recoveries reviewed!</p>
                            <button onclick="document.getElementById('recoveryReviewModal').classList.add('hidden')" class="mt-4 text-sm text-ojt-primary hover:underline">Close</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Modal (History & Review) -->
    <div id="attendanceModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-start justify-center min-h-screen p-4 pt-10">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('attendanceModal').classList.add('hidden')"></div>

            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                            Attendance Logs
                        </h3>
                        <button onclick="document.getElementById('attendanceModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                @php
                    $allLogs = \App\Models\AttendanceLog::where('student_user_id', $student->id)
                        ->orderBy('work_date', 'desc')
                        ->get();
                @endphp

                <div class="flex-1 overflow-y-auto px-4 sm:px-6 py-4 space-y-3">
                    @if($allLogs->count() > 0)
                        @foreach($allLogs as $log)
                            <div class="border rounded-lg p-4 {{ $log->is_recovered && is_null($log->recovery_approved) ? 'bg-yellow-50 border-yellow-200 border-2' : ($log->status === 'approved' ? 'bg-white border-gray-200' : 'bg-gray-50 border-gray-200') }}">
                                <!-- Date & Status Row -->
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-sm font-semibold text-gray-900">{{ $log->work_date->format('M d, Y') }}</span>
                                    </div>
                                    @php
                                        // Simple completion check
                                        $isComplete = ($log->am_in_time && $log->am_out_time) || ($log->pm_in_time && $log->pm_out_time);
                                        $isPastDate = $log->work_date < today();
                                        $isMissed = $isPastDate && !$isComplete && $log->status !== 'pending' && !$log->is_recovered;
                                    @endphp

                                    @if($log->is_recovered && is_null($log->recovery_approved))
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 animate-pulse">Pending Review</span>
                                    @elseif($log->is_recovered && $log->recovery_approved === true)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Recovered (Approved)</span>
                                    @elseif($log->is_recovered && $log->recovery_approved === false)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Recovery Rejected</span>
                                    @elseif($isMissed)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 border border-red-200">Incomplete</span>
                                    @elseif($log->status === 'approved' && $isComplete)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">In Progress</span>
                                    @endif
                                </div>

                                <!-- Time & Hours Row -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Time In/Out</p>
                                        <p class="text-sm text-gray-900">
                                            <span class="font-medium">In:</span> {{ $log->time_in_formatted }}
                                        </p>
                                        <p class="text-sm text-gray-900 has-tooltip">
                                            <span class="font-medium">Out:</span> {{ $log->time_out_formatted }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Hours Logged</p>
                                        <div class="flex items-end gap-2">
                                            <p class="text-lg font-bold text-gray-900">{{ $log->hours_worked_formatted }}h</p>
                                            @if($log->overtime_minutes && $log->overtime_minutes > 0)
                                                <span class="text-xs font-bold text-green-600 mb-1">
                                                    +{{ number_format($log->overtime_minutes / 60, 2) }}h OT
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Inline Review -->
                                @if($log->is_recovered && is_null($log->recovery_approved))
                                    <button onclick="toggleReviewPanel({{ $log->id }})" id="review-btn-{{ $log->id }}"
                                            class="w-full mt-4 inline-flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-200 font-bold text-sm shadow-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        Review Recovery Request
                                    </button>

                                    <!-- Enhanced Review Panel -->
                                    <div id="review-panel-{{ $log->id }}" class="hidden mt-4 pt-4 border-t border-yellow-200 animate-fade-in-down">
                                        <div class="mb-4">
                                            <p class="text-[11px] font-bold text-gray-500 uppercase mb-3 tracking-widest flex items-center">
                                                <svg class="w-3 h-3 mr-1 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/></svg>
                                                Timeline Comparison
                                            </p>
                                            
                                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                                @php
                                                    $isWholeDay = $log->pm_out_time && $log->is_recovered && ($log->pm_in_photo === $log->am_out_photo);
                                                    // Recovered slots usually have the same photo as the one uploaded in recovery
                                                    // For simple recovery, only am_out or pm_out is recovered.
                                                @endphp

                                                <!-- AM IN -->
                                                <div class="p-2 rounded border bg-green-50 border-green-100">
                                                    <span class="block text-[9px] font-bold text-green-600 uppercase tracking-tight">AM In (Regular)</span>
                                                    <span class="block text-green-900 font-bold text-sm">{{ $log->am_in_time ? \Carbon\Carbon::parse($log->am_in_time)->format('g:i A') : '—' }}</span>
                                                </div>

                                                <!-- AM OUT -->
                                                @php $isAmRec = $log->is_recovered && $log->am_out_time && ($isWholeDay || !$log->pm_in_time); @endphp
                                                <div class="p-2 rounded border {{ $isAmRec ? 'bg-blue-50 border-blue-200 ring-1 ring-blue-500/20' : 'bg-green-50 border-green-100' }}">
                                                    <span class="block text-[9px] font-bold {{ $isAmRec ? 'text-blue-600' : 'text-green-600' }} uppercase tracking-tight">
                                                        AM Out {{ $isAmRec ? '(Recovery)' : '(Regular)' }}
                                                    </span>
                                                    <span class="block {{ $isAmRec ? 'text-blue-900' : 'text-green-900' }} font-bold text-sm">
                                                        {{ $log->am_out_time ? \Carbon\Carbon::parse($log->am_out_time)->format('g:i A') : '—' }}
                                                    </span>
                                                </div>

                                                <!-- PM IN -->
                                                @php $isPmInRec = $log->is_recovered && $isWholeDay; @endphp
                                                <div class="p-2 rounded border {{ $isPmInRec ? 'bg-blue-50 border-blue-200 ring-1 ring-blue-500/20' : ($log->pm_in_time ? 'bg-green-50 border-green-100' : 'bg-gray-50 border-gray-100') }}">
                                                    <span class="block text-[9px] font-bold {{ $isPmInRec ? 'text-blue-600' : ($log->pm_in_time ? 'text-green-600' : 'text-gray-400') }} uppercase tracking-tight">
                                                        PM In {{ $isPmInRec ? '(Recovery)' : ($log->pm_in_time ? '(Regular)' : '') }}
                                                    </span>
                                                    <span class="block {{ $isPmInRec ? 'text-blue-900' : ($log->pm_in_time ? 'text-green-900' : 'text-gray-400') }} font-bold text-sm">
                                                        {{ $log->pm_in_time ? \Carbon\Carbon::parse($log->pm_in_time)->format('g:i A') : '—' }}
                                                    </span>
                                                </div>

                                                <!-- PM OUT -->
                                                @php $isPmOutRec = $log->is_recovered && $log->pm_out_time; @endphp
                                                <div class="p-2 rounded border {{ $isPmOutRec ? 'bg-blue-50 border-blue-200 ring-1 ring-blue-500/20' : ($log->pm_out_time ? 'bg-green-50 border-green-100' : 'bg-gray-50 border-gray-100') }}">
                                                    <span class="block text-[9px] font-bold {{ $isPmOutRec ? 'text-blue-600' : ($log->pm_out_time ? 'text-green-600' : 'text-gray-400') }} uppercase tracking-tight">
                                                        PM Out {{ $isPmOutRec ? '(Recovery)' : ($log->pm_out_time ? '(Regular)' : '') }}
                                                    </span>
                                                    <span class="block {{ $isPmOutRec ? 'text-blue-900' : ($log->pm_out_time ? 'text-green-900' : 'text-gray-400') }} font-bold text-sm">
                                                        {{ $log->pm_out_time ? \Carbon\Carbon::parse($log->pm_out_time)->format('g:i A') : '—' }}
                                                    </span>
                                                </div>
                                            </div>

                                            @if($isWholeDay)
                                                <div class="mt-2 text-center">
                                                    <span class="px-2 py-0.5 rounded text-[9px] font-black bg-blue-600 text-white uppercase italic tracking-widest shadow-sm">Whole Day Request</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 mb-4">
                                            <span class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Reason for Recovery</span>
                                            <p class="text-sm text-gray-700 italic">"{{ $log->recovery_reason }}"</p>
                                        </div>

                                        @php
                                            $recoveryPhoto = $log->am_out_photo ?? $log->pm_out_photo;
                                            $recoveryLat = $log->am_out_lat ?? $log->pm_out_lat ?? null;
                                            $recoveryLng = $log->am_out_lng ?? $log->pm_out_lng ?? null;
                                        @endphp
                                        @if($recoveryPhoto)
                                            <div class="mb-4">
                                                <div class="flex justify-between items-end mb-1">
                                                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Proof Photo</h4>
                                                    <div class="flex items-center gap-2">
                                                        @if($recoveryLat && $recoveryLng)
                                                            <button onclick="showRecoveryPhotoMap('{{ Storage::url($recoveryPhoto) }}', '{{ $recoveryLat }}', '{{ $recoveryLng }}', 'Recovery Photo')" 
                                                                    class="text-[10px] text-blue-600 cursor-pointer hover:underline flex items-center">
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                                View on Map
                                                            </button>
                                                        @endif
                                                        <span class="text-[10px] text-blue-600 cursor-pointer hover:underline" onclick="window.open('{{ Storage::url($recoveryPhoto) }}', '_blank')">View Full Size</span>
                                                    </div>
                                                </div>
                                                <div class="relative h-32 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 group cursor-pointer" onclick="window.open('{{ Storage::url($recoveryPhoto) }}', '_blank')">
                                                    <img src="{{ Storage::url($recoveryPhoto) }}" class="w-full h-full object-cover transition-transform group-hover:scale-110">
                                                </div>
                                            </div>
                                        @else
                                            <div class="mb-4 p-2 bg-red-50 text-red-600 text-xs rounded border border-red-100">
                                                No proof photo attached.
                                            </div>
                                        @endif

                                        <div class="flex gap-2">
                                            <button onclick="submitDecision(this, {{ $log->id }}, 'reject')" class="flex-1 py-2 bg-white text-red-600 border border-red-200 rounded hover:bg-red-50 font-bold text-xs uppercase tracking-wide">
                                                Reject
                                            </button>
                                            <button onclick="submitDecision(this, {{ $log->id }}, 'approve')" class="flex-1 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-bold text-xs uppercase tracking-wide shadow-sm">
                                                Approve Recovery
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500 text-center py-8">No attendance history found.</p>
                    @endif
                </div>
                
                <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
                    <button type="button" onclick="document.getElementById('attendanceModal').classList.add('hidden')"
                            class="w-full inline-flex justify-center items-center rounded-md border border-gray-300 shadow-sm px-4 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ojt-primary">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function toggleReviewPanel(id) {
            const panel = document.getElementById(`review-panel-${id}`);
            const btn = document.getElementById(`review-btn-${id}`);
            
            if (panel.classList.contains('hidden')) {
                // Open
                panel.classList.remove('hidden');
                btn.classList.add('hidden'); // Hide the main review button when open to reduce clutter
            } else {
                // Close
                panel.classList.add('hidden');
                btn.classList.remove('hidden');
            }
        }

        async function submitDecision(btn, logId, decision) {
            if (!logId) return;
            
            const action = decision === 'approve' ? 'approve-recovery' : 'reject-recovery';
            const confirmMsg = decision === 'approve' 
                ? 'APPROVE this recovery? Hours will be added immediately.' 
                : 'REJECT this recovery request?';

            if (!confirm(confirmMsg)) return;

            const originalContent = btn.innerHTML;
            
            try {
                btn.disabled = true;
                btn.innerHTML = '...';
                btn.classList.add('opacity-75');

                const response = await fetch(`/supervisor/attendance/${logId}/${action}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Refresh page to update stats
                    window.location.reload();
                } else {
                    alert(data.message || 'Error processing request');
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                    btn.classList.remove('opacity-75');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred.');
                btn.disabled = false;
                btn.innerHTML = originalContent;
                btn.classList.remove('opacity-75');
            }
        }

        function showRecoveryPhotoMap(photoUrl, lat, lng, title) {
            document.getElementById('recoveryPhotoMapTitle').textContent = title;
            document.getElementById('recoveryModalPhoto').src = photoUrl;
            
            const mapFrame = document.getElementById('recoveryGoogleMap');
            const mapLink = document.getElementById('recoveryExternalMapLink');
            const noMap = document.getElementById('recoveryNoMapMessage');
            
            // Check if lat/lng are valid
            const hasValidLocation = lat && lng && 
                                     lat !== 'null' && lng !== 'null' && 
                                     lat !== '' && lng !== '' &&
                                     !isNaN(parseFloat(lat)) && !isNaN(parseFloat(lng)) &&
                                     parseFloat(lat) !== 0 && parseFloat(lng) !== 0;
            
            if (hasValidLocation) {
                const mapUrl = `https://maps.google.com/maps?q=${lat},${lng}&t=&z=15&ie=UTF8&iwloc=&output=embed`;
                mapFrame.src = mapUrl;
                mapFrame.classList.remove('hidden');
                noMap.classList.add('hidden');
                
                mapLink.href = `https://www.google.com/maps?q=${lat},${lng}`;
                mapLink.style.display = 'inline-flex';
            } else {
                mapFrame.classList.add('hidden');
                noMap.classList.remove('hidden');
                mapLink.style.display = 'none';
            }

            document.getElementById('recoveryPhotoMapModal').classList.remove('hidden');
        }

        function closeRecoveryPhotoMap() {
            document.getElementById('recoveryPhotoMapModal').classList.add('hidden');
            document.getElementById('recoveryGoogleMap').src = '';
        }
    </script>

    <!-- Recovery Photo Map Modal -->
    <div id="recoveryPhotoMapModal" class="fixed inset-0 z-50 hidden overflow-y-auto" onclick="closeRecoveryPhotoMap()">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-4xl sm:w-full" onclick="event.stopPropagation()">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="recoveryPhotoMapTitle">Recovery Photo</h3>
                        <button type="button" onclick="closeRecoveryPhotoMap()" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Photo Column -->
                        <div class="bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center h-full min-h-[300px]">
                            <img id="recoveryModalPhoto" src="" alt="Recovery Photo" class="max-w-full max-h-[500px] object-contain">
                        </div>
                        <!-- Map Column -->
                        <div class="bg-gray-100 rounded-lg overflow-hidden h-[300px] md:h-auto relative">
                            <div id="recoveryNoMapMessage" class="hidden absolute inset-0 flex items-center justify-center text-gray-500 text-sm">
                                No location data available
                            </div>
                            <iframe id="recoveryGoogleMap" class="w-full h-full" frameborder="0" style="border:0" allowfullscreen loading="lazy" src=""></iframe>
                        </div>
                    </div>
                    <!-- External Link -->
                    <div class="mt-4 text-right">
                        <a id="recoveryExternalMapLink" href="#" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 font-medium inline-flex items-center">
                            Open in Google Maps
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
