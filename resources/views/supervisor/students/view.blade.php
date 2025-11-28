<x-app-layout>
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
                    // Get specific documents: Application Letter, Resume/PDS, Recommendation Letter
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
                // Use the variable passed from controller, or check if student has supervisor
                $hasLetter = $hasAcceptanceLetter ?? false;
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
                                            PDF
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

                <!-- Monthly Progress Evaluations Section -->
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

                    <!-- Statistics -->
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
                                               class="inline-flex justify-center items-center px-3 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                                               download="{{ $student->studentProfile->student_id ?? 'student' }}-{{ $evaluation->getMonthYearLabel() }}.pdf">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                PDF
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
</x-app-layout>

