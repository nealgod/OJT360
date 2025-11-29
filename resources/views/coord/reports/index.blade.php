<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
            Weekly Reports
        </h2>
        <p class="text-sm text-gray-500">Review and manage student weekly reports</p>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Reports</p>
                            <p class="text-2xl font-bold text-ojt-dark">{{ $reports->total() }}</p>
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
                            <p class="text-sm text-gray-600">Students Submitted</p>
                            <p class="text-2xl font-bold text-purple-600">
                                {{ $reports->pluck('student_user_id')->unique()->count() }}
                            </p>
                        </div>
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Submitted</p>
                            <p class="text-2xl font-bold text-yellow-600">
                                {{ $reports->where('status', 'submitted')->count() }}
                            </p>
                        </div>
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
                <form method="GET" action="{{ route('coord.reports.index') }}" class="flex flex-col sm:flex-row gap-4">
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
                            <a href="{{ route('coord.reports.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-4 sm:p-6">
                @if($reports->count() > 0)
                    <!-- Mobile: Card View -->
                    <div class="block lg:hidden space-y-4">
                        @foreach($reports as $report)
                            <div class="border rounded-lg p-4 hover:border-ojt-primary/50 transition-colors">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center space-x-3">
                                        @if($report->student->studentProfile && $report->student->studentProfile->profile_image)
                                            <img src="{{ Storage::url($report->student->studentProfile->profile_image) }}" 
                                                 alt="{{ $report->student->name }}" 
                                                 class="w-10 h-10 rounded-full object-cover">
                                        @else
                                            <div class="w-10 h-10 {{ $report->student->getAvatarColor() }} rounded-full flex items-center justify-center text-white text-sm font-bold">
                                                {{ substr($report->student->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="font-semibold text-ojt-dark">{{ $report->student->name }}</h3>
                                            <p class="text-xs text-gray-500">{{ $report->student->studentProfile->student_id ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium
                                        @if($report->status === 'reviewed') bg-green-100 text-green-800
                                        @elseif($report->status === 'submitted') bg-blue-100 text-blue-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </div>
                                
                                <div class="space-y-2 text-sm mb-3">
                                    <div class="flex items-center text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Week {{ $report->week_number }}: {{ $report->week_start_date->format('M d') }} - {{ $report->week_end_date->format('M d, Y') }}
                                    </div>
                                    <div class="flex items-center text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $report->days_present }} days • {{ number_format($report->total_hours, 2) }} hours
                                    </div>
                                </div>
                                
                                <div class="flex gap-2">
                                    <a href="{{ route('coord.reports.show', $report) }}"
                                       class="flex-1 text-center px-4 py-2 bg-ojt-primary text-white rounded-lg text-sm font-medium hover:bg-maroon-700 transition-colors">
                                        View Details →
                                    </a>
                                    <a href="{{ route('coord.reports.pdf', $report) }}"
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Week Period</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($reports as $report)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($report->student->studentProfile && $report->student->studentProfile->profile_image)
                                                    <img src="{{ Storage::url($report->student->studentProfile->profile_image) }}" 
                                                         alt="{{ $report->student->name }}" 
                                                         class="w-10 h-10 rounded-full object-cover">
                                                @else
                                                    <div class="w-10 h-10 {{ $report->student->getAvatarColor() }} rounded-full flex items-center justify-center text-white text-sm font-bold">
                                                        {{ substr($report->student->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">{{ $report->student->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $report->student->studentProfile->student_id ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">Week {{ $report->week_number }}</div>
                                            <div class="text-xs text-gray-500">{{ $report->week_start_date->format('M d') }} - {{ $report->week_end_date->format('M d, Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $report->days_present }} days</div>
                                            <div class="text-xs text-gray-500">{{ number_format($report->total_hours, 2) }} hours</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium
                                                @if($report->status === 'reviewed') bg-green-100 text-green-800
                                                @elseif($report->status === 'submitted') bg-blue-100 text-blue-800
                                                @else bg-yellow-100 text-yellow-800
                                                @endif">
                                                {{ ucfirst($report->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $report->submitted_at ? $report->submitted_at->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                <a href="{{ route('coord.reports.show', $report) }}"
                                                   class="text-ojt-primary hover:text-maroon-700">
                                                    View
                                                </a>
                                                <span class="text-gray-300">|</span>
                                                <a href="{{ route('coord.reports.pdf', $report) }}"
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
                        {{ $reports->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-gray-500 text-lg">No weekly reports found</p>
                        @if(request('search'))
                            <p class="text-gray-400 text-sm mt-2">Try adjusting your search criteria</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
