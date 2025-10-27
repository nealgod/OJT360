<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Generate Weekly Report</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-ojt-dark mb-2">Weekly Report Generator</h1>
                <p class="text-gray-600">Generate a comprehensive weekly report from your daily reports.</p>
            </div>

            <!-- Quick Generate Current Week -->
            @if($weeklyReports->count() > 0)
                <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-ojt-dark">Current Week ({{ $startOfWeek->format('M d') }} - {{ $endOfWeek->format('M d, Y') }})</h3>
                            <p class="text-sm text-gray-600">{{ $weeklyReports->count() }} reports • {{ $weeklyReports->where('status', 'approved')->count() }} approved</p>
                        </div>
                        <form id="currentWeekForm" method="POST" action="{{ route('reports.generate-weekly') }}">
                            @csrf
                            <input type="hidden" name="week_start" value="{{ $startOfWeek->format('Y-m-d') }}">
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                📄 Generate Weekly Report
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Week Selection Form -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-ojt-dark mb-4">Select Week to Generate Report</h3>
                
                <form method="POST" action="{{ route('reports.generate-weekly') }}" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label for="week_start" class="block text-sm font-medium text-gray-700 mb-2">Select Week</label>
                        <select name="week_start" id="week_start" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-ojt-primary focus:border-ojt-primary" required>
                            <option value="">Choose a week...</option>
                            @foreach($weeksWithReports as $week)
                                @php
                                    $weekStart = \Carbon\Carbon::parse($week->week_start);
                                    $weekEnd = $weekStart->copy()->endOfWeek();
                                    $reportCount = \App\Models\DailyReport::where('student_user_id', Auth::id())
                                        ->whereBetween('work_date', [$weekStart, $weekEnd])
                                        ->count();
                                @endphp
                                <option value="{{ $weekStart->format('Y-m-d') }}">
                                    Week of {{ $weekStart->format('M d') }} - {{ $weekEnd->format('M d, Y') }} ({{ $reportCount }} reports)
                                </option>
                            @endforeach
                        </select>
                        @error('week_start')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>


                    <div class="flex justify-between">
                        <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            Back to Reports
                        </a>
                        <button type="submit" class="px-6 py-2 bg-ojt-primary text-white rounded-lg hover:bg-maroon-700 transition-colors">
                            Generate Weekly Report
                        </button>
                    </div>
                </form>
            </div>

            <!-- Simple Instructions -->
            <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <h4 class="font-medium text-green-900 mb-2">💡 How it works</h4>
                <p class="text-sm text-green-800">Select a week with your daily reports and generate a professional weekly report. Download and use it for your "Weekly Accomplishment Report" document submission.</p>
            </div>
        </div>
    </div>
</x-app-layout>
