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
                        <h1 class="text-3xl font-bold text-ojt-dark">Attendance Logs</h1>
                        <p class="text-gray-600 mt-1">View and filter all intern attendance records</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Total Records</p>
                        <p class="text-2xl font-bold text-ojt-primary">{{ $logs->total() }}</p>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg border p-6 mb-6 shadow-sm">
                <h3 class="font-semibold text-gray-700 mb-4">Filters</h3>
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search Intern</label>
                        <select name="user_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary">
                            <option value="">All Interns</option>
                            @foreach($interns as $intern)
                                <option value="{{ $intern->id }}" {{ request('user_id') == $intern->id ? 'selected' : '' }}>
                                    {{ $intern->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="bg-ojt-primary text-white px-6 py-2 rounded-lg hover:bg-maroon-700 transition-colors">
                            Apply Filters
                        </button>
                        @if(request()->hasAny(['user_id', 'date_from', 'date_to']))
                            <a href="{{ route('admin.reports.attendance') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg border shadow-sm overflow-hidden">
                <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Intern</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time In</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time Out</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $log->work_date->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $log->work_date->format('l') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($log->user->studentProfile?->profile_image)
                                                <img src="{{ Storage::url($log->user->studentProfile->profile_image) }}" alt="{{ $log->user->name }}" class="w-10 h-10 rounded-full object-cover mr-3">
                                            @else
                                                <div class="w-10 h-10 {{ $log->user->getAvatarColor() }} rounded-full flex items-center justify-center text-white font-bold mr-3">
                                                    {{ substr($log->user->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $log->user->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $log->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">{{ $log->time_in_formatted }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">{{ $log->time_out_formatted ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-ojt-primary">{{ number_format(($log->minutes_worked ?? 0) / 60, 1) }}h</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($log->is_recovered)
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                Recovered
                                            </span>
                                        @elseif($log->time_out)
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                Complete
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                                Incomplete
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">No attendance logs found</p>
                                        <p class="text-xs text-gray-400 mt-1">Try adjusting your filters</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $logs->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
