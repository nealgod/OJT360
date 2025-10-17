<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Placement Requests Inbox</h2>
            <div class="flex items-center space-x-4">
                <span class="bg-ojt-warning/10 text-ojt-warning px-3 py-1 rounded-full text-sm font-medium">
                    {{ $requests->count() }} Pending
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc ml-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if($requests->count() > 0)
                <!-- Filter and Sort Options -->
                <div class="mb-6 bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600">Filter by:</span>
                            <form method="GET" action="{{ route('coord.placements.inbox') }}" class="inline">
                                <select name="filter" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary">
                                    <option value="all" {{ ($filter ?? 'all') == 'all' ? 'selected' : '' }}>All Requests</option>
                                    <option value="recent" {{ ($filter ?? 'all') == 'recent' ? 'selected' : '' }}>Recent (Last 7 days)</option>
                                    <option value="company" {{ ($filter ?? 'all') == 'company' ? 'selected' : '' }}>Listed Companies</option>
                                    <option value="external" {{ ($filter ?? 'all') == 'external' ? 'selected' : '' }}>External Companies</option>
                                </select>
                                @if(isset($sort))
                                    <input type="hidden" name="sort" value="{{ $sort }}">
                                @endif
                            </form>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Sort by:</span>
                            <form method="GET" action="{{ route('coord.placements.inbox') }}" class="inline">
                                <select name="sort" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary">
                                    <option value="newest" {{ ($sort ?? 'newest') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                    <option value="oldest" {{ ($sort ?? 'newest') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                    <option value="name" {{ ($sort ?? 'newest') == 'name' ? 'selected' : '' }}>Student Name</option>
                                    <option value="company" {{ ($sort ?? 'newest') == 'company' ? 'selected' : '' }}>Company Name</option>
                                </select>
                                @if(isset($filter))
                                    <input type="hidden" name="filter" value="{{ $filter }}">
                                @endif
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Placement Requests Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($requests as $req)
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200 flex flex-col h-full">
                            <!-- Student Header -->
                            <div class="p-6 border-b border-gray-100">
                                <div class="flex items-start space-x-4">
                                    <!-- Student Avatar -->
                                    <div class="flex-shrink-0">
                                        @if($req->student->getProfile() && $req->student->getProfile()->profile_image)
                                            <img src="{{ Storage::url($req->student->getProfile()->profile_image) }}" 
                                                 alt="{{ $req->student->name }}" 
                                                 class="w-12 h-12 rounded-full object-cover border-2 border-ojt-primary">
                                        @else
                                            <div class="w-12 h-12 bg-ojt-primary rounded-full flex items-center justify-center text-white font-bold text-lg">
                                                {{ substr($req->student->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Student Info -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-semibold text-ojt-dark truncate">{{ $req->student->name }}</h3>
                                        <p class="text-sm text-gray-600">{{ $req->student->studentProfile?->student_id ?? 'No ID' }}</p>
                                        <p class="text-sm text-gray-500">{{ $req->student->studentProfile?->course ?? 'No Course' }}</p>
                                    </div>
                                    
                                    <!-- Status Badge -->
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-ojt-warning/10 text-ojt-warning">
                                            Pending
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Request Details -->
                            <div class="p-6 flex-1">
                                <!-- Company Information -->
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Company Details</h4>
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        @if($req->company)
                                            <div class="flex items-center space-x-2">
                                                <svg class="w-4 h-4 text-ojt-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                <span class="font-medium text-ojt-dark">{{ $req->company->name }}</span>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1">{{ $req->company->address }}</p>
                                            @if($req->position_title)
                                                <p class="text-sm text-gray-600 mt-1"><span class="font-medium">Position:</span> {{ $req->position_title }}</p>
                                            @endif
                                        @else
                                            <div class="flex items-center space-x-2">
                                                <svg class="w-4 h-4 text-ojt-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                <span class="font-medium text-ojt-dark">{{ $req->external_company_name }}</span>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1">{{ $req->external_company_address }}</p>
                                            @if($req->position_title)
                                                <p class="text-sm text-gray-600 mt-1"><span class="font-medium">Position:</span> {{ $req->position_title }}</p>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                <!-- Request Information -->
                                <div class="space-y-3 mb-4">
                                    @if($req->start_date)
                                        <div class="flex items-center justify-between bg-blue-50 rounded-lg p-3 border border-blue-200">
                                            <div class="flex items-center space-x-2">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span class="text-sm font-medium text-blue-800">Internship Start Date</span>
                                            </div>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                                {{ $req->start_date->format('M d, Y') }}
                                            </span>
                                        </div>
                                    @endif

                                    @if($req->shift_start || $req->shift_end)
                                        <div class="flex items-center justify-between bg-amber-50 rounded-lg p-3 border border-amber-200">
                                            <div class="flex items-center space-x-2">
                                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="text-sm font-medium text-amber-800">Declared Shift</span>
                                            </div>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-amber-100 text-amber-800">
                                                {{ $req->shift_start ? \Carbon\Carbon::parse($req->shift_start)->format('g:i A') : '—' }}
                                                <span class="mx-1">–</span>
                                                {{ $req->shift_end ? \Carbon\Carbon::parse($req->shift_end)->format('g:i A') : '—' }}
                                            </span>
                                        </div>
                                    @endif

                                    @if(!is_null($req->break_minutes))
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-sm text-gray-600">Break Time: <span class="font-medium">{{ $req->break_minutes }} min</span></span>
                                        </div>
                                    @endif

                                    @if(is_array($req->working_days) && count($req->working_days) > 0)
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="text-sm text-gray-600">Work Days:
                                                <span class="font-medium">
                                                    {{ collect($req->working_days)->map(function($d){
                                                        $map = ['mon'=>'Mon','tue'=>'Tue','wed'=>'Wed','thu'=>'Thu','fri'=>'Fri','sat'=>'Sat','sun'=>'Sun'];
                                                        return $map[$d] ?? $d;
                                                    })->join(', ') }}
                                                </span>
                                            </span>
                                        </div>
                                    @endif
                                    
                                    @if($req->contact_person)
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span class="text-sm text-gray-600">Contact: <span class="font-medium">{{ $req->contact_person }}</span></span>
                                        </div>
                                    @endif
                                    
                                    @if($req->supervisor_name)
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                            </svg>
                                            <span class="text-sm text-gray-600">Supervisor: <span class="font-medium">{{ $req->supervisor_name }}</span></span>
                                        </div>
                                    @endif
                                    
                                    @if($req->supervisor_email)
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            <span class="text-sm text-gray-600">Email: <span class="font-medium">{{ $req->supervisor_email }}</span></span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Notes -->
                                @if($req->note)
                                    <div class="mb-4">
                                        <h4 class="text-sm font-medium text-gray-900 mb-2">Notes</h4>
                                        <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3">{{ $req->note }}</p>
                                    </div>
                                @endif

                                <!-- Proof Document -->
                                @if($req->proof_path)
                                    <div class="mb-4">
                                        <h4 class="text-sm font-medium text-gray-900 mb-2">Proof Document</h4>
                                        <div class="bg-gray-50 rounded-lg p-3">
                                            <a href="{{ Storage::url($req->proof_path) }}" 
                                               target="_blank" 
                                               class="inline-flex items-center space-x-2 text-ojt-primary hover:text-maroon-700 text-sm font-medium"
                                               title="{{ basename($req->proof_path) }}">
                                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span class="truncate max-w-xs">
                                                    @php
                                                        $filename = basename($req->proof_path);
                                                        $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
                                                        $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
                                                        
                                                        // Truncate filename if too long (keep extension)
                                                        if (strlen($nameWithoutExt) > 20) {
                                                            $truncatedName = substr($nameWithoutExt, 0, 20) . '...';
                                                        } else {
                                                            $truncatedName = $nameWithoutExt;
                                                        }
                                                        
                                                        $displayName = $truncatedName . ($fileExtension ? '.' . $fileExtension : '');
                                                    @endphp
                                                    {{ $displayName }}
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                @endif

                                <!-- Request Date -->
                                <div class="text-xs text-gray-500 mb-4">
                                    Submitted {{ $req->created_at->diffForHumans() }}
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex-shrink-0">
                                <div class="space-y-4">
                                    <!-- Approve Section -->
                                    <div class="bg-white rounded-lg p-4 border border-green-200">
                                        <h5 class="text-sm font-medium text-green-800 mb-3 flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Approve Placement
                                        </h5>
                                        <form method="POST" action="{{ route('coord.placements.approve', $req) }}">
                                            @csrf
                                            <div class="space-y-3">
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div>
                                                        <label for="start_date_{{ $req->id }}" class="block text-xs font-medium text-gray-700 mb-1">Start Date</label>
                                                        <input type="date" id="start_date_{{ $req->id }}" name="start_date" min="{{ date('Y-m-d') }}" value="{{ $req->start_date ? $req->start_date->format('Y-m-d') : '' }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500" required />
                                                    </div>
                                                    <div>
                                                        <label for="break_minutes_{{ $req->id }}" class="block text-xs font-medium text-gray-700 mb-1">Break Time (minutes)</label>
                                                        <input type="number" id="break_minutes_{{ $req->id }}" name="break_minutes" min="0" max="240" value="{{ $req->break_minutes ?? 60 }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500" />
                                                    </div>
                                                </div>
                                                
                                                <!-- Shift Times -->
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div>
                                                        <label for="shift_start_{{ $req->id }}" class="block text-xs font-medium text-gray-700 mb-1">Shift Start</label>
                                                        <input type="time" id="shift_start_{{ $req->id }}" name="shift_start" value="{{ $req->shift_start ?? '08:00' }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500" required />
                                                    </div>
                                                    <div>
                                                        <label for="shift_end_{{ $req->id }}" class="block text-xs font-medium text-gray-700 mb-1">Shift End</label>
                                                        <input type="time" id="shift_end_{{ $req->id }}" name="shift_end" value="{{ $req->shift_end ?? '17:00' }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500" required />
                                                    </div>
                                                </div>
                                                
                                                <!-- Working Days -->
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-2">Working Days</label>
                                                    <div class="grid grid-cols-4 gap-2">
                                                        @php
                                                            $workingDays = $req->working_days ?? ['mon', 'tue', 'wed', 'thu', 'fri'];
                                                        @endphp
                                                        <label class="flex items-center space-x-1">
                                                            <input type="checkbox" name="working_days[]" value="mon" class="rounded border-gray-300 text-green-600 focus:ring-green-500" {{ in_array('mon', $workingDays) ? 'checked' : '' }}>
                                                            <span class="text-xs">Mon</span>
                                                        </label>
                                                        <label class="flex items-center space-x-1">
                                                            <input type="checkbox" name="working_days[]" value="tue" class="rounded border-gray-300 text-green-600 focus:ring-green-500" {{ in_array('tue', $workingDays) ? 'checked' : '' }}>
                                                            <span class="text-xs">Tue</span>
                                                        </label>
                                                        <label class="flex items-center space-x-1">
                                                            <input type="checkbox" name="working_days[]" value="wed" class="rounded border-gray-300 text-green-600 focus:ring-green-500" {{ in_array('wed', $workingDays) ? 'checked' : '' }}>
                                                            <span class="text-xs">Wed</span>
                                                        </label>
                                                        <label class="flex items-center space-x-1">
                                                            <input type="checkbox" name="working_days[]" value="thu" class="rounded border-gray-300 text-green-600 focus:ring-green-500" {{ in_array('thu', $workingDays) ? 'checked' : '' }}>
                                                            <span class="text-xs">Thu</span>
                                                        </label>
                                                        <label class="flex items-center space-x-1">
                                                            <input type="checkbox" name="working_days[]" value="fri" class="rounded border-gray-300 text-green-600 focus:ring-green-500" {{ in_array('fri', $workingDays) ? 'checked' : '' }}>
                                                            <span class="text-xs">Fri</span>
                                                        </label>
                                                        <label class="flex items-center space-x-1">
                                                            <input type="checkbox" name="working_days[]" value="sat" class="rounded border-gray-300 text-green-600 focus:ring-green-500" {{ in_array('sat', $workingDays) ? 'checked' : '' }}>
                                                            <span class="text-xs">Sat</span>
                                                        </label>
                                                        <label class="flex items-center space-x-1">
                                                            <input type="checkbox" name="working_days[]" value="sun" class="rounded border-gray-300 text-green-600 focus:ring-green-500" {{ in_array('sun', $workingDays) ? 'checked' : '' }}>
                                                            <span class="text-xs">Sun</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0 mt-3">
                                                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-md text-sm font-medium hover:bg-green-700 transition-colors duration-200 flex items-center whitespace-nowrap">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Approve
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    
                                    <!-- Decline Section -->
                                    <div class="bg-white rounded-lg p-4 border border-red-200">
                                        <h5 class="text-sm font-medium text-red-800 mb-3 flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Decline Placement
                                        </h5>
                                        <form method="POST" action="{{ route('coord.placements.decline', $req) }}">
                                            @csrf
                                            <div class="flex items-end gap-3">
                                                <div class="flex-1 min-w-0">
                                                    <label for="reason_{{ $req->id }}" class="block text-xs font-medium text-gray-700 mb-1">Reason for Decline</label>
                                                    <input type="text" 
                                                           id="reason_{{ $req->id }}"
                                                           name="reason" 
                                                           placeholder="Enter reason for declining..." 
                                                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-red-500 focus:border-red-500" 
                                                           required />
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <button type="submit" 
                                                            class="bg-red-600 text-white px-6 py-2 rounded-md text-sm font-medium hover:bg-red-700 transition-colors duration-200 flex items-center whitespace-nowrap">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        Decline
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $requests->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Pending Requests</h3>
                        <p class="text-gray-500 mb-4">All placement requests have been reviewed.</p>
                        <a href="{{ route('companies.index') }}" class="text-ojt-primary hover:text-maroon-700 font-medium">
                            Manage Companies →
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
