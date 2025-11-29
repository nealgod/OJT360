<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Resume Builder') }}
            </h2>
            <a href="{{ route('resume.create') }}" class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white rounded-lg hover:bg-maroon-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create New Resume
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">


            @if($resumes->count() > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Resumes ({{ $resumes->count() }})</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($resumes as $resume)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    @if($resume->profile_image)
                                        <img src="{{ Storage::url($resume->profile_image) }}" alt="Profile" class="w-16 h-16 rounded-lg object-cover border border-gray-200">
                                    @else
                                        <div class="w-16 h-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <form action="{{ route('resume.destroy', $resume) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this resume? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>

                                <div class="mb-4">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-1">
                                        {{ $resume->personal_info['name'] ?? 'Untitled Resume' }}
                                    </h4>
                                    @if(!empty($resume->personal_info['job_title']))
                                        <p class="text-sm text-gray-600 mb-2">{{ $resume->personal_info['job_title'] }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500">
                                        Created: {{ $resume->created_at->format('M d, Y') }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Updated: {{ $resume->updated_at->format('M d, Y') }}
                                    </p>
                                </div>

                                <div class="flex flex-wrap gap-2 mb-4">
                                    @if(!empty($resume->education))
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">Education</span>
                                    @endif
                                    @if(!empty($resume->work_experience))
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">Experience</span>
                                    @endif
                                    @if(!empty($resume->skills))
                                        <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded">Skills</span>
                                    @endif
                                    @if(!empty($resume->certifications))
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded">Certifications</span>
                                    @endif
                                </div>

                                <div class="flex space-x-2">
                                    <a href="{{ route('resume.edit', $resume) }}" class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </a>
                                    <a href="{{ route('resume.download', $resume) }}" class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-ojt-primary text-white rounded-lg hover:bg-maroon-700 transition text-sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v16h16V4H4zm4 8l4 4 4-4m-4 4V8" />
                                        </svg>
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Resumes Created Yet</h3>
                        <p class="text-gray-600 mb-6">Create your first professional resume using our builder.</p>
                        <a href="{{ route('resume.create') }}" class="inline-flex items-center px-6 py-3 bg-ojt-primary text-white rounded-lg hover:bg-maroon-700 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create Your First Resume
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
