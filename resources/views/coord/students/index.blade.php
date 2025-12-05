<x-app-layout>
    <x-slot name="header">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Manage Students</h2>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Department: {{ Auth::user()->coordinatorProfile?->department }}</span>
                    <span class="text-sm text-gray-600">Program: {{ $programName }}</span>
                </div>
            </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-ojt-primary to-maroon-700 rounded-xl p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-ojt-accent/80 text-sm font-medium">Total Students</p>
                            <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Active OJT</p>
                            <p class="text-2xl font-bold text-ojt-dark">{{ $stats['active'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Pending</p>
                            <p class="text-2xl font-bold text-ojt-dark">{{ $stats['pending'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Completed</p>
                            <p class="text-2xl font-bold text-ojt-dark">{{ $stats['completed'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="mb-6 bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <form method="GET" action="{{ route('coord.students.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" 
                                   name="search" 
                                   value="{{ $search }}"
                                   placeholder="Name or Student ID..." 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary">
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary">
                                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Status</option>
                                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>

                        <!-- Sort -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                            <select name="sort" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary">
                                <option value="name" {{ $sort == 'name' ? 'selected' : '' }}>Name</option>
                                <option value="id" {{ $sort == 'id' ? 'selected' : '' }}>Student ID</option>
                                <option value="status" {{ $sort == 'status' ? 'selected' : '' }}>Status</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <button type="submit" class="bg-ojt-primary text-white px-6 py-2 rounded-md text-sm font-medium hover:bg-maroon-700 transition-colors duration-200">
                            Apply Filters
                        </button>
                        <a href="{{ route('coord.students.index') }}" class="text-gray-600 hover:text-ojt-primary text-sm">
                            Clear Filters
                        </a>
                    </div>
                </form>
            </div>

            @if($students->count() > 0)
                <!-- Students Table -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supervisor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($students as $student)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    @if($student->getProfile() && $student->getProfile()->profile_image)
                                                        <img class="h-10 w-10 rounded-full object-cover" 
                                                             src="{{ Storage::url($student->getProfile()->profile_image) }}" 
                                                             alt="{{ $student->name }}">
                                                    @else
                                                        <div class="h-10 w-10 rounded-full {{ $student->getAvatarColor() }} flex items-center justify-center">
                                                            <span class="text-white font-medium text-sm">{{ substr($student->name, 0, 1) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $student->name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">{{ $student->studentProfile?->student_id ?? 'No ID' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $status = $student->studentProfile?->ojt_status ?? 'pending';
                                                $statusColors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'active' => 'bg-green-100 text-green-800',
                                                    'completed' => 'bg-blue-100 text-blue-800'
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($student->studentProfile?->supervisor)
                                                <div class="flex items-center space-x-2">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Assigned</span>
                                                    <span class="text-sm text-gray-700 truncate max-w-[10rem]" title="{{ $student->studentProfile->supervisor->name }}">{{ $student->studentProfile->supervisor->name }}</span>
                                                </div>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex flex-col sm:flex-row gap-2">
                                                <a href="{{ route('coord.students.show', $student) }}" 
                                                   class="inline-flex items-center justify-center px-4 py-2 bg-ojt-primary text-white rounded-md text-xs font-semibold tracking-wide hover:bg-maroon-700 transition-colors">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m6 4H9m3 4v-4m0-8V4m9 8a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    View Student
                                                </a>
                                                @if(!$student->studentProfile?->supervisor)
                                                    <!-- Supervisor assignment is now handled via Supervisor Portal -->
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $students->links() }}
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
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Students Found</h3>
                        <p class="text-gray-500 mb-4">No students match your current filters.</p>
                        <a href="{{ route('coord.students.index') }}" class="text-ojt-primary hover:text-maroon-700 font-medium">
                            Clear Filters →
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
