<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                    Audit Logs
                </h2>
                <p class="text-sm text-gray-500 mt-1">System activity and user actions tracking</p>
            </div>
            <a href="{{ route('admin.audit.export', request()->all()) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors font-medium inline-flex items-center justify-center text-sm sm:text-base">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="hidden sm:inline">Export CSV</span>
                <span class="sm:hidden">Export</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Bulk Actions & Delete Old Logs -->
            <div class="flex flex-col sm:flex-row gap-3 mb-6 justify-between items-center">
                <form id="bulkDeleteForm" method="POST" action="{{ route('admin.audit.bulk-delete') }}" class="flex-1" onsubmit="return handleBulkDelete(event)">
                    @csrf
                    <div class="flex gap-2">
                        <button type="submit" id="bulkDeleteBtn" disabled class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors font-medium text-sm inline-flex items-center disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span id="bulkDeleteText">Delete Selected</span>
                        </button>
                    </div>
                </form>

                <button onclick="document.getElementById('deleteOldModal').classList.remove('hidden')" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 hover:text-ojt-primary transition-colors font-medium text-sm inline-flex items-center shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Delete Old Logs
                </button>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
                <div class="bg-white rounded-lg border border-gray-200 p-3 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs sm:text-sm text-gray-500 mb-1 truncate">Total Logs</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-2 sm:p-3 flex-shrink-0 ml-2">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-3 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs sm:text-sm text-gray-500 mb-1 truncate">Today</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ number_format($stats['today']) }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-2 sm:p-3 flex-shrink-0 ml-2">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-3 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs sm:text-sm text-gray-500 mb-1 truncate">This Week</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ number_format($stats['this_week']) }}</p>
                        </div>
                        <div class="bg-purple-100 rounded-full p-2 sm:p-3 flex-shrink-0 ml-2">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-3 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs sm:text-sm text-gray-500 mb-1 truncate">This Month</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ number_format($stats['this_month']) }}</p>
                        </div>
                        <div class="bg-orange-100 rounded-full p-2 sm:p-3 flex-shrink-0 ml-2">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg border p-4 mb-6 shadow-sm">
                <form method="GET" class="space-y-4">
                    <!-- Quick Filter Presets -->
                    <div class="flex flex-wrap gap-2 pb-4 border-b">
                        <span class="text-xs sm:text-sm font-medium text-gray-700 mr-2 self-center">Quick Filters:</span>
                        <a href="{{ route('admin.audit.index', array_merge(request()->except('preset', 'date_from', 'date_to'), ['preset' => 'today'])) }}" 
                           class="px-2 sm:px-3 py-1 text-xs rounded-md {{ request('preset') == 'today' ? 'bg-ojt-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Today
                        </a>
                        <a href="{{ route('admin.audit.index', array_merge(request()->except('preset', 'date_from', 'date_to'), ['preset' => 'week'])) }}" 
                           class="px-2 sm:px-3 py-1 text-xs rounded-md {{ request('preset') == 'week' ? 'bg-ojt-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            This Week
                        </a>
                        <a href="{{ route('admin.audit.index', array_merge(request()->except('preset', 'date_from', 'date_to'), ['preset' => 'month'])) }}" 
                           class="px-2 sm:px-3 py-1 text-xs rounded-md {{ request('preset') == 'month' ? 'bg-ojt-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            This Month
                        </a>
                    </div>
                    <input type="hidden" name="preset" value="{{ request('preset') }}">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs sm:text-sm font-medium mb-1">User</label>
                            <select name="user_id" class="w-full border rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium mb-1">Role</label>
                            <select name="role" class="w-full border rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary">
                                <option value="">All Roles</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>
                                        {{ ucfirst($role) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium mb-1">Action</label>
                            <select name="action" class="w-full border rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary">
                                <option value="">All Actions</option>
                                @foreach($actions as $action)
                                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                        {{ ucfirst($action) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium mb-1">Model Type</label>
                            <select name="model_type" class="w-full border rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary">
                                <option value="">All Models</option>
                                @foreach($modelTypes as $modelType)
                                    <option value="{{ $modelType }}" {{ request('model_type') == $modelType ? 'selected' : '' }}>
                                        {{ class_basename($modelType) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs sm:text-sm font-medium mb-1">From Date</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary">
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium mb-1">To Date</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary">
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium mb-1">Search Description</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="w-full border rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary">
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                        <button type="submit" class="bg-ojt-primary text-white px-4 py-2 rounded-md hover:bg-maroon-700 transition-colors font-medium text-sm inline-flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Filter
                        </button>
                        <a href="{{ route('admin.audit.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors font-medium text-sm text-center">
                            Clear Filters
                        </a>
                    </div>
                </form>
            </div>

            <!-- Logs Table -->
            <div class="bg-white rounded-lg border overflow-hidden shadow-sm">
                <div class="overflow-x-auto max-h-[560px] overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    <input type="checkbox" id="selectAll" onchange="toggleAll(this)" class="rounded border-gray-300 text-ojt-primary focus:ring-ojt-primary">
                                </th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('admin.audit.index', array_merge(request()->all(), ['sort_by' => 'created_at', 'sort_order' => $sortBy == 'created_at' && $sortOrder == 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center hover:text-gray-700">
                                        Time
                                        @if($sortBy == 'created_at')
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortOrder == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                            </svg>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('admin.audit.index', array_merge(request()->all(), ['sort_by' => 'action', 'sort_order' => $sortBy == 'action' && $sortOrder == 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center hover:text-gray-700">
                                        Action
                                        @if($sortBy == 'action')
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortOrder == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                            </svg>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ route('admin.audit.index', array_merge(request()->all(), ['sort_by' => 'model_type', 'sort_order' => $sortBy == 'model_type' && $sortOrder == 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center hover:text-gray-700">
                                        Model
                                        @if($sortBy == 'model_type')
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortOrder == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                            </svg>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">IP Address</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-4 whitespace-nowrap" onclick="event.stopPropagation()">
                                        <input type="checkbox" name="log_ids[]" value="{{ $log->id }}" class="log-checkbox rounded border-gray-300 text-ojt-primary focus:ring-ojt-primary" onchange="updateBulkDeleteButton()">
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900 cursor-pointer" onclick="window.location='{{ route('admin.audit.show', $log) }}'">
                                        <div class="font-medium">{{ $log->created_at->format('M d, Y') }}</div>
                                        <div class="text-gray-500 text-xs">{{ $log->created_at->format('H:i:s') }}</div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm cursor-pointer" onclick="window.location='{{ route('admin.audit.show', $log) }}'">
                                        @if($log->user)
                                            <a href="{{ route('admin.audit.user', $log->user) }}" class="font-medium text-gray-900 hover:text-ojt-primary" onclick="event.stopPropagation()">
                                                {{ $log->user->name }}
                                            </a>
                                            <div class="text-gray-500 text-xs">{{ ucfirst($log->user->role) }}</div>
                                        @else
                                            <span class="font-medium text-gray-900">System</span>
                                        @endif
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap cursor-pointer" onclick="window.location='{{ route('admin.audit.show', $log) }}'">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                            @if($log->action === 'created') bg-green-100 text-green-800
                                            @elseif($log->action === 'updated') bg-blue-100 text-blue-800
                                            @elseif($log->action === 'deleted') bg-red-100 text-red-800
                                            @elseif($log->action === 'login' || $log->action === 'logout') bg-purple-100 text-purple-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($log->action) }}
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500 cursor-pointer" onclick="window.location='{{ route('admin.audit.show', $log) }}'">
                                        @if($log->model_type)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ class_basename($log->model_type) }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-900 cursor-pointer" onclick="window.location='{{ route('admin.audit.show', $log) }}'">
                                        <div class="max-w-xs lg:max-w-md truncate" title="{{ $log->description }}">
                                            {{ $log->description }}
                                        </div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell cursor-pointer" onclick="window.location='{{ route('admin.audit.show', $log) }}'">
                                        <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $log->ip_address ?? '—' }}</code>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="text-gray-500">
                                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <p class="text-base sm:text-lg font-medium">No audit logs found</p>
                                            <p class="text-sm mt-1">Try adjusting your filters</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $logs->links() }}
            </div>
        </div>
    <!-- Delete Old Logs Modal -->
    <div id="deleteOldModal" class="hidden fixed inset-0 bg-gray-200 bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md border-2 border-gray-300">
            <div class="p-6 border-b bg-white">
                <h3 class="text-xl font-bold text-gray-800">Delete Logs from Past X Days</h3>
                <p class="text-sm text-gray-600 mt-1">Remove recent audit log records</p>
            </div>
            <form action="{{ route('admin.audit.delete-older-than') }}" method="POST" class="p-6" onsubmit="return confirm('Are you sure? This will delete recent logs and keep older ones.')">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Delete logs from the past</label>
                    <div class="relative">
                        <input type="number" name="days" min="1" max="365" value="30" required class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary pr-12">
                        <span class="absolute right-4 top-2.5 text-gray-500 font-medium">days</span>
                    </div>
                    <p class="text-xs text-gray-700 mt-2 bg-yellow-50 p-3 rounded border-l-4 border-yellow-400">
                        <span class="font-bold">⚠️ Warning:</span> This will delete all logs from the <strong>past X days</strong> (keeping older records). This action cannot be undone.
                    </p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('deleteOldModal').classList.add('hidden')" class="flex-1 bg-white border-2 border-gray-300 text-gray-700 px-4 py-2.5 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 bg-red-600 text-white px-4 py-2.5 rounded-lg hover:bg-red-700 transition-colors font-medium flex justify-center items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Logs
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleAll(source) {
            const checkboxes = document.getElementsByClassName('log-checkbox');
            for(let i=0; i<checkboxes.length; i++) {
                checkboxes[i].checked = source.checked;
            }
            updateBulkDeleteButton();
        }

        function updateBulkDeleteButton() {
            const checkboxes = document.querySelectorAll('.log-checkbox:checked');
            const btn = document.getElementById('bulkDeleteBtn');
            const text = document.getElementById('bulkDeleteText');
            
            if(checkboxes.length > 0) {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                text.textContent = `Delete Selected (${checkboxes.length})`;
            } else {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                text.textContent = 'Delete Selected';
            }
        }

        function handleBulkDelete(event) {
            event.preventDefault();
            const checkboxes = document.querySelectorAll('.log-checkbox:checked');
            const count = checkboxes.length;
            
            if (count === 0) return false;
            
            if (!confirm(`Are you sure you want to delete ${count} selected log(s)? This action cannot be undone.`)) {
                return false;
            }

            const form = document.getElementById('bulkDeleteForm');
            
            // Remove any existing hidden inputs to avoid duplicates
            const existingInputs = form.querySelectorAll('input[name="log_ids[]"]');
            existingInputs.forEach(input => input.remove());

            // Append new hidden inputs for each checked box
            checkboxes.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'log_ids[]';
                input.value = checkbox.value;
                form.appendChild(input);
            });

            form.submit();
        }

        // Close modal on outside click
        window.onclick = function(event) {
            const modal = document.getElementById('deleteOldModal');
            if (event.target == modal) {
                modal.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>
