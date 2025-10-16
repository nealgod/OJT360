<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-ojt-dark leading-tight">My Placement</h2>
            @php
                $isApprovedActive = optional(Auth::user()->studentProfile)->ojt_status === 'active' || !is_null(optional($placement)->id);
            @endphp
            <div class="flex items-center gap-2">
                @unless($isApprovedActive)
                    <a href="{{ route('placements.index') }}" class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">View Requests</a>
                @endunless
                <a href="{{ route('companies.index') }}" class="inline-flex items-center px-3 py-2 bg-ojt-primary text-white text-sm font-medium rounded-lg hover:bg-maroon-700 transition-colors duration-200">Browse Companies</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                @if($placement)
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-ojt-dark">{{ $placement->company?->name ?? $placement->external_company_name ?? '—' }}</h3>
                            <p class="text-xs text-gray-500">Your latest approved placement</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-ojt-success/10 text-ojt-success">Approved</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="flex items-center justify-between bg-blue-50 rounded-lg p-3 border border-blue-200">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-sm font-medium text-blue-800">Start Date</span>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">{{ $placement->start_date?->format('M d, Y') ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between bg-amber-50 rounded-lg p-3 border border-amber-200">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm font-medium text-amber-800">Declared Shift</span>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-amber-100 text-amber-800">{{ $placement->shift_start ? \Carbon\Carbon::parse($placement->shift_start)->format('g:i A') : '—' }}<span class="mx-1">–</span>{{ $placement->shift_end ? \Carbon\Carbon::parse($placement->shift_end)->format('g:i A') : '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between bg-green-50 rounded-lg p-3 border border-green-200">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm font-medium text-green-800">Break Time</span>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">{{ $placement->break_minutes !== null ? ($placement->break_minutes . ' min') : '—' }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <p class="text-sm text-gray-600">Company</p>
                            <p class="text-base font-semibold text-ojt-dark">
                                {{ $placement->company?->name ?? $placement->external_company_name ?? '—' }}
                            </p>
                            @if(!$placement->company)
                                <p class="text-sm text-gray-500 mt-1">{{ $placement->external_company_address ?? '—' }}</p>
                            @endif
                            <p class="text-sm text-gray-600 mt-2"><span class="font-medium">Position:</span> {{ $placement->position_title ?? '—' }}</p>
                            <p class="text-sm text-gray-600 mt-2"><span class="font-medium">Contact Person:</span> {{ $placement->contact_person ?? '—' }}</p>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Start Date</p>
                                    <p class="text-base font-semibold text-ojt-dark">{{ $placement->start_date?->format('M d, Y') ?? '—' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Break</p>
                                    <p class="text-base font-semibold text-ojt-dark">{{ $placement->break_minutes ?? '—' }}{{ $placement->break_minutes !== null ? ' min' : '' }}</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <p class="text-sm text-gray-600">Shift</p>
                                <p class="text-base font-semibold text-ojt-dark">
                                    {{ $placement->shift_start ? \Carbon\Carbon::parse($placement->shift_start)->format('g:i A') : '—' }}
                                    <span class="mx-1">–</span>
                                    {{ $placement->shift_end ? \Carbon\Carbon::parse($placement->shift_end)->format('g:i A') : '—' }}
                                </p>
                            </div>
                            <div class="mt-3">
                                <p class="text-sm text-gray-600">Work Days</p>
                                <p class="text-base font-semibold text-ojt-dark">
                                    @if(is_array($placement->working_days) && count($placement->working_days) > 0)
                                        {{ collect($placement->working_days)->map(function($d){
                                            $map = ['mon'=>'Mon','tue'=>'Tue','wed'=>'Wed','thu'=>'Thu','fri'=>'Fri','sat'=>'Sat','sun'=>'Sun'];
                                            return $map[$d] ?? $d;
                                        })->join(', ') }}
                                    @else
                                        —
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Supervisor</h4>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            @if($hasAssignedSupervisor && $assignedSupervisor)
                                <p class="text-sm text-gray-600"><span class="font-medium">Name:</span> {{ $assignedSupervisor->name }}</p>
                                <p class="text-sm text-gray-600 mt-1"><span class="font-medium">Email:</span> {{ $assignedSupervisor->email }}</p>
                                <p class="text-xs text-green-600 mt-2">✓ Assigned by your coordinator</p>
                            @else
                                <p class="text-sm text-gray-600"><span class="font-medium">Name:</span> {{ $placement->supervisor_name ?? '—' }}</p>
                                <p class="text-sm text-gray-600 mt-1"><span class="font-medium">Email:</span> {{ $placement->supervisor_email ?? '—' }}</p>
                                @if($placement->supervisor_name || $placement->supervisor_email)
                                    <p class="text-xs text-blue-600 mt-2">Pending coordinator assignment</p>
                                @endif
                            @endif
                        </div>
                    </div>

                    @if($placement->note)
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Notes</h4>
                            <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-4">{{ $placement->note }}</p>
                        </div>
                    @endif

                    @if($placement->proof_path)
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Proof Document</h4>
                            <a href="{{ Storage::url($placement->proof_path) }}" target="_blank" class="text-ojt-primary hover:text-maroon-700 text-sm underline">View uploaded proof</a>
                        </div>
                    @endif

                    <!-- Supervisor details (company-locked) -->
                    @if(!$hasAssignedSupervisor)
                        <div class="mt-8">
                            <button type="button" onclick="document.getElementById('supForm').classList.toggle('hidden')" class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white text-sm font-medium rounded-lg hover:bg-maroon-700 transition-colors duration-200">
                                Add/Update Supervisor Details
                            </button>
                            <p class="text-xs text-gray-500 mt-2">Your coordinator will finalize the assignment for your company.</p>
                        <form id="supForm" method="POST" action="{{ route('placements.propose-supervisor', $placement) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 hidden">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Supervisor Name</label>
                                <input type="text" name="proposed_name" value="{{ old('proposed_name', $placement->supervisor_name) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Supervisor Email</label>
                                <input type="email" name="proposed_email" value="{{ old('proposed_email', $placement->supervisor_email) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary" placeholder="Optional context (e.g., department, availability)">{{ old('notes') }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <button type="submit" class="bg-ojt-primary text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-maroon-700 transition-colors">Send</button>
                            </div>
                        </form>
                        </div>
                    @endif

                @else
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Approved Placement Found</h3>
                        <p class="text-gray-500">Once your placement is approved by your coordinator, it will appear here.</p>
                        <div class="mt-4">
                            <a href="{{ route('placements.index') }}" class="text-ojt-primary hover:text-maroon-700 underline">View placement requests</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>


