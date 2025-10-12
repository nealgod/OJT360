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
            @if($requests->count() > 0)
                <!-- Filter and Sort Options -->
                <div class="mb-6 bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600">Filter by:</span>
                            <select class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary">
                                <option value="all">All Requests</option>
                                <option value="recent">Recent (Last 7 days)</option>
                                <option value="company">Listed Companies</option>
                                <option value="external">External Companies</option>
                            </select>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Sort by:</span>
                            <select class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary">
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="name">Student Name</option>
                                <option value="company">Company Name</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Placement Requests Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($requests as $req)
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200">
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
                            <div class="p-6">
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
                                        @else
                                            <div class="flex items-center space-x-2">
                                                <svg class="w-4 h-4 text-ojt-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                <span class="font-medium text-ojt-dark">{{ $req->external_company_name }}</span>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1">{{ $req->external_company_address }}</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Request Information -->
                                <div class="space-y-3 mb-4">
                                    @if($req->start_date)
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span class="text-sm text-gray-600">Start Date: <span class="font-medium">{{ $req->start_date->format('M d, Y') }}</span></span>
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
                                        <a href="{{ Storage::url($req->proof_path) }}" 
                                           target="_blank" 
                                           class="inline-flex items-center space-x-2 text-ojt-primary hover:text-maroon-700 text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span>View Document</span>
                                        </a>
                                    </div>
                                @endif

                                <!-- Request Date -->
                                <div class="text-xs text-gray-500 mb-4">
                                    Submitted {{ $req->created_at->diffForHumans() }}
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="px-6 py-4 bg-gray-50 rounded-b-xl">
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <!-- Approve Form -->
                                    <form method="POST" action="{{ route('coord.placements.approve', $req) }}" class="flex-1">
                                        @csrf
                                        <div class="flex flex-col sm:flex-row gap-2">
                                            <input type="date" 
                                                   name="start_date" 
                                                   class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary" 
                                                   required />
                                            <button type="submit" 
                                                    class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition-colors duration-200 flex items-center justify-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Approve
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <!-- Decline Form -->
                                    <form method="POST" action="{{ route('coord.placements.decline', $req) }}" class="flex-1">
                                        @csrf
                                        <div class="flex flex-col sm:flex-row gap-2">
                                            <input type="text" 
                                                   name="reason" 
                                                   placeholder="Decline reason..." 
                                                   class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-red-500 focus:border-red-500" 
                                                   required />
                                            <button type="submit" 
                                                    class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition-colors duration-200 flex items-center justify-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                Decline
                                            </button>
                                        </div>
                                    </form>
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


