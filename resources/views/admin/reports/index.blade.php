<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6 sm:mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-ojt-dark">Reports & Analytics</h1>
                <p class="text-gray-600 mt-1">System-wide statistics and insights</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
                <div class="bg-white rounded-lg border p-4">
                    <p class="text-sm text-gray-600">Total Users</p>
                    <p class="text-2xl font-bold text-ojt-dark">{{ $stats['total_users'] }}</p>
                </div>
                <div class="bg-white rounded-lg border p-4">
                    <p class="text-sm text-gray-600">Active Interns</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['active_interns'] }}</p>
                </div>
                <div class="bg-white rounded-lg border p-4">
                    <p class="text-sm text-gray-600">Total Hours Logged</p>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_hours'], 1) }}</p>
                </div>
                <div class="bg-white rounded-lg border p-4">
                    <p class="text-sm text-gray-600">Weekly Reports</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['total_weekly_reports'] }}</p>
                </div>
                <div class="bg-white rounded-lg border p-4">
                    <p class="text-sm text-gray-600">Companies</p>
                    <p class="text-2xl font-bold">{{ $stats['total_companies'] }}</p>
                </div>
                <div class="bg-white rounded-lg border p-4">
                    <p class="text-sm text-gray-600">Coordinators</p>
                    <p class="text-2xl font-bold">{{ $stats['total_coordinators'] }}</p>
                </div>
                <div class="bg-white rounded-lg border p-4">
                    <p class="text-sm text-gray-600">Supervisors</p>
                    <p class="text-2xl font-bold">{{ $stats['total_supervisors'] }}</p>
                </div>
                <div class="bg-white rounded-lg border p-4">
                    <p class="text-sm text-gray-600">Evaluations</p>
                    <p class="text-2xl font-bold">{{ $stats['total_monthly_evaluations'] + $stats['total_final_evaluations'] }}</p>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 sm:mb-8">
                <a href="{{ route('admin.reports.attendance') }}" class="bg-white border-2 border-gray-200 rounded-xl p-6 hover:border-ojt-primary hover:shadow-md transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg text-ojt-dark mb-1">Attendance Logs</h3>
                            <p class="text-sm text-gray-600">View all attendance records</p>
                        </div>
                    </div>
                </a>
                <a href="{{ route('admin.reports.weekly') }}" class="bg-white border-2 border-gray-200 rounded-xl p-6 hover:border-ojt-primary hover:shadow-md transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg text-ojt-dark mb-1">Weekly Reports</h3>
                            <p class="text-sm text-gray-600">View all weekly reports</p>
                        </div>
                    </div>
                </a>
                <a href="{{ route('admin.reports.evaluations') }}" class="bg-white border-2 border-gray-200 rounded-xl p-6 hover:border-ojt-primary hover:shadow-md transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg text-ojt-dark mb-1">Evaluations</h3>
                            <p class="text-sm text-gray-600">View all evaluations</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Top Interns -->
            <div class="bg-white rounded-lg border shadow-sm p-6 mb-6 sm:mb-8">
                <h3 class="text-lg font-semibold text-ojt-dark mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-ojt-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    Top Interns by Hours
                </h3>
                <div class="space-y-2">
                    @foreach($topInterns as $intern)
                        <div class="flex justify-between items-center py-2 border-b">
                            <span>{{ $intern->name }}</span>
                            <span class="font-semibold">{{ number_format(($intern->total_minutes ?? 0) / 60, 1) }} hrs</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Monthly Trends -->
            <div class="bg-white rounded-lg border shadow-sm p-6">
                <h3 class="text-lg font-semibold text-ojt-dark mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-ojt-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Monthly Trends (Last 6 Months)
                </h3>
                <div class="space-y-3">
                    @foreach($monthlyData as $data)
                        <div class="flex justify-between items-center">
                            <span class="font-medium">{{ \Carbon\Carbon::parse($data->month . '-01')->format('F Y') }}</span>
                            <div class="text-sm text-gray-600">
                                {{ $data->active_interns }} interns | {{ number_format($data->total_hours, 1) }} hours
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
