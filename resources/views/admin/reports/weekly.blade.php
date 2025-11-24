<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-start gap-4 mb-6">
                <a href="{{ route('admin.reports.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors mt-1">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div class="flex-1 flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-ojt-dark">Weekly Reports</h1>
                        <p class="text-gray-600 mt-1">Monitor all intern weekly report submissions</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Total Reports</p>
                        <p class="text-2xl font-bold text-ojt-primary">{{ $reports->total() }}</p>
                    </div>
                </div>
            </div>

            <!-- Filter -->
            <div class="bg-white rounded-lg border p-4 mb-6 shadow-sm">
                <form method="GET" class="flex gap-4 items-end">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-ojt-primary" onchange="this.form.submit()">
                            <option value="">All Reports</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Review</option>
                            <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                        </select>
                    </div>
                    @if(request('status'))
                        <a href="{{ route('admin.reports.weekly') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300">Clear</a>
                    @endif
                </form>
            </div>

            <div class="bg-white rounded-lg border shadow-sm overflow-hidden">
                <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Week Period</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Intern</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coordinator</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($reports as $report)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">Week {{ $report->week_number }}</div>
                                        <div class="text-xs text-gray-500">{{ $report->week_start_date->format('M d') }} - {{ $report->week_end_date->format('M d, Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($report->student->studentProfile?->profile_image)
                                                <img src="{{ Storage::url($report->student->studentProfile->profile_image) }}" alt="{{ $report->student->name }}" class="w-10 h-10 rounded-full object-cover mr-3">
                                            @else
                                                <div class="w-10 h-10 {{ $report->student->getAvatarColor() }} rounded-full flex items-center justify-center text-white font-bold mr-3">
                                                    {{ substr($report->student->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $report->student->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $report->student->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $report->coordinator?->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-blue-600">{{ number_format($report->total_hours, 1) }}h</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($report->coordinator_reviewed_at)
                                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                ✓ Reviewed
                                            </span>
                                        @else
                                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                ⏳ Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $report->submitted_at ? $report->submitted_at->format('M d, Y') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">No weekly reports found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $reports->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
