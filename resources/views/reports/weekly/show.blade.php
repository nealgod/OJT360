<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                    Weekly Report – Week {{ $report->week_number }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ $report->week_start_date->format('M d') }} - {{ $report->week_end_date->format('M d, Y') }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                @if($report->status === 'draft')
                    <form method="POST" action="{{ route('reports.weekly.submit', $report) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Submit Report
                        </button>
                    </form>
                    <form method="POST" action="{{ route('reports.weekly.destroy', $report) }}" class="inline" 
                          onsubmit="return confirm('Are you sure you want to delete this weekly report? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete
                        </button>
                    </form>
                @endif
                <a href="{{ route('reports.weekly.pdf', ['weekly' => $report->id, 'view' => 1]) }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    View PDF
                </a>
                <a href="{{ route('reports.weekly.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white rounded-lg shadow hover:bg-maroon-700 transition">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Status Badge -->
            @if($report->status !== 'draft')
                <div class="bg-white shadow sm:rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Report Status</p>
                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold
                                @if($report->status === 'reviewed') bg-green-100 text-green-800
                                @elseif($report->status === 'submitted') bg-blue-100 text-blue-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ ucfirst($report->status) }}
                            </span>
                        </div>
                        @if($report->submitted_at)
                            <p class="text-sm text-gray-600">Submitted on {{ $report->submitted_at->format('M d, Y g:i A') }}</p>
                        @endif
                    </div>
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Days Present</p>
                        <p class="text-2xl font-semibold text-green-600">{{ $report->days_present }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Days Absent</p>
                        <p class="text-2xl font-semibold text-red-500">{{ $report->days_absent }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Days Late</p>
                        <p class="text-2xl font-semibold text-yellow-500">{{ $report->days_late }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Hours</p>
                        <p class="text-2xl font-semibold text-blue-600">{{ number_format($report->total_hours, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-ojt-dark mb-4">Daily Activities</h3>
                <div class="border rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-gray-600">Date</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-600">Activities / Job Work</th>
                                <th class="px-4 py-2 text-right font-medium text-gray-600">Hours</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($report->entries_for_display as $entry)
                                <tr>
                                    <td class="px-4 py-2 text-gray-700">{{ $entry['date'] }}</td>
                                    <td class="px-4 py-2 text-gray-700">{{ $entry['activity'] }}</td>
                                    <td class="px-4 py-2 text-right text-gray-700">{{ $entry['hours'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($report->problems_encountered)
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Problems Encountered</h4>
                    <p class="text-gray-800">{{ $report->problems_encountered }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>



