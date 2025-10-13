<x-app-layout>

    <div class="py-6 sm:py-12" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="mb-8">
                <div class="flex items-center space-x-4 mb-4">
                    @if(Auth::user()->getProfile() && Auth::user()->getProfile()->profile_image)
                        <img src="{{ Storage::url(Auth::user()->getProfile()->profile_image) }}" alt="Profile" class="w-16 h-16 rounded-full object-cover border-2 border-ojt-primary">
                    @else
                        <div class="w-16 h-16 bg-ojt-primary rounded-full flex items-center justify-center text-white text-xl font-bold">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-ojt-dark mb-1">
                            Welcome back, {{ Auth::user()->name }}! 👋
                        </h1>
                        <p class="text-gray-600 capitalize">{{ Auth::user()->role }} Dashboard</p>
                    </div>
                </div>
                @if(Auth::user()->isStudent())
                    @if(Auth::user()->studentProfile && Auth::user()->studentProfile->ojt_status === 'active')
                        <p class="text-gray-600">Here's what's happening with your OJT internship today.</p>
                    @else
                        <p class="text-gray-600">Complete your OJT placement to start using the system features.</p>
                        <div class="mt-3">
                            <a href="{{ route('companies.index') }}" class="text-ojt-primary hover:text-maroon-700 underline">Browse companies</a>
                            <span class="mx-2">•</span>
                            <a href="{{ route('placements.create') }}" class="text-ojt-primary hover:text-maroon-700 underline">Notify acceptance</a>
                        </div>
                    @endif
                @else
                    <p class="text-gray-600">Here's what's happening in your OJT management system today.</p>
                @endif
            </div>

            <!-- Role-based Stats Cards -->
            @if(Auth::user()->isStudent())
                <!-- Student Dashboard Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- OJT Status -->
                    <div class="bg-gradient-to-br from-ojt-primary to-maroon-700 rounded-xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-ojt-accent/80 text-sm font-medium">OJT Status</p>
                                <p class="text-lg font-bold capitalize">{{ Auth::user()->studentProfile->ojt_status ?? 'Not Started' }}</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Completed Hours -->
                    <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Completed Hours</p>
                                <p class="text-2xl font-bold text-ojt-dark">{{ Auth::user()->getCompletedHours() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-ojt-accent/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-ojt-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Reports Submitted -->
                    <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Reports Submitted</p>
                                <p class="text-2xl font-bold text-ojt-dark">{{ Auth::user()->dailyReports()->count() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-ojt-success/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-ojt-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Percentage -->
                    <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Progress</p>
                                <p class="text-2xl font-bold text-ojt-dark">
                                    @php
                                        $completed = Auth::user()->getCompletedHours();
                                        $required = Auth::user()->getRequiredHours();
                                        $percentage = $required > 0 ? round(($completed / $required) * 100, 1) : 0;
                                    @endphp
                                    {{ $percentage }}%
                                </p>
                            </div>
                            <div class="w-12 h-12 bg-ojt-warning/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-ojt-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                        </div>
                        <!-- Progress Bar -->
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-ojt-primary to-ojt-accent h-2 rounded-full transition-all duration-300" 
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">{{ $completed }} / {{ $required }} hours</p>
                    </div>
                </div>

                <!-- Today's Attendance Status -->
                @if(Auth::user()->studentProfile && Auth::user()->studentProfile->ojt_status === 'active')
                    @php
                        $todayAttendance = Auth::user()->attendanceLogs()->where('work_date', today())->first();
                    @endphp
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-8">
                        <h3 class="text-lg font-semibold text-ojt-dark mb-4">Today's Attendance</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="text-center p-4 rounded-lg {{ $todayAttendance && $todayAttendance->time_in ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200' }}">
                                <div class="w-8 h-8 mx-auto mb-2 {{ $todayAttendance && $todayAttendance->time_in ? 'text-green-600' : 'text-yellow-600' }}">
                                    @if($todayAttendance && $todayAttendance->time_in)
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @else
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @endif
                                </div>
                                <p class="text-sm font-medium {{ $todayAttendance && $todayAttendance->time_in ? 'text-green-800' : 'text-yellow-800' }}">Time In</p>
                                <p class="text-lg font-bold {{ $todayAttendance && $todayAttendance->time_in ? 'text-green-900' : 'text-yellow-900' }}">
                                    {{ $todayAttendance && $todayAttendance->time_in ? $todayAttendance->time_in : 'Not recorded' }}
                                </p>
                            </div>
                            
                            <div class="text-center p-4 rounded-lg {{ $todayAttendance && $todayAttendance->time_out ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' }}">
                                <div class="w-8 h-8 mx-auto mb-2 {{ $todayAttendance && $todayAttendance->time_out ? 'text-green-600' : 'text-gray-400' }}">
                                    @if($todayAttendance && $todayAttendance->time_out)
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @else
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @endif
                                </div>
                                <p class="text-sm font-medium {{ $todayAttendance && $todayAttendance->time_out ? 'text-green-800' : 'text-gray-600' }}">Time Out</p>
                                <p class="text-lg font-bold {{ $todayAttendance && $todayAttendance->time_out ? 'text-green-900' : 'text-gray-500' }}">
                                    {{ $todayAttendance && $todayAttendance->time_out ? $todayAttendance->time_out : 'Not recorded' }}
                                </p>
                            </div>
                            
                            <div class="text-center p-4 rounded-lg bg-blue-50 border border-blue-200">
                                <div class="w-8 h-8 mx-auto mb-2 text-blue-600">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-blue-800">Hours Today</p>
                                <p class="text-lg font-bold text-blue-900">
                                    {{ $todayAttendance && $todayAttendance->minutes_worked ? round($todayAttendance->minutes_worked / 60, 1) : '0' }}h
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            @elseif(Auth::user()->isCoordinator())
                @php
                    $coordinator = Auth::user();
                    $department = $coordinator->coordinatorProfile?->department;
                    $program = $coordinator->coordinatorProfile?->program;
                    
                    // Real data calculations
                    $managedStudents = \App\Models\User::where('role', 'student')
                        ->whereHas('studentProfile', function($query) use ($department) {
                            $query->where('department', $department);
                        })->count();
                    
                    $pendingPlacements = \App\Models\PlacementRequest::whereHas('student', function($query) use ($department) {
                        $query->whereHas('studentProfile', function($q) use ($department) {
                            $q->where('department', $department);
                        });
                    })->where('status', 'pending')->count();
                    
                    $approvedPlacements = \App\Models\PlacementRequest::whereHas('student', function($query) use ($department) {
                        $query->whereHas('studentProfile', function($q) use ($department) {
                            $q->where('department', $department);
                        });
                    })->where('status', 'approved')->count();
                    
                    $activeCompanies = \App\Models\Company::where('department', $department)
                        ->where('status', 'active')->count();
                @endphp
                <!-- Coordinator Dashboard Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Managed Students -->
                    <div class="bg-gradient-to-br from-ojt-primary to-maroon-700 rounded-xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-ojt-accent/80 text-sm font-medium">Managed Students</p>
                                <p class="text-2xl font-bold">{{ $managedStudents }}</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Reviews -->
                    <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Pending Reviews</p>
                                <p class="text-2xl font-bold text-ojt-dark">{{ $pendingPlacements }}</p>
                            </div>
                            <div class="w-12 h-12 bg-ojt-warning/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-ojt-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Approved Placements -->
                    <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Approved Placements</p>
                                <p class="text-2xl font-bold text-ojt-dark">{{ $approvedPlacements }}</p>
                            </div>
                            <div class="w-12 h-12 bg-ojt-success/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-ojt-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Active Companies -->
                    <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Active Companies</p>
                                <p class="text-2xl font-bold text-ojt-dark">{{ $activeCompanies }}</p>
                            </div>
                            <div class="w-12 h-12 bg-ojt-accent/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-ojt-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(Auth::user()->isSupervisor())
                <!-- Supervisor Dashboard Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Supervised Students -->
                    <div class="bg-gradient-to-br from-ojt-primary to-maroon-700 rounded-xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-ojt-accent/80 text-sm font-medium">Supervised Students</p>
                                <p class="text-2xl font-bold">8</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Evaluations -->
                    <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Pending Evaluations</p>
                                <p class="text-2xl font-bold text-ojt-dark">3</p>
                            </div>
                            <div class="w-12 h-12 bg-ojt-warning/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-ojt-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Completed Evaluations -->
                    <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Completed Evaluations</p>
                                <p class="text-2xl font-bold text-ojt-dark">15</p>
                            </div>
                            <div class="w-12 h-12 bg-ojt-success/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-ojt-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Company Rating -->
                    <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Company Rating</p>
                                <p class="text-2xl font-bold text-ojt-dark">4.8</p>
                            </div>
                            <div class="w-12 h-12 bg-ojt-accent/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-ojt-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(Auth::user()->isAdmin())
                <!-- Admin Dashboard Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Users -->
                    <div class="bg-gradient-to-br from-ojt-primary to-maroon-700 rounded-xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-ojt-accent/80 text-sm font-medium">Total Users</p>
                                <p class="text-2xl font-bold">150</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Active Companies -->
                    <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Active Companies</p>
                                <p class="text-2xl font-bold text-ojt-dark">25</p>
                            </div>
                            <div class="w-12 h-12 bg-ojt-success/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-ojt-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- System Health -->
                    <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">System Health</p>
                                <p class="text-2xl font-bold text-ojt-dark">99%</p>
                            </div>
                            <div class="w-12 h-12 bg-ojt-accent/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-ojt-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Approvals -->
                    <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Pending Approvals</p>
                                <p class="text-2xl font-bold text-ojt-dark">5</p>
                            </div>
                            <div class="w-12 h-12 bg-ojt-warning/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-ojt-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Recent Activities -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-ojt-dark">Recent Activities</h3>
                        </div>
                        <div class="p-6">
                            @if(Auth::user()->isStudent())
                                @if(Auth::user()->studentProfile && Auth::user()->studentProfile->ojt_status === 'active')
                                    <!-- Active OJT Activities -->
                                    <div class="space-y-4">
                                        @php
                                            $recentAttendance = Auth::user()->attendanceLogs()->latest()->first();
                                            $recentReport = Auth::user()->dailyReports()->latest()->first();
                                            $todayAttendance = Auth::user()->attendanceLogs()->where('work_date', today())->first();
                                        @endphp
                                        
                                        @if($recentAttendance)
                                            <div class="flex items-start space-x-3">
                                                <div class="w-8 h-8 bg-ojt-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-4 h-4 text-ojt-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-ojt-dark">Last Time In: {{ $recentAttendance->time_in ?? 'Not recorded' }}</p>
                                                    <p class="text-xs text-gray-500">{{ $recentAttendance->work_date->format('M d, Y') }}</p>
                                                </div>
                                            </div>
                                        @endif

                                        @if($recentReport)
                                            <div class="flex items-start space-x-3">
                                                <div class="w-8 h-8 bg-ojt-success/10 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-4 h-4 text-ojt-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-ojt-dark">Last Daily Report Submitted</p>
                                                    <p class="text-xs text-gray-500">{{ $recentReport->work_date->format('M d, Y') }}</p>
                                                </div>
                                            </div>
                                        @endif

                                        @if(!$todayAttendance)
                                            <div class="flex items-start space-x-3">
                                                <div class="w-8 h-8 bg-ojt-warning/10 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-4 h-4 text-ojt-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-ojt-dark">Time-in required today</p>
                                                    <p class="text-xs text-gray-500">Don't forget to time in for your OJT today</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <!-- Pre-OJT Activities -->
                                    @php($latestPlacement = Auth::user()->placementRequests()->latest()->first())
                                    <div class="space-y-4">
                                        <div class="flex items-start space-x-3">
                                            <div class="w-8 h-8 bg-ojt-success/10 rounded-full flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4 text-ojt-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-ojt-dark">Profile Completed</p>
                                                <p class="text-xs text-gray-500">Your profile has been set up</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start space-x-3">
                                            <div class="w-8 h-8 bg-ojt-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4 text-ojt-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-ojt-dark">Browse Companies</p>
                                                <p class="text-xs text-gray-500">View approved companies for your department</p>
                                                <div class="mt-2">
                                                    <a href="{{ route('companies.index') }}" class="text-ojt-primary hover:text-maroon-700 underline">Open companies</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-start space-x-3">
                                            <div class="w-8 h-8 bg-ojt-warning/10 rounded-full flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4 text-ojt-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                @if(!$latestPlacement)
                                                    <p class="text-sm font-medium text-ojt-dark">OJT Placement Required</p>
                                                    <p class="text-xs text-gray-500">Apply to companies and get accepted to start OJT</p>
                                                    <div class="mt-2">
                                                        <a href="{{ route('placements.create') }}" class="text-ojt-primary hover:text-maroon-700 underline">Notify acceptance</a>
                                                    </div>
                                                @else
                                                    <p class="text-sm font-medium text-ojt-dark">Placement {{ ucfirst($latestPlacement->status) }}</p>
                                                    <p class="text-xs text-gray-500">
                                                        @if($latestPlacement->status === 'pending')
                                                            Your coordinator is reviewing your placement request.
                                                        @elseif($latestPlacement->status === 'declined')
                                                            Your last request was declined. You may submit another.
                                                        @endif
                                                    </p>
                                                    <div class="mt-2 space-x-3">
                                                        <a href="{{ route('placements.index') }}" class="text-ojt-primary hover:text-maroon-700 underline">View request</a>
                                                        <a href="{{ route('notifications.index') }}" class="text-ojt-primary hover:text-maroon-700 underline">Messages</a>
                                                        @if($latestPlacement->status === 'declined')
                                                            <a href="{{ route('placements.create') }}" class="text-ojt-primary hover:text-maroon-700 underline">Submit new</a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <!-- Other roles activities -->
                                <div class="space-y-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-8 h-8 bg-ojt-success/10 rounded-full flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-ojt-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-ojt-dark">System Active</p>
                                            <p class="text-xs text-gray-500">OJT360 system is running smoothly</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Role-based Quick Actions -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-ojt-dark">Quick Actions</h3>
                        </div>
                        <div class="p-6">
                            @if(Auth::user()->isStudent())
                                <!-- Student Quick Actions -->
                                <div class="space-y-3">
                                    @if(Auth::user()->studentProfile && Auth::user()->studentProfile->ojt_status === 'active')
                                        <!-- Active OJT Actions -->
                                        <a href="{{ route('attendance.index') }}" class="w-full bg-ojt-primary text-white py-3 px-4 rounded-lg font-medium hover:bg-maroon-700 transition-colors duration-200 flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Time In/Out
                                        </a>
                                        <a href="{{ route('reports.create') }}" class="w-full bg-white border border-ojt-primary text-ojt-primary py-3 px-4 rounded-lg font-medium hover:bg-ojt-primary hover:text-white transition-colors duration-200 flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Submit Report
                                        </a>
                                        <a href="{{ route('reports.index') }}" class="w-full bg-white border border-gray-300 text-gray-700 py-3 px-4 rounded-lg font-medium hover:bg-gray-50 transition-colors duration-200 flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            View Reports
                                        </a>
                                    @else
                                        <!-- Pre-OJT Actions -->
                                        <div class="text-center py-4">
                                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                            </div>
                                            <h4 class="text-sm font-medium text-gray-900 mb-1">OJT Not Started</h4>
                                            <p class="text-xs text-gray-500 mb-3">Complete your OJT placement to access features</p>
                                            <div class="text-xs text-gray-400 mb-4">
                                                <p>1. Browse companies</p>
                                                <p>2. Apply physically</p>
                                                <p>3. Get accepted</p>
                                                <p>4. Start OJT</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @elseif(Auth::user()->isCoordinator())
                                <!-- Coordinator Quick Actions -->
                                <div class="space-y-3">
                                    <a href="{{ route('coord.placements.inbox') }}" class="w-full bg-ojt-primary text-white py-3 px-4 rounded-lg font-medium hover:bg-maroon-700 transition-colors duration-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Review Placements
                                    </a>
                                    <a href="{{ route('companies.index') }}" class="w-full bg-white border border-ojt-primary text-ojt-primary py-3 px-4 rounded-lg font-medium hover:bg-ojt-primary hover:text-white transition-colors duration-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        Manage Companies
                                    </a>
                                    <a href="{{ route('coord.supervisors.index') }}" class="w-full bg-white border border-gray-300 text-gray-700 py-3 px-4 rounded-lg font-medium hover:bg-gray-50 transition-colors duration-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                        </svg>
                                        Manage Supervisors
                                    </a>
                                    <a href="{{ route('coord.students.index') }}" class="w-full bg-white border border-gray-300 text-gray-700 py-3 px-4 rounded-lg font-medium hover:bg-gray-50 transition-colors duration-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                        </svg>
                                        Manage Students
                                    </a>
                                </div>
                            @elseif(Auth::user()->isSupervisor())
                                <!-- Supervisor Quick Actions -->
                                <div class="space-y-3">
                                    <button class="w-full bg-ojt-primary text-white py-3 px-4 rounded-lg font-medium hover:bg-maroon-700 transition-colors duration-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                        </svg>
                                        Evaluate Students
                                    </button>
                                    <button class="w-full bg-white border border-ojt-primary text-ojt-primary py-3 px-4 rounded-lg font-medium hover:bg-ojt-primary hover:text-white transition-colors duration-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Submit Feedback
                                    </button>
                                    <button class="w-full bg-white border border-gray-300 text-gray-700 py-3 px-4 rounded-lg font-medium hover:bg-gray-50 transition-colors duration-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        Company Profile
                                    </button>
                                </div>
                            @elseif(Auth::user()->isAdmin())
                                <!-- Admin Quick Actions -->
                                <div class="space-y-3">
                                    <button class="w-full bg-ojt-primary text-white py-3 px-4 rounded-lg font-medium hover:bg-maroon-700 transition-colors duration-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                        </svg>
                                        Manage Users
                                    </button>
                                    <button class="w-full bg-white border border-ojt-primary text-ojt-primary py-3 px-4 rounded-lg font-medium hover:bg-ojt-primary hover:text-white transition-colors duration-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        Manage Companies
                                    </button>
                                    <button class="w-full bg-white border border-gray-300 text-gray-700 py-3 px-4 rounded-lg font-medium hover:bg-gray-50 transition-colors duration-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        System Settings
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
