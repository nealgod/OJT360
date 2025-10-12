<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Manage Supervisors</h2>
            <a href="{{ route('coord.supervisors.create') }}" class="bg-ojt-primary text-white px-4 py-2 rounded-lg hover:bg-maroon-700">
                Add Supervisor
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($supervisors->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($supervisors as $supervisor)
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <!-- Supervisor Header -->
                            <div class="p-6 border-b border-gray-100">
                                <div class="flex items-start space-x-4">
                                    <!-- Supervisor Avatar -->
                                    <div class="flex-shrink-0">
                                        @if($supervisor->supervisorProfile?->profile_image)
                                            <img src="{{ Storage::url($supervisor->supervisorProfile->profile_image) }}" 
                                                 alt="{{ $supervisor->name }}" 
                                                 class="w-12 h-12 rounded-full object-cover border-2 border-ojt-primary">
                                        @else
                                            <div class="w-12 h-12 bg-ojt-primary rounded-full flex items-center justify-center text-white font-bold text-lg">
                                                {{ substr($supervisor->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Supervisor Info -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-semibold text-ojt-dark truncate">{{ $supervisor->name }}</h3>
                                        <p class="text-sm text-gray-600">{{ $supervisor->supervisorProfile?->employee_id ?? 'No ID' }}</p>
                                        <p class="text-sm text-gray-500">{{ $supervisor->supervisorProfile?->position ?? 'No Position' }}</p>
                                    </div>
                                    
                                    <!-- Status Badge -->
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $supervisor->supervisorProfile?->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($supervisor->supervisorProfile?->status ?? 'inactive') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Supervisor Details -->
                            <div class="p-6">
                                <!-- Company Information -->
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Company</h4>
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-ojt-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <span class="font-medium text-ojt-dark">{{ $supervisor->supervisorProfile?->company?->name ?? 'No Company' }}</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ $supervisor->supervisorProfile?->company?->address ?? 'No Address' }}</p>
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="space-y-3 mb-4">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-sm text-gray-600">{{ $supervisor->email }}</span>
                                    </div>
                                    
                                    @if($supervisor->supervisorProfile?->phone)
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            <span class="text-sm text-gray-600">{{ $supervisor->supervisorProfile->phone }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Assigned Students Count -->
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Assigned Students</h4>
                                    <div class="bg-blue-50 rounded-lg p-3">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                            </svg>
                                            <span class="text-sm font-medium text-blue-800">
                                                {{ $supervisor->studentProfiles()->count() }} Student(s)
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Assigned Students List -->
                            <div class="px-6 py-4 bg-gray-50 rounded-b-xl">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Assigned Students</h4>
                                @if($supervisor->studentProfiles->count() > 0)
                                    <div class="space-y-2">
                                        @foreach($supervisor->studentProfiles as $studentProfile)
                                            <div class="flex items-center space-x-2 text-sm">
                                                <div class="w-6 h-6 bg-ojt-primary rounded-full flex items-center justify-center text-white text-xs font-bold">
                                                    {{ substr($studentProfile->user->name, 0, 1) }}
                                                </div>
                                                <span class="text-gray-700">{{ $studentProfile->user->name }}</span>
                                                <span class="text-gray-500">({{ $studentProfile->student_id ?? 'No ID' }})</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500">No students assigned yet</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $supervisors->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Supervisors Found</h3>
                        <p class="text-gray-500 mb-4">Supervisors will appear here when students submit placement requests with supervisor information and you approve them.</p>
                        <div class="space-y-2">
                            <a href="{{ route('coord.placements.inbox') }}" class="block text-ojt-primary hover:text-maroon-700 font-medium">
                                Review Placement Requests →
                            </a>
                            <p class="text-xs text-gray-400">Students need to provide supervisor name and email in their placement requests</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
