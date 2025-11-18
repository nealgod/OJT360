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
                            <div class="h-20 w-20 rounded-full {{ $student->getAvatarColor() }} flex items-center justify-center border-4 border-gray-200">
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

            @php
                $milestones = [
                    ['label' => 'Pre-Placement', 'complete' => (bool) $student->studentProfile?->preplacement_complete, 'note' => $student->studentProfile?->preplacement_complete ? 'Checklist done' : 'Waiting submissions'],
                    ['label' => 'Company', 'complete' => (bool) $derivedCompanyName, 'note' => $derivedCompanyName ?? 'Not assigned'],
                    ['label' => 'Supervisor', 'complete' => (bool) $student->studentProfile?->supervisor_id, 'note' => $student->studentProfile?->supervisor?->name ?? 'Not assigned'],
                    ['label' => 'Activation', 'complete' => $student->studentProfile?->ojt_status === 'active', 'note' => ucfirst($student->studentProfile?->ojt_status ?? 'Pending')],
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                @foreach($milestones as $milestone)
                    <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">{{ $milestone['label'] }}</p>
                            <p class="text-sm font-semibold text-ojt-dark">
                                {{ $milestone['complete'] ? 'Complete' : 'Pending' }}
                            </p>
                            @if(!empty($milestone['note']))
                                <p class="text-xs text-gray-500 mt-1">{{ $milestone['note'] }}</p>
                            @endif
                        </div>
                        <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $milestone['complete'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            @if($milestone['complete'])
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Attendance Overview -->
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Attendance Overview</h3>
                                <p class="text-sm text-gray-500">Latest logs with photos and punctuality checks.</p>
                                </div>
                            <div class="flex items-center gap-4 text-xs text-gray-600">
                                <div><span class="font-semibold text-ojt-dark">{{ $attendanceStats['total_days'] }}</span> days logged</div>
                                <div><span class="font-semibold text-green-600">{{ $attendanceStats['completed_days'] }}</span> completed</div>
                                <div><span class="font-semibold text-yellow-600">{{ $attendanceStats['missing_checkout'] }}</span> pending out</div>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Date</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Time In</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Time Out</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Photos</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($student->attendanceLogs as $log)
                                        @php
                                            $shiftStart = $placementRequest?->shift_start;
                                            $late = false;
                                            if ($shiftStart && $log->time_in) {
                                                try {
                                                    $late = \Carbon\Carbon::createFromFormat('H:i:s', $log->time_in)
                                                        ->gt(\Carbon\Carbon::createFromFormat('H:i:s', $shiftStart));
                                                } catch (\Exception $e) {
                                                    $late = false;
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            <td class="px-3 py-2 text-gray-900">{{ $log->work_date?->format('M d, Y') ?? '—' }}</td>
                                            <td class="px-3 py-2 text-gray-700">{{ $log->time_in_formatted ?? '—' }}</td>
                                            <td class="px-3 py-2 text-gray-700">{{ $log->time_out_formatted ?? '—' }}</td>
                                            <td class="px-3 py-2">
                                                <div class="flex items-center gap-2">
                                                    @if($log->photo_in_path)
                                                        <a href="{{ Storage::url($log->photo_in_path) }}" target="_blank" class="inline-flex items-center px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Time In</a>
                    @endif
                                                    @if($log->photo_out_path)
                                                        <a href="{{ Storage::url($log->photo_out_path) }}" target="_blank" class="inline-flex items-center px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Time Out</a>
                                                    @endif
                                            </div>
                                            </td>
                                            <td class="px-3 py-2">
                                                @if(!$log->time_in)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Missing Time-In</span>
                                                @elseif(!$log->time_out)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Needs Time-Out</span>
                                                @elseif($late)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">Late</span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">On Time</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-3 py-4 text-center text-sm text-gray-500">No attendance logs yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                                    </div>
                                </div>

                    <!-- Reports Overview -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                                <div>
                                <h3 class="text-lg font-semibold text-gray-900">Reports Overview</h3>
                                <p class="text-sm text-gray-500">Recent submissions with quick access.</p>
                                            </div>
                            <div class="flex items-center gap-4 text-xs text-gray-600">
                                <div><span class="font-semibold text-ojt-dark">{{ $reportStats['total_reports'] }}</span> total</div>
                                <div><span class="font-semibold text-ojt-dark">{{ $reportStats['this_week'] }}</span> this week</div>
                                    </div>
                                </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Date</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Summary</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($student->dailyReports as $report)
                                        <tr>
                                            <td class="px-3 py-2 text-gray-900">{{ $report->work_date?->format('M d, Y') ?? '—' }}</td>
                                            <td class="px-3 py-2 text-gray-700">{{ Str::limit($report->summary, 80) ?: 'No summary provided' }}</td>
                                            <td class="px-3 py-2">
                                                <a href="{{ route('reports.show', $report) }}" target="_blank" class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-xs font-medium">
                                                    View Report
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-3 py-4 text-center text-sm text-gray-500">No reports submitted yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Company & Supervisor Summary -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Placement Summary</h3>
                            @if($companySource)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    {{ ucfirst($companySource) }}
                                </span>
                            @endif
                        </div>
                        <div class="space-y-4 text-sm text-gray-700">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Company</p>
                                <p class="mt-1 font-medium text-ojt-dark">
                                    {{ $derivedCompanyName ?? 'Not assigned' }}
                                </p>
                                @if($derivedCompanyAddress)
                                    <p class="text-xs text-gray-500">{{ $derivedCompanyAddress }}</p>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 gap-3 text-xs text-gray-500 border-t border-gray-100 pt-4">
                                <div class="flex items-center justify-between">
                                    <span class="uppercase tracking-wide">Hours Completed</span>
                                    <span class="text-sm text-ojt-dark font-semibold">
                                        {{ number_format($student->studentProfile?->completed_hours ?? 0) }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="uppercase tracking-wide">Required Hours</span>
                                    <span class="text-sm text-ojt-dark font-semibold">
                                        @if($acceptance?->total_hours)
                                            {{ number_format($acceptance->total_hours) }}
                                        @elseif($student->studentProfile?->required_hours)
                                            {{ number_format($student->studentProfile->required_hours) }}
                                        @else
                                            —
                                        @endif
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="uppercase tracking-wide">Activation</span>
                                    <span class="text-sm text-ojt-dark font-semibold">
                                        {{ ucfirst($student->studentProfile?->ojt_status ?? 'pending') }}
                                    </span>
                                </div>
                            </div>
                            @if($student->studentProfile?->supervisor)
                                <div class="border-t border-gray-100 pt-4">
                                    <p class="text-xs uppercase tracking-wide text-gray-500">Supervisor</p>
                                    <p class="mt-1 font-medium text-ojt-dark">{{ $student->studentProfile->supervisor->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $student->studentProfile->supervisor->email }}</p>
                                @else
                                    <div class="border-t border-gray-100 pt-4">
                                        <p class="text-xs uppercase tracking-wide text-gray-500">Supervisor</p>
                                        <p class="mt-1 text-gray-400">Not assigned</p>
                                    </div>
                            @endif
                            </div>
                        </div>
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

                    <!-- Supervisor Assignment Section -->
                    <div id="supervisor-assignment" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6" x-data="{ open: {{ $student->studentProfile?->supervisor ? 'false' : 'true' }} }">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Supervisor Assignment</h3>
                            <div class="flex items-center gap-2">
                                @if($student->studentProfile?->supervisor)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">✓ Assigned</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">⚠ Pending</span>
                                @endif
                                <button type="button" @click="open = !open" class="inline-flex items-center px-2.5 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <span x-show="!open">Show</span>
                                    <span x-show="open">Hide</span>
                                </button>
                            </div>
                        </div>

                        <!-- Current assignment -->
                        <div class="mb-4" x-show="open">
                            @if($student->studentProfile?->supervisor)
                        <div class="bg-ojt-accent/10 border border-ojt-accent/30 rounded-lg p-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-ojt-accent font-medium">Supervisor:</p>
                            <p class="text-sm text-ojt-dark">{{ $student->studentProfile->supervisor->name }}</p>
                            <p class="text-xs text-ojt-dark/70">{{ $student->studentProfile->supervisor->email }}</p>
                                    </div>
                                    @if($student->studentProfile->supervisor->supervisorProfile?->company)
                                        <div class="text-right text-xs text-ojt-dark/70">
                                            <p class="font-medium">{{ $student->studentProfile->supervisor->supervisorProfile->company->name }}</p>
                                            <p>{{ $student->studentProfile->supervisor->supervisorProfile->company->address }}</p>
                                        </div>
                                    @endif
                                </div>
                                </div>
                            @else
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <p class="text-sm text-gray-600">No supervisor assigned yet</p>
                                    <p class="text-xs text-gray-500 mt-1">Student can submit supervisor details, or you can assign an existing supervisor.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Student-submitted details -->
                        @if(isset($latestProposal) && $latestProposal)
                                <div class="bg-ojt-accent/10 border border-ojt-accent/30 rounded-lg p-3 mb-4" x-show="open">
                                <p class="text-sm text-ojt-accent font-medium mb-2">📝 Supervisor Details Submitted by Student:</p>
                                <p class="text-sm text-ojt-dark"><strong>Name:</strong> {{ $latestProposal->proposed_name ?? 'Not provided' }}</p>
                                <p class="text-sm text-ojt-dark"><strong>Email:</strong> {{ $latestProposal->proposed_email ?? 'Not provided' }}</p>
                                @if($latestProposal->notes)
                                    <button type="button" onclick="document.getElementById('proposalNotes').classList.toggle('hidden')" class="mt-2 text-xs text-ojt-accent underline">Show notes</button>
                                    <div id="proposalNotes" class="hidden mt-2 text-xs text-ojt-dark bg-ojt-accent/10 p-2 rounded">{{ $latestProposal->notes }}</div>
                                @endif
                            </div>
                        @endif

                        <!-- Assignment Options -->
                        <div class="space-y-3" x-show="open">
                            <!-- Option 1: Create from student proposal or placement info -->
                            @php
                                $hasSupervisorInfo = false;
                                $supervisorName = null;
                                $supervisorEmail = null;
                                
                                if (isset($latestProposal) && $latestProposal && $latestProposal->proposed_name && $latestProposal->proposed_email) {
                                    $hasSupervisorInfo = true;
                                    $supervisorName = $latestProposal->proposed_name;
                                    $supervisorEmail = $latestProposal->proposed_email;
                                } elseif (isset($placementSupervisorInfo) && $placementSupervisorInfo) {
                                    $hasSupervisorInfo = true;
                                    $supervisorName = $placementSupervisorInfo->proposed_name;
                                    $supervisorEmail = $placementSupervisorInfo->proposed_email;
                                }
                            @endphp
                            
                            @if($hasSupervisorInfo)
                                <div class="border border-blue-200 rounded-lg p-3">
                                    <h4 class="text-sm font-medium text-ojt-dark mb-2">Option 1: Create Supervisor Account</h4>
                                    <p class="text-xs text-gray-600 mb-2">Create a new supervisor account using the details submitted by the student.</p>
                                    <div class="text-xs text-gray-700 mb-3 space-y-1">
                                        <p><strong>Name:</strong> {{ $supervisorName }}</p>
                                        <p><strong>Email:</strong> {{ $supervisorEmail }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('coord.students.assign-supervisor', $student) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="action" value="create_from_proposal">
                                        <button type="submit" class="bg-ojt-primary text-white px-3 py-1 rounded text-sm hover:bg-maroon-700 transition-colors">
                                            Create Account & Assign
                                        </button>
                                    </form>
                                </div>
                            @endif

                            <!-- Option 2: Assign existing supervisor -->
                            <div class="border border-gray-200 rounded-lg p-3">
                                <h4 class="text-sm font-medium text-ojt-dark mb-2">Option 2: Assign Existing Supervisor</h4>
                                <form method="POST" action="{{ route('coord.students.assign-supervisor', $student) }}">
                                    @csrf
                                    <input type="hidden" name="action" value="assign_existing">
                                    <div class="flex items-end gap-3">
                                        <div class="flex-1">
                                            <select name="supervisor_id" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary" {{ ($studentCompanyId) ? '' : 'disabled' }}>
                                                @if(!$studentCompanyId && !$externalCompanyName)
                                                    <option value="">No company assigned</option>
                                                @elseif($externalCompanyName)
                                                    <option value="">External placement: create supervisor from proposal</option>
                                                @else
                                                    @if(isset($eligibleSupervisors) && count($eligibleSupervisors) === 0)
                                                        <option value="">No supervisors available</option>
                                                    @else
                                                        <option value="">Select a supervisor</option>
                                                        @foreach($eligibleSupervisors as $sup)
                                                            <option value="{{ $sup->id }}" {{ $student->studentProfile?->supervisor_id == $sup->id ? 'selected' : '' }}>
                                                                {{ $sup->name }}@if(!empty($sup->email)) ({{ $sup->email }})@endif
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                @endif
                                            </select>
                                        </div>
                                        <button type="submit" class="bg-ojt-primary text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-maroon-700 transition-colors" {{ ($studentCompanyId) ? '' : 'disabled' }}>
                                            Assign
                                        </button>
                                    </div>
                                </form>
                                <p class="text-xs text-gray-500 mt-2">
                                    @if($studentCompanyId)
                                        Only supervisors from {{ $student->studentProfile->company->name }} are shown.
                                    @elseif($externalCompanyName)
                                        For external placements, please use Option 1 to create the supervisor from the student's proposal (or add the company to the system first).
                                    @else
                                        Student needs an assigned company first.
                                    @endif
                                </p>
                            </div>

                            <!-- Option 3: Create new supervisor -->
                            @if($studentCompanyId)
                                <div class="border border-gray-200 rounded-lg p-3">
                                    <h4 class="text-sm font-medium text-ojt-dark mb-2">Option 3: Create New Supervisor</h4>
                                    <p class="text-xs text-gray-600 mb-3">Create a new supervisor account for {{ $student->studentProfile->company->name }}.</p>
                                    <a href="{{ route('coord.supervisors.create', ['company_id' => $studentCompanyId]) }}" class="inline-flex items-center px-3 py-1 bg-ojt-primary text-white rounded text-sm hover:bg-maroon-700 transition-colors">
                                        Create New Supervisor
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
