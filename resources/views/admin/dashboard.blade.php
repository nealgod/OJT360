<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-ojt-dark mb-2">Admin Dashboard</h1>
                <p class="text-gray-600">Overview of system users and quick actions</p>
                <div class="mt-4">
                    <a href="/admin/users" class="inline-block bg-ojt-primary text-white px-6 py-2 rounded-lg hover:bg-maroon-700">
                        Go to Manage Users (Direct Link)
                    </a>
                    <a href="/admin/companies" class="inline-block ml-3 bg-white text-ojt-dark border border-gray-300 px-6 py-2 rounded-lg hover:bg-gray-50">
                        Go to Manage Companies
                    </a>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <!-- Total Users -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Total Users</p>
                            <p class="text-3xl font-bold text-ojt-dark">{{ $stats['total'] }}</p>
                        </div>
                        <div class="bg-gray-100 rounded-full p-3">
                            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Coordinators -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Coordinators</p>
                            <p class="text-3xl font-bold text-blue-600">{{ $stats['coordinators'] }}</p>
                        </div>
                        <div class="bg-blue-50 rounded-full p-3">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Supervisors -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Supervisors</p>
                            <p class="text-3xl font-bold text-green-600">{{ $stats['supervisors'] }}</p>
                        </div>
                        <div class="bg-green-50 rounded-full p-3">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Interns -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Total Interns</p>
                            <p class="text-3xl font-bold text-purple-600">{{ $stats['students'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $stats['active_interns'] }} active</p>
                        </div>
                        <div class="bg-purple-50 rounded-full p-3">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Hours -->
                <div class="bg-gradient-to-br from-ojt-primary to-maroon-700 rounded-lg p-6 text-white hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-white/80 mb-1">Total OJT Hours</p>
                            <p class="text-3xl font-bold">{{ number_format($stats['total_hours'], 1) }}</p>
                            <p class="text-xs text-white/70 mt-1">{{ $stats['total_attendance_logs'] }} logs</p>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Weekly Reports -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Weekly Reports</p>
                            <p class="text-3xl font-bold text-ojt-dark">{{ $stats['total_weekly_reports'] }}</p>
                            <p class="text-xs text-yellow-600 mt-1">{{ $stats['pending_weekly_reports'] }} pending</p>
                        </div>
                        <div class="bg-yellow-50 rounded-full p-3">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Evaluations -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Evaluations</p>
                            <p class="text-3xl font-bold text-ojt-dark">{{ $stats['total_monthly_evaluations'] + $stats['total_final_evaluations'] }}</p>
                            <p class="text-xs text-orange-600 mt-1">{{ $stats['pending_monthly_evaluations'] + $stats['pending_final_evaluations'] }} pending</p>
                        </div>
                        <div class="bg-orange-50 rounded-full p-3">
                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Active Companies -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Active Companies</p>
                            <p class="text-3xl font-bold text-ojt-dark">{{ $stats['active_companies'] }}</p>
                            <p class="text-xs text-gray-600 mt-1">of {{ $stats['total_companies'] }} total</p>
                        </div>
                        <div class="bg-teal-50 rounded-full p-3">
                            <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Active OJT Rate -->
                <div class="bg-gradient-to-br from-ojt-primary to-maroon-700 rounded-lg p-6 text-white hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-white/80 mb-1">Active OJT Rate</p>
                            <p class="text-3xl font-bold">
                                {{ ($stats['students'] ?? 0) > 0 ? round((($stats['active_interns'] ?? 0) / max($stats['students'], 1)) * 100) : 0 }}%
                            </p>
                            <p class="text-xs text-white/70 mt-1">{{ $stats['active_interns'] }} of {{ $stats['students'] }} interns active</p>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m4 4v-1a4 4 0 10-8 0v1m8 0h2m-10 0H5"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pending Approvals -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Pending Approvals</p>
                            <p class="text-3xl font-bold text-red-600">{{ $stats['pending_approvals'] }}</p>
                            <p class="text-xs text-red-500 mt-1">Recovery requests</p>
                        </div>
                        <div class="bg-red-50 rounded-full p-3">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- System Overview -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">System</p>
                            <p class="text-lg font-bold text-ojt-dark">{{ $stats['total_departments'] }} Departments</p>
                            <p class="text-xs text-gray-600 mt-1">{{ $stats['total_programs'] }} Programs</p>
                        </div>
                        <div class="bg-indigo-50 rounded-full p-3">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>
</x-app-layout>
