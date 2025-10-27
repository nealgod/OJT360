<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Weekly Report Preview</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-ojt-dark">Weekly Accomplishment Report</h1>
                    <p class="text-gray-600">{{ $weekStart->format('M d, Y') }} - {{ $weekEnd->format('M d, Y') }}</p>
                </div>
                <div class="flex gap-3">
                    <form method="POST" action="{{ route('reports.submit-weekly') }}" class="inline">
                        @csrf
                        <input type="hidden" name="week_start" value="{{ $weekStart->format('Y-m-d') }}">
                        <button type="submit" class="bg-ojt-primary text-white px-4 py-2 rounded-lg hover:bg-maroon-700 transition-colors">
                            📤 Submit to Documents
                        </button>
                    </form>
                    <form method="POST" action="{{ route('reports.download-weekly') }}" class="inline">
                        @csrf
                        <input type="hidden" name="week_start" value="{{ $weekStart->format('Y-m-d') }}">
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            📥 Download Report
                        </button>
                    </form>
                    <a href="{{ route('reports.weekly') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </a>
                </div>
            </div>

            <div id="weeklyReportContent" class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="prose max-w-none">
                    <h2 class="text-xl font-bold text-ojt-dark mb-4">WEEKLY ACCOMPLISHMENT REPORT</h2>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Student Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div><strong>Name:</strong> {{ Auth::user()->name }}</div>
                            <div><strong>Student ID:</strong> {{ Auth::user()->studentProfile?->student_id ?? 'N/A' }}</div>
                            <div><strong>Program:</strong> {{ Auth::user()->studentProfile?->course ?? 'N/A' }}</div>
                            <div><strong>Department:</strong> {{ Auth::user()->studentProfile?->department ?? 'N/A' }}</div>
                            <div><strong>Company:</strong> {{ Auth::user()->studentProfile?->company?->name ?? 'N/A' }}</div>
                            <div><strong>Week Period:</strong> {{ $weekStart->format('M d, Y') }} - {{ $weekEnd->format('M d, Y') }}</div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Daily Activities Summary</h3>
                        <div class="space-y-4">
                            @foreach($reports as $report)
                                <div class="border-l-4 border-ojt-primary pl-4 py-2">
                                    <div class="flex justify-between items-start mb-1">
                                        <h4 class="font-medium text-gray-900">{{ $report->work_date->format('l, M d, Y') }}</h4>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $report->status === 'approved' ? 'bg-green-100 text-green-800' : ($report->status === 'returned' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-700">{{ $report->summary }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Weekly Summary</h3>
                        <div class="bg-gray-50 p-3 rounded text-sm">
                            <div class="font-medium">Total Hours Worked This Week: <span class="text-ojt-primary font-bold">{{ number_format($totalHours, 2) }} hours</span></div>
                        </div>
                    </div>

                    <div class="text-xs text-gray-500 mt-8 pt-4 border-t">
                        <p><strong>Generated on:</strong> {{ now()->format('M d, Y g:i A') }}</p>
                        <p><strong>Report Period:</strong> {{ $weekStart->format('M d, Y') }} - {{ $weekEnd->format('M d, Y') }}</p>
                        <p><strong>Total Daily Reports:</strong> {{ $totalDays }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-medium text-blue-900 mb-2">💡 Quick Actions</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• <strong>Submit to Documents:</strong> Automatically submits this report to your "Weekly Accomplishment Report" document requirement for coordinator review.</li>
                    <li>• <strong>Download Report:</strong> Download the report for your personal records.</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
