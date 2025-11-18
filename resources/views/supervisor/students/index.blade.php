<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('My Supervised Students') }}
                </h2>
                <p class="text-sm text-gray-500">
                    Track everyone you’ve accepted and jump back into their profiles or letters.
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('supervisor.students.search') }}"
                   class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white text-sm font-medium rounded-lg hover:bg-maroon-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Accept Another Student
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($students->count())
                <div class="space-y-4">
                    @foreach ($students as $student)
                        @php
                            $profile = $student->studentProfile;
                            $company = $profile?->company;
                            $latestLetter = $student->acceptanceLetters->first();
                        @endphp
                        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        {!! $student->getAvatarHtml('w-14 h-14') !!}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-3">
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $student->name }}</h3>
                                            @if ($profile?->student_id)
                                                <span class="text-xs px-2 py-1 bg-gray-100 text-gray-700 rounded-full">
                                                    {{ $profile->student_id }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-600 space-x-2">
                                            @if ($profile?->course)
                                                <span>{{ $profile->course }}</span>
                                            @endif
                                            @if ($profile?->department)
                                                <span class="text-gray-400">•</span>
                                                <span>{{ $profile->department }}</span>
                                            @endif
                                        </div>
                                        @if ($company)
                                            <div class="text-sm text-gray-500 mt-1">
                                                <span class="font-medium text-gray-700">Company:</span>
                                                {{ $company->name }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    <a href="{{ route('supervisor.students.view', $student->id) }}"
                                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View Profile
                                    </a>
                                    @if ($latestLetter)
                                        <a href="{{ route('acceptance-letters.download', $latestLetter) }}"
                                           class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white text-sm font-medium rounded-lg hover:bg-maroon-700 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Download Letter
                                        </a>
                                    @else
                                        <a href="{{ route('supervisor.students.accept', $student->id) }}"
                                           class="inline-flex items-center px-4 py-2 bg-ojt-primary/10 text-ojt-primary text-sm font-medium rounded-lg hover:bg-ojt-primary/20 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Generate Letter
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500">Status</p>
                                    <p class="font-medium text-gray-900">
                                        {{ $profile?->ojt_status ? ucfirst($profile->ojt_status) : 'In Progress' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Start Date</p>
                                    <p class="font-medium text-gray-900">
                                        @if ($latestLetter?->start_date)
                                            {{ $latestLetter->start_date->format('M d, Y') }}
                                        @else
                                            <span class="text-gray-400">Not set</span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Total Hours</p>
                                    <p class="font-medium text-gray-900">
                                        @if ($latestLetter)
                                            {{ number_format($latestLetter->total_hours) }} hrs
                                        @else
                                            <span class="text-gray-400">Not set</span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Document ID</p>
                                    <p class="font-medium text-gray-900">
                                        @if ($latestLetter)
                                            {{ $latestLetter->document_id }}
                                        @else
                                            <span class="text-gray-400">No document yet</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $students->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg border border-dashed border-gray-300 p-10 text-center">
                    <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">No supervised students yet</h3>
                    <p class="text-gray-500 mt-2">
                        Once you accept a student, they’ll appear here for quick access to their details and documents.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('supervisor.students.search') }}"
                           class="inline-flex items-center px-5 py-3 bg-ojt-primary text-white font-medium rounded-lg hover:bg-maroon-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Accept a Student
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

