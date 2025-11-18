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
                                @if($student->studentProfile->supervisor_id === Auth::id())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        Your Student
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
                            <p class="text-xs text-gray-500 mb-3">{{ $applicationLetter->original_filename }}</p>
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
                            <p class="text-xs text-gray-500 mb-3">{{ $resume->original_filename }}</p>
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
                            <p class="text-xs text-gray-500 mb-3">{{ $recommendation->original_filename }}</p>
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
                $isSupervised = $student->studentProfile && $student->studentProfile->supervisor_id === Auth::id();
                // Use the variable passed from controller, or check if student has supervisor
                $hasLetter = $hasAcceptanceLetter ?? false;
            @endphp

            @if($isSupervised && $hasLetter)
                <!-- Student Reports Section (Only for supervised students) -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Daily Reports</h3>
                        <div class="flex gap-2">
                            <button onclick="filterReports('all')" id="btn-all" class="px-3 py-1 text-sm font-medium rounded-lg bg-ojt-primary text-white">
                                All
                            </button>
                            <button onclick="filterReports('week')" id="btn-week" class="px-3 py-1 text-sm font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                                This Week
                            </button>
                            <button onclick="filterReports('month')" id="btn-month" class="px-3 py-1 text-sm font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                                This Month
                            </button>
                        </div>
                    </div>

                    @php
                        $reports = $student->dailyReports()->latest('work_date')->get();
                    @endphp

                    @if($reports->count() > 0)
                        <div id="reports-container" class="space-y-3">
                            @foreach($reports as $report)
                                <div class="report-item border border-gray-200 rounded-lg p-4 hover:border-ojt-primary transition-colors" 
                                     data-date="{{ $report->work_date->format('Y-m-d') }}">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <h4 class="font-medium text-gray-900">{{ $report->work_date->format('l, F j, Y') }}</h4>
                                                <span class="text-xs px-2 py-1 rounded-full {{ $report->status === 'submitted' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                                    {{ ucfirst($report->status ?? 'submitted') }}
                                                </span>
                                                <span class="text-xs text-gray-500">{{ $report->work_date->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $report->summary }}</p>
                                            @if($report->attachment_path)
                                                <div class="mt-2">
                                                    <a href="{{ Storage::url($report->attachment_path) }}" target="_blank"
                                                       class="inline-flex items-center text-sm text-ojt-primary hover:text-maroon-700">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                        </svg>
                                                        View Attachment
                                                    </a>
                                                </div>
                                            @endif
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
                            <p class="text-gray-500">No reports submitted yet</p>
                        </div>
                    @endif
                </div>

                <script>
                    function filterReports(period) {
                        const now = new Date();
                        const reports = document.querySelectorAll('.report-item');
                        
                        // Update button styles
                        document.querySelectorAll('[id^="btn-"]').forEach(btn => {
                            btn.classList.remove('bg-ojt-primary', 'text-white');
                            btn.classList.add('bg-gray-100', 'text-gray-700');
                        });
                        document.getElementById('btn-' + period).classList.remove('bg-gray-100', 'text-gray-700');
                        document.getElementById('btn-' + period).classList.add('bg-ojt-primary', 'text-white');
                        
                        reports.forEach(report => {
                            const reportDate = new Date(report.dataset.date);
                            let show = false;
                            
                            if (period === 'all') {
                                show = true;
                            } else if (period === 'week') {
                                const weekAgo = new Date(now);
                                weekAgo.setDate(weekAgo.getDate() - 7);
                                show = reportDate >= weekAgo;
                            } else if (period === 'month') {
                                const monthAgo = new Date(now);
                                monthAgo.setMonth(monthAgo.getMonth() - 1);
                                show = reportDate >= monthAgo;
                            }
                            
                            report.style.display = show ? 'block' : 'none';
                        });
                    }
                </script>
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
                   class="inline-flex items-center px-6 py-3 {{ $isSupervised ? 'bg-ojt-primary text-white' : 'border border-gray-300 text-gray-700' }} rounded-lg hover:bg-{{ $isSupervised ? 'maroon-700' : 'gray-50' }} transition-colors font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    {{ $isSupervised ? 'Back to My Students' : 'Back to Search' }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
