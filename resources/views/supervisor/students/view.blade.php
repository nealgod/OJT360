<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Student Profile') }}
            </h2>
            <a href="{{ route('supervisor.students.search') }}" class="text-ojt-primary hover:text-maroon-700 text-sm font-medium">
                ← Back to Search
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
                            <div class="w-24 h-24 rounded-full bg-ojt-primary flex items-center justify-center text-white text-3xl font-bold border-4 border-ojt-primary">
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

            <!-- Action Buttons -->
            <div class="flex items-center justify-between">
                <a href="{{ route('supervisor.students.search') }}" 
                   class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Search Another Student
                </a>

                @if(!$student->studentProfile || !$student->studentProfile->supervisor_id || $student->studentProfile->supervisor_id === Auth::id())
                    <a href="{{ route('supervisor.students.accept', $student->id) }}" 
                       class="inline-flex items-center px-6 py-3 bg-ojt-primary text-white rounded-lg hover:bg-maroon-700 transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Accept & Generate Letter
                    </a>
                @else
                    <div class="text-sm text-gray-500">
                        This student already has a supervisor
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
