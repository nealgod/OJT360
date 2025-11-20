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
                <a href="{{ route('reports.weekly.pdf', $report) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Download PDF
                </a>
                <a href="{{ route('reports.weekly.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white rounded-lg shadow hover:bg-maroon-700">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">
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

