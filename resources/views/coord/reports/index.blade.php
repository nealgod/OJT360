<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                Weekly Reports
            </h2>
            <p class="text-sm text-gray-500">Review and manage student weekly reports</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <!-- Search -->
                <form method="GET" action="{{ route('coord.reports.index') }}" class="mb-6">
                    <div class="flex gap-3">
                        <div class="flex-1">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search by Student ID</label>
                            <input type="text" 
                                   name="search" 
                                   id="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Enter student ID..."
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-ojt-primary focus:ring-ojt-primary">
                        </div>
                        
                        <div class="flex items-end gap-2">
                            <button type="submit" class="px-6 py-2 bg-ojt-primary text-white rounded-lg hover:bg-maroon-700 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                            @if(request('search'))
                                <a href="{{ route('coord.reports.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

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

                <!-- Reports List -->
                @if($reports->count() > 0)
                    <div class="space-y-4">
                        @foreach($reports as $report)
                            <div class="border rounded-lg p-4 hover:border-ojt-primary/50 transition">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h3 class="text-lg font-semibold text-ojt-dark">
                                                {{ $report->student->name }}
                                            </h3>
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                                                @if($report->status === 'reviewed') bg-green-100 text-green-800
                                                @elseif($report->status === 'submitted') bg-blue-100 text-blue-800
                                                @else bg-yellow-100 text-yellow-800
                                                @endif">
                                                {{ ucfirst($report->status) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500 mb-2">
                                            {{ $report->student->studentProfile->student_id ?? 'N/A' }}
                                        </p>
                                        <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                            <span>Week {{ $report->week_number }}</span>
                                            <span>{{ $report->week_start_date->format('M d') }} - {{ $report->week_end_date->format('M d, Y') }}</span>
                                            <span>Present: <strong>{{ $report->days_present }}</strong></span>
                                            <span>Hours: <strong>{{ number_format($report->total_hours, 2) }}</strong></span>
                                        </div>
                                    </div>
                                    <div>
                                        <a href="{{ route('coord.reports.show', $report) }}"
                                           class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $reports->links() }}
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No weekly reports found.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
