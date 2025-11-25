<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
            Monthly Evaluations
        </h2>
        <p class="text-sm text-gray-500">Review and manage student monthly progress evaluations</p>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-md bg-green-50 border border-green-100 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 rounded-md bg-red-50 border border-red-100 px-4 py-3 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total</p>
                            <p class="text-2xl font-bold text-ojt-dark">{{ $evaluations->total() }}</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Pending Review</p>
                            <p class="text-2xl font-bold text-yellow-600">
                                {{ $evaluations->where('reviewed_at', null)->count() }}
                            </p>
                        </div>
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Reviewed</p>
                            <p class="text-2xl font-bold text-green-600">
                                {{ $evaluations->whereNotNull('reviewed_at')->count() }}
                            </p>
                        </div>
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">This Month</p>
                            <p class="text-2xl font-bold text-purple-600">
                                {{ $evaluations->filter(function($e) { 
                                    return $e->evaluation_month === now()->month && 
                                           $e->evaluation_year === now()->year; 
                                })->count() }}
                            </p>
                        </div>
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
                <form method="GET" action="{{ route('coordinator.evaluations.index') }}" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search by student ID..." 
                               class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-ojt-primary text-white rounded-lg hover:bg-maroon-700 transition-colors">
                            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Search
                        </button>
                        @if(request('search'))
                            <a href="{{ route('coordinator.evaluations.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-4 sm:p-6">
                @if($evaluations->count() > 0)
                    <!-- Mobile: Card View -->
                    <div class="block lg:hidden space-y-4">
                        @foreach($evaluations as $evaluation)
                            <div class="border rounded-lg p-4 hover:border-ojt-primary/50 transition-colors">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center space-x-3">
                                        @if($evaluation->student->studentProfile && $evaluation->student->studentProfile->profile_image)
                                            <img src="{{ Storage::url($evaluation->student->studentProfile->profile_image) }}" 
                                                 alt="{{ $evaluation->student->name }}" 
                                                 class="w-10 h-10 rounded-full object-cover">
                                        @else
                                            <div class="w-10 h-10 {{ $evaluation->student->getAvatarColor() }} rounded-full flex items-center justify-center text-white text-sm font-bold">
                                                {{ substr($evaluation->student->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="font-semibold text-ojt-dark">{{ $evaluation->student->name }}</h3>
                                            <p class="text-xs text-gray-500">{{ $evaluation->student->studentProfile->student_id ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <x-evaluation-status-badge :evaluation="$evaluation" />
                                </div>
                                
                                <div class="space-y-2 text-sm mb-3">
                                    <div class="flex items-center text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $evaluation->getMonthYearLabel() }} (Month {{ $evaluation->month_number }})
                                    </div>
                                    <div class="flex items-center text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Supervisor: {{ $evaluation->supervisor->name }}
                                    </div>
                                </div>
                                
                                <div class="flex gap-2">
                                    <a href="{{ route('coordinator.evaluations.show', $evaluation) }}"
                                       class="flex-1 text-center px-4 py-2 bg-ojt-primary text-white rounded-lg text-sm font-medium hover:bg-maroon-700 transition-colors">
                                        Review →
                                    </a>
                                    <a href="{{ route('coordinator.evaluations.download-pdf', $evaluation) }}"
                                       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Desktop: Table View -->
                    <div class="hidden lg:block">
                        <div class="overflow-x-auto">
                            <div class="max-h-[640px] overflow-y-auto border border-gray-200 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supervisor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($evaluations as $evaluation)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($evaluation->student->studentProfile && $evaluation->student->studentProfile->profile_image)
                                                    <img src="{{ Storage::url($evaluation->student->studentProfile->profile_image) }}" 
                                                         alt="{{ $evaluation->student->name }}" 
                                                         class="w-10 h-10 rounded-full object-cover">
                                                @else
                                                    <div class="w-10 h-10 {{ $evaluation->student->getAvatarColor() }} rounded-full flex items-center justify-center text-white text-sm font-bold">
                                                        {{ substr($evaluation->student->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">{{ $evaluation->student->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $evaluation->student->studentProfile->student_id ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $evaluation->getMonthYearLabel() }}</div>
                                            <div class="text-xs text-gray-500">Month {{ $evaluation->month_number }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $evaluation->supervisor->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-evaluation-status-badge :evaluation="$evaluation" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $evaluation->submitted_at ? $evaluation->submitted_at->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                <a href="{{ route('coordinator.evaluations.show', $evaluation) }}"
                                                   class="text-ojt-primary hover:text-maroon-700">
                                                    Review
                                                </a>
                                                <span class="text-gray-300">|</span>
                                                <a href="{{ route('coordinator.evaluations.download-pdf', $evaluation) }}"
                                                   class="text-gray-600 hover:text-gray-900">
                                                    PDF
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        {{ $evaluations->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-gray-500 text-lg">No evaluations found</p>
                        @if(request('search'))
                            <p class="text-gray-400 text-sm mt-2">Try adjusting your search criteria</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
