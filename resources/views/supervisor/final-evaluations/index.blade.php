<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
            Final Evaluations
        </h2>
        <p class="text-sm text-gray-500">View all final OJT performance evaluations you've submitted</p>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-md bg-green-50 border border-green-100 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6">
                @if($evaluations->count() > 0)
                    <div class="space-y-4">
                        @foreach($evaluations as $evaluation)
                            <div class="border rounded-lg p-4 hover:border-ojt-primary/50 transition">
                                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                    <div class="flex gap-4 flex-1">
                                        <!-- Student Avatar -->
                                        <div class="flex-shrink-0">
                                            @if($evaluation->student && $evaluation->student->studentProfile && $evaluation->student->studentProfile->profile_image)
                                                <img src="{{ Storage::url($evaluation->student->studentProfile->profile_image) }}" 
                                                     alt="{{ $evaluation->student_name }}" 
                                                     class="w-16 h-16 rounded-full object-cover border-2 border-gray-200">
                                            @else
                                                <div class="w-16 h-16 {{ $evaluation->student ? $evaluation->student->getAvatarColor() : 'bg-gray-400' }} rounded-full flex items-center justify-center text-white text-xl font-bold border-2 border-gray-200">
                                                    {{ substr($evaluation->student_name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Student Info -->
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <h3 class="text-lg font-semibold text-ojt-dark">
                                                    {{ $evaluation->student_name }}
                                                </h3>
                                                <x-final-evaluation-status-badge :evaluation="$evaluation" />
                                            </div>
                                            <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-2">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                                    </svg>
                                                    <strong>{{ $evaluation->student_id }}</strong>
                                                </span>
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                    {{ $evaluation->hte_name }}
                                                </span>
                                            </div>
                                            <div class="flex flex-wrap gap-4 text-sm">
                                                <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 rounded-md">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Rating: <strong class="ml-1">{{ number_format($evaluation->total_rating, 2) }}%</strong>
                                                </span>
                                                @if($evaluation->submitted_at)
                                                    <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 rounded-md">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        {{ $evaluation->submitted_at->format('M d, Y') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('supervisor.final-evaluations.show', $evaluation) }}"
                                           class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white rounded-lg text-sm font-medium hover:bg-maroon-700 transition-colors">
                                            View Details →
                                        </a>
                                        <a href="{{ route('supervisor.final-evaluations.pdf', $evaluation) }}"
                                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
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

                    <div class="mt-6">
                        {{ $evaluations->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-gray-500 text-lg mb-2">No final evaluations yet</p>
                        <p class="text-gray-400 text-sm">Final evaluations will appear here once you submit them</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
