<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('coord.students.index') }}" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Student Details</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Student Header -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-8">
                <div class="flex items-start space-x-6">
                    <!-- Student Avatar -->
                    <div class="flex-shrink-0">
                        @if($student->getProfile() && $student->getProfile()->profile_image)
                            <img class="h-20 w-20 rounded-full object-cover border-4 border-ojt-primary" 
                                 src="{{ Storage::url($student->getProfile()->profile_image) }}" 
                                 alt="{{ $student->name }}">
                        @else
                            <div class="h-20 w-20 rounded-full bg-ojt-primary flex items-center justify-center border-4 border-ojt-primary">
                                <span class="text-white font-bold text-2xl">{{ substr($student->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Student Info -->
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $student->name }}</h1>
                        <p class="text-gray-600">Student ID: {{ $student->studentProfile?->student_id ?? 'N/A' }}</p>
                        <p class="text-gray-600">{{ $student->studentProfile?->course ?? 'N/A' }}</p>
                        <p class="text-gray-600">{{ $student->studentProfile?->department ?? 'N/A' }}</p>
                        
                        @php
                            $status = $student->studentProfile?->ojt_status ?? 'pending';
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'active' => 'bg-green-100 text-green-800',
                                'completed' => 'bg-blue-100 text-blue-800'
                            ];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-800' }} mt-2">
                            {{ ucfirst($status) }}
                        </span>
                    </div>

                    <!-- Quick Actions -->
                    <div class="flex-shrink-0">
                        <div class="flex space-x-3">
                            <form method="POST" action="{{ route('coord.students.update-status', $student) }}" class="inline">
                                @csrf
                                <select name="ojt_status" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary">
                                    <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- OJT Progress -->
                    @if($student->studentProfile && $student->studentProfile->ojt_status === 'active')
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">OJT Progress</h3>
                            @php
                                $completed = $student->getCompletedHours();
                                $required = $student->getRequiredHours();
                                $percentage = $required > 0 ? round(($completed / $required) * 100, 1) : 0;
                            @endphp
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-700">Progress</span>
                                    <span class="text-sm font-bold text-ojt-primary">{{ $percentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-ojt-primary to-ojt-accent h-3 rounded-full transition-all duration-300" 
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>{{ $completed }} hours completed</span>
                                    <span>{{ $required }} hours required</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Recent Activity -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                        <div class="space-y-4">
                            @if($student->attendanceLogs->count() > 0)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Recent Attendance</h4>
                                    <div class="space-y-2">
                                        @foreach($student->attendanceLogs->take(5) as $attendance)
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="text-gray-600">{{ $attendance->work_date?->format('M d, Y') ?? 'N/A' }}</span>
                                                <span class="text-gray-900">
                                                    {{ $attendance->time_in_formatted ?? 'No time-in' }}
                                                    @if($attendance->time_out)
                                                        - {{ $attendance->time_out_formatted }}
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($student->dailyReports->count() > 0)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Recent Reports</h4>
                                    <div class="space-y-2">
                                        @foreach($student->dailyReports->take(5) as $report)
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="text-gray-600">{{ $report->work_date?->format('M d, Y') ?? 'N/A' }}</span>
                                                <span class="text-gray-900">{{ Str::limit($report->summary, 50) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Company Assignment -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Company Assignment</h3>
                        <form method="POST" action="{{ route('coord.students.update-company', $student) }}">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Assigned Company</label>
                                    <select name="company_id" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary">
                                        <option value="">Not Assigned</option>
                                        @foreach($availableCompanies as $company)
                                            <option value="{{ $company->id }}" {{ $student->studentProfile?->assigned_company_id == $company->id ? 'selected' : '' }}>
                                                {{ $company->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Required Hours</label>
                                    <input type="number" 
                                           name="required_hours" 
                                           value="{{ $student->studentProfile?->required_hours ?? $student->getRequiredHours() }}"
                                           min="1" 
                                           max="1000"
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary">
                                    <p class="text-xs text-gray-500 mt-1">Leave empty to use default for course</p>
                                </div>
                                
                                <button type="submit" class="w-full bg-ojt-primary text-white py-2 px-4 rounded-md text-sm font-medium hover:bg-maroon-700 transition-colors duration-200">
                                    Update Assignment
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Placement History -->
                    @if($student->placementRequests->count() > 0)
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Placement History</h3>
                            <div class="space-y-3">
                                @foreach($student->placementRequests as $placement)
                                    <div class="border-l-4 border-gray-200 pl-3">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-900">
                                                {{ $placement->company?->name ?? $placement->external_company_name }}
                                            </span>
                                            <span class="text-xs px-2 py-1 rounded-full {{ 
                                                $placement->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                                ($placement->status == 'declined' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')
                                            }}">
                                                {{ ucfirst($placement->status) }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500">{{ $placement->created_at?->format('M d, Y') ?? 'N/A' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
