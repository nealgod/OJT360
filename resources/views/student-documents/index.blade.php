<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header with Stats -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-ojt-dark">My Documents</h1>
                        <p class="text-gray-600 mt-2">Create and manage your professional documents</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-center px-4 py-2 bg-ojt-primary/10 rounded-lg">
                            <p class="text-2xl font-bold text-ojt-primary">{{ $resumes->count() }}</p>
                            <p class="text-xs text-gray-600">Resumes</p>
                        </div>
                        <div class="text-center px-4 py-2 bg-ojt-accent/10 rounded-lg">
                            <p class="text-2xl font-bold text-ojt-accent">{{ $applicationLetters->count() }}</p>
                            <p class="text-xs text-gray-600">Letters</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages -->


            <!-- Create Document Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Create Resume -->
                <a href="{{ route('student-documents.resume.create') }}" class="block bg-white rounded-xl border-2 border-ojt-primary hover:border-maroon-700 transition-all duration-200 p-6 group">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-ojt-primary group-hover:bg-maroon-700 transition-colors rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-ojt-dark group-hover:text-ojt-primary transition-colors">Create Resume</h3>
                            <p class="text-sm text-gray-600">Build your professional resume</p>
                        </div>
                        <svg class="w-6 h-6 text-gray-400 group-hover:text-ojt-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>

                <!-- Create Application Letter -->
                <a href="{{ route('student-documents.application-letter.create') }}" class="block bg-white rounded-xl border-2 border-ojt-accent hover:border-ojt-primary transition-all duration-200 p-6 group">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-ojt-accent group-hover:bg-ojt-primary transition-colors rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-ojt-dark group-hover:text-ojt-primary transition-colors">Create Application Letter</h3>
                            <p class="text-sm text-gray-600">Write your application letter</p>
                        </div>
                        <svg class="w-6 h-6 text-gray-400 group-hover:text-ojt-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            </div>

            <!-- Documents List -->
            <div class="space-y-6">
                <!-- Resumes Section -->
                @if($resumes->count() > 0)
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-ojt-dark">Resumes</h2>
                        </div>
                        <div class="divide-y divide-gray-200">
                            @foreach($resumes as $index => $resume)
                                <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-ojt-primary/10 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-ojt-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <h3 class="font-medium text-ojt-dark break-words text-sm sm:text-base">Resume #{{ $resumes->count() - $index }}</h3>
                                                    @if($resume->submitted_to_documents)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            Submitted
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-xs sm:text-sm text-gray-500 break-words">Created {{ $resume->created_at->format('M d, Y') }}</p>
                                                @if($resume->submitted_to_documents && $resume->submitted_at)
                                                    <p class="text-xs text-green-600 mt-1">Submitted {{ $resume->submitted_at->diffForHumans() }}</p>
                                                @endif
                                                @if($resume->personal_info && isset($resume->personal_info['job_title']) && $resume->personal_info['job_title'])
                                                    <p class="text-xs text-gray-400 mt-1 truncate max-w-full">{{ $resume->personal_info['job_title'] }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2 justify-end">
                                            @if($hasActiveResumeSubmission)
                                                <span class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-500 text-sm font-medium rounded-lg cursor-not-allowed" title="A resume is already submitted in Documents">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                    Cannot Submit
                                                </span>
                                            @elseif($resume->submitted_to_documents)
                                                <span class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-500 text-sm font-medium rounded-lg cursor-not-allowed">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Already Submitted
                                                </span>
                                            @else
                                                <form action="{{ route('student-documents.resume.submit', $resume) }}" method="POST" class="w-full sm:w-auto">
                                                    @csrf
                                                    <button type="submit" class="inline-flex w-full sm:w-auto justify-center items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors" onclick="return confirm('Submit this resume to your coordinator for review?')">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                        </svg>
                                                        Send to Documents
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('student-documents.resume.download', $resume) }}" class="inline-flex w-full sm:w-auto justify-center items-center px-4 py-2 bg-ojt-primary text-white text-sm font-medium rounded-lg hover:bg-maroon-700 transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                Download
                                            </a>
                                            <a href="{{ route('student-documents.resume.edit', $resume) }}" class="inline-flex w-full sm:w-auto justify-center items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </a>
                                            <form action="{{ route('student-documents.resume.destroy', $resume) }}" method="POST" class="w-full sm:w-auto" onsubmit="return confirm('Are you sure you want to delete this resume?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex w-full sm:w-auto justify-center items-center px-4 py-2 bg-white border border-red-300 text-red-700 text-sm font-medium rounded-lg hover:bg-red-50 transition-colors">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Application Letters Section -->
                @if($applicationLetters->count() > 0)
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-ojt-dark">Application Letters</h2>
                        </div>
                        <div class="divide-y divide-gray-200">
                            @foreach($applicationLetters as $index => $letter)
                                <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-ojt-accent/10 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-ojt-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <h3 class="font-medium text-ojt-dark break-words text-sm sm:text-base">Application Letter #{{ $applicationLetters->count() - $index }}</h3>
                                                    @if($letter->submitted_to_documents)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            Submitted
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-xs sm:text-sm text-gray-500 break-words">Created {{ $letter->created_at->format('M d, Y') }}</p>
                                                @if($letter->submitted_to_documents && $letter->submitted_at)
                                                    <p class="text-xs text-green-600 mt-1">Submitted {{ $letter->submitted_at->diffForHumans() }}</p>
                                                @endif
                                                <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ Str::limit($letter->content, 80) }}</p>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2 justify-end">
                                            @if($hasActiveLetterSubmission)
                                                <span class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-500 text-sm font-medium rounded-lg cursor-not-allowed" title="An application letter is already submitted in Documents">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                    Cannot Submit
                                                </span>
                                            @elseif($letter->submitted_to_documents)
                                                <span class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-500 text-sm font-medium rounded-lg cursor-not-allowed">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Already Submitted
                                                </span>
                                            @else
                                                <form action="{{ route('student-documents.application-letter.submit', $letter) }}" method="POST" class="w-full sm:w-auto">
                                                    @csrf
                                                    <button type="submit" class="inline-flex w-full sm:w-auto justify-center items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors" onclick="return confirm('Submit this application letter to your coordinator for review?')">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                        </svg>
                                                        Send to Documents
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('student-documents.application-letter.download', $letter) }}" class="inline-flex w-full sm:w-auto justify-center items-center px-4 py-2 bg-ojt-primary text-white text-sm font-medium rounded-lg hover:bg-maroon-700 transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                Download
                                            </a>
                                            <a href="{{ route('student-documents.application-letter.edit', $letter) }}" class="inline-flex w-full sm:w-auto justify-center items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </a>
                                            <form action="{{ route('student-documents.application-letter.destroy', $letter) }}" method="POST" class="w-full sm:w-auto" onsubmit="return confirm('Are you sure you want to delete this application letter?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex w-full sm:w-auto justify-center items-center px-4 py-2 bg-white border border-red-300 text-red-700 text-sm font-medium rounded-lg hover:bg-red-50 transition-colors">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Empty State -->
                @if($resumes->count() === 0 && $applicationLetters->count() === 0)
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No documents yet</h3>
                        <p class="text-gray-600 mb-6">Create your first document to get started</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
