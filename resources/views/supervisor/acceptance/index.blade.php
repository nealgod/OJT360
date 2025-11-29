<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                Acceptance Letters
            </h2>
            <a href="{{ route('supervisor.students.search') }}" class="bg-ojt-primary text-white px-4 py-2 rounded-lg hover:bg-maroon-700 transition-colors">
                + Accept New Student
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Supervised Students</p>
                            <p class="text-2xl font-bold text-ojt-dark">{{ $studentsCount }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Generated Letters</p>
                            <p class="text-2xl font-bold text-ojt-dark">{{ $generatedLetters->total() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">This Month</p>
                            <p class="text-2xl font-bold text-ojt-dark">{{ \App\Models\AcceptanceLetter::where('supervisor_user_id', Auth::id())->whereMonth('created_at', now()->month)->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Generated Letters -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Generated Acceptance Letters</h3>
                        <p class="text-sm text-gray-600 mt-1">View and download acceptance letters you've generated</p>
                    </div>
                </div>

                @if($generatedLetters->count() > 0)
                    <div class="space-y-4">
                        @foreach($generatedLetters as $letter)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-ojt-primary transition-colors">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                    <div class="flex items-start gap-4 flex-1">
                                        <!-- Student Avatar -->
                                        <div class="flex-shrink-0">
                                            @if($letter->student->studentProfile && $letter->student->studentProfile->profile_image)
                                                <img src="{{ Storage::url($letter->student->studentProfile->profile_image) }}" 
                                                     alt="{{ $letter->student->name }}" 
                                                     class="w-12 h-12 rounded-full object-cover border-2 border-ojt-primary">
                                            @else
                                                <div class="w-12 h-12 {{ $letter->student->getAvatarColor() }} rounded-full flex items-center justify-center text-white text-lg font-bold">
                                                    {{ substr($letter->student->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2">
                                                <h4 class="font-medium text-gray-900 truncate">{{ $letter->student->name }}</h4>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 w-fit">
                                                    Generated
                                                </span>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-4 text-sm text-gray-600">
                                                <div class="truncate">
                                                    <span class="font-medium">Position:</span> {{ $letter->job_title }}
                                                </div>
                                                <div class="truncate">
                                                    <span class="font-medium">Department:</span> {{ $letter->department }}
                                                </div>
                                                <div>
                                                    <span class="font-medium">Start Date:</span> {{ $letter->start_date->format('M d, Y') }}
                                                </div>
                                                <div>
                                                    <span class="font-medium">Generated:</span> {{ $letter->created_at->format('M d, Y') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex sm:flex-shrink-0">
                                        <a href="{{ route('acceptance-letters.download', $letter) }}" 
                                           class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-ojt-primary text-white rounded-lg hover:bg-maroon-700 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $generatedLetters->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No acceptance letters yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by accepting a student.</p>
                        <div class="mt-6">
                            <a href="{{ route('supervisor.students.search') }}" class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white rounded-lg hover:bg-maroon-700 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Accept Student
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
