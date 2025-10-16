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
                        <p class="text-gray-600">Browse approved companies and apply for your OJT placement.</p>
                        <div class="mt-3">
                            <a href="{{ route('companies.index') }}" class="text-ojt-primary hover:text-maroon-700 underline">Browse companies</a>
                            @if(!Auth::user()->placementRequests()->where('status', 'approved')->exists())
                                <span class="mx-2">•</span>
                                <a href="{{ route('placements.create') }}" class="text-ojt-primary hover:text-maroon-700 underline">Notify acceptance</a>
                            @endif
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
                                @php
                                    $completedMinutes = Auth::user()->attendanceLogs()->sum('minutes_worked');
                                    $completedHours = round(($completedMinutes ?? 0) / 60, 1);
                                @endphp
                                <p class="text-2xl font-bold text-ojt-dark">{{ $completedHours }}</p>
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
                                        $required = Auth::user()->getRequiredHours();
                                        $percentage = $required > 0 ? round(($completedHours / $required) * 100, 1) : 0;
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
                        <p class="text-xs text-gray-500 mt-2">{{ $completedHours }} / {{ $required }} hours</p>
                    </div>
                </div>

                <!-- Today's Attendance Status -->
                @if(Auth::user()->studentProfile && Auth::user()->studentProfile->ojt_status === 'active')
                    @php
                        $approvedPlacement = Auth::user()->placementRequests()
                            ->where('status', 'approved')
                            ->latest('decided_at')
                            ->first();
                    @endphp
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
                                    {{ $todayAttendance && $todayAttendance->time_in ? $todayAttendance->time_in_formatted : 'Not recorded' }}
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
                                    {{ $todayAttendance && $todayAttendance->time_out ? $todayAttendance->time_out_formatted : 'Not recorded' }}
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
                        
                        <!-- Incomplete Attendance Recovery -->
                        @php
                            $incompleteLogs = Auth::user()->attendanceLogs()
                                ->whereNotNull('time_in')
                                ->whereNull('time_out')
                                ->where('work_date', '<', today())
                                ->orderBy('work_date', 'desc')
                                ->get();
                        @endphp
                        
                        @if($incompleteLogs->count() > 0)
                            <div class="mt-4 bg-red-50 border border-red-200 p-6 rounded-lg">
                                <div class="flex items-center mb-4">
                                    <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                    <h4 class="text-lg font-semibold text-red-800">Incomplete Attendance Records</h4>
                                </div>
                                <p class="text-sm text-red-700 mb-4">
                                    You have {{ $incompleteLogs->count() }} incomplete attendance record(s) that need to be completed to receive credit for your work hours.
                                </p>
                                
                                <div class="space-y-3">
                                    @foreach($incompleteLogs as $log)
                                        <div class="bg-white border border-red-200 rounded-lg p-4">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-4">
                                                        <div class="text-sm">
                                                            <span class="font-medium text-gray-900">{{ $log->work_date->format('l, F j, Y') }}</span>
                                                            <span class="text-gray-500 ml-2">({{ $log->work_date->diffForHumans() }})</span>
                                                        </div>
                                                        <div class="text-sm text-gray-600">
                                                            <span class="font-medium">Time In:</span> {{ $log->time_in_formatted }}
                                                        </div>
                                                    </div>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        You timed in but forgot to time out on this day.
                                                    </p>
                                                </div>
                                                <button onclick="openRecoveryModal({{ $log->id }}, '{{ $log->work_date->format('Y-m-d') }}', '{{ $log->time_in }}')" 
                                                        class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors">
                                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Complete
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
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
                                                    <p class="text-sm font-medium text-ojt-dark">Last Time In: {{ $recentAttendance->time_in_formatted ?? 'Not recorded' }}</p>
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

                                        @php($recentMessage = Auth::user()->receivedMessages()->latest()->first())
                                        @if($recentMessage)
                                            <div class="flex items-start space-x-3">
                                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-ojt-dark">Latest Message from {{ $recentMessage->sender->name }}</p>
                                                    <p class="text-xs text-gray-500">{{ Str::limit($recentMessage->subject, 50) }}</p>
                                                    <div class="mt-1">
                                                        <a href="{{ route('messages.show', $recentMessage) }}" class="text-xs text-blue-600 hover:text-blue-800 underline">View message</a>
                                                    </div>
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
                                                @elseif($latestPlacement->status === 'approved')
                                                    <p class="text-sm font-medium text-ojt-dark">Placement Approved ✅</p>
                                                    <p class="text-xs text-gray-500">Your OJT placement has been approved. You can now start your internship.</p>
                                                    <div class="mt-2 space-x-3">
                                                        <a href="{{ route('placements.index') }}" class="text-ojt-primary hover:text-maroon-700 underline">View details</a>
                                                        <a href="{{ route('notifications.index') }}" class="text-ojt-primary hover:text-maroon-700 underline">Messages</a>
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
                                            <p class="text-xs text-gray-500 mb-3">Browse companies and apply for your OJT placement</p>
                                            <div class="text-xs text-gray-400 mb-4">
                                                <p>1. Browse companies</p>
                                                <p>2. Apply physically</p>
                                                <p>3. Get accepted</p>
                                                <p>4. Notify acceptance</p>
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
    
    <!-- Recovery Modal -->
    <div id="recoveryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900">Complete Missing Attendance</h3>
                    </div>
                    
                    <!-- Attendance Info -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Date:</span>
                                <span id="recoveryDate" class="text-gray-900 ml-2"></span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Time In:</span>
                                <span id="recoveryTimeIn" class="text-gray-900 ml-2"></span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            You timed in but forgot to time out on this day. Complete the form below to receive credit for your work hours.
                        </p>
                    </div>
                    
                    <form id="recoveryForm" enctype="multipart/form-data">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Time Out *</label>
                                <input type="time" id="recoveryTimeOut" name="time_out" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500" required />
                                <p class="text-xs text-gray-500 mt-1">Enter the time you actually left work</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reason *</label>
                                <textarea id="recoveryReason" name="reason" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500" placeholder="Explain why you couldn't time out normally (e.g., forgot to time out, system issue, emergency, etc.)" required></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Proof Photo *</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                                    <input type="file" id="recoveryPhoto" name="photo_out" accept="image/*" capture="environment" class="hidden" required />
                                    <div id="photoUploadArea" class="cursor-pointer">
                                        <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        </svg>
                                        <p class="text-sm text-gray-600">Click to upload proof photo</p>
                                        <p class="text-xs text-gray-500">Take a photo or upload from gallery</p>
                                    </div>
                                    <div id="photoPreview" class="hidden mt-2">
                                        <img id="previewImage" class="w-20 h-20 object-cover rounded mx-auto" />
                                        <p class="text-xs text-green-600 mt-1">Photo selected</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex space-x-3 mt-6">
                            <button type="button" onclick="closeRecoveryModal()" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="flex-1 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors flex items-center justify-center">
                                <span id="submitText">Complete Attendance</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        let currentLogId = null;
        
        function openRecoveryModal(logId, date, timeIn) {
            currentLogId = logId;
            document.getElementById('recoveryModal').classList.remove('hidden');
            
            // Set attendance info
            document.getElementById('recoveryDate').textContent = new Date(date).toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            document.getElementById('recoveryTimeIn').textContent = timeIn;
            
            // Set default time out (suggest 5:30 PM)
            document.getElementById('recoveryTimeOut').value = '17:30';
            
            // Reset form
            document.getElementById('recoveryForm').reset();
            document.getElementById('recoveryTimeOut').value = '17:30';
            document.getElementById('photoPreview').classList.add('hidden');
            document.getElementById('photoUploadArea').classList.remove('hidden');
        }
        
        function closeRecoveryModal() {
            document.getElementById('recoveryModal').classList.add('hidden');
            currentLogId = null;
        }
        
        // Photo upload handling
        document.getElementById('recoveryPhoto').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImage').src = e.target.result;
                    document.getElementById('photoPreview').classList.remove('hidden');
                    document.getElementById('photoUploadArea').classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Click to upload photo
        document.getElementById('photoUploadArea').addEventListener('click', function() {
            document.getElementById('recoveryPhoto').click();
        });
        
        // Form submission
        document.getElementById('recoveryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('log_id', currentLogId);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            // Validate required fields
            if (!formData.get('time_out')) {
                alert('Please enter your time out.');
                return;
            }
            
            if (!formData.get('reason') || formData.get('reason').trim() === '') {
                alert('Please provide a reason.');
                return;
            }
            
            if (!formData.get('photo_out')) {
                alert('Please upload a proof photo.');
                return;
            }
            
            if (confirm('Complete your attendance for this day?')) {
                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const submitText = document.getElementById('submitText');
                const originalText = submitText.textContent;
                submitText.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Uploading proof...';
                submitBtn.disabled = true;
                
                fetch('{{ route("attendance.recovery") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Attendance completed successfully! Your hours have been recorded.');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                        submitText.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                    submitText.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
            }
        });
        
        // Close modal when clicking outside
        document.getElementById('recoveryModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRecoveryModal();
            }
        });
    </script>
</x-app-layout>
