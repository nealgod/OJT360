<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Program Hours Management</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('coord.students.index') }}" class="text-ojt-primary hover:text-maroon-700 text-sm font-medium">
                    ← Back to Students
                </a>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <!-- Header -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-ojt-dark">{{ $program->name }}</h1>
                    <p class="text-sm text-gray-600">{{ $program->department->name ?? 'N/A' }}</p>
                </div>

                <!-- Content -->
                <div class="px-6 py-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Required Hours</h3>
                        <div class="bg-ojt-accent/10 border border-ojt-accent/30 rounded-lg p-6">
                            <div class="text-center">
                                <div class="text-5xl font-bold text-ojt-primary mb-2">
                                    {{ $program->required_hours ?? 'Not Set' }}
                                </div>
                                <p class="text-gray-600">hours required for OJT</p>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="text-2xl font-bold text-blue-900">{{ $totalStudents }}</div>
                            <div class="text-sm text-blue-700">Total Students</div>
                        </div>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="text-2xl font-bold text-green-900">{{ $studentsUsingDefault }}</div>
                            <div class="text-sm text-green-700">Using Default Hours</div>
                        </div>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="text-2xl font-bold text-yellow-900">{{ $studentsWithCustomHours }}</div>
                            <div class="text-sm text-yellow-700">With Custom Hours</div>
                        </div>
                    </div>

                    <!-- Update Form -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Change Required Hours</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            You can update the required hours for your program. This will affect students who don't have custom hours set.
                            <br><strong class="text-ojt-primary">{{ $studentsUsingDefault }}</strong> students will be affected.
                        </p>

                        <form method="POST" action="{{ route('coord.program.update-hours') }}">
                            @csrf
                            @method('PATCH')

                            <div class="mb-4">
                                <label for="required_hours" class="block text-sm font-medium text-gray-700 mb-2">Required Hours</label>
                                <input type="number" 
                                       id="required_hours" 
                                       name="required_hours" 
                                       value="{{ $program->required_hours }}" 
                                       min="200" 
                                       max="1000" 
                                       required
                                       class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-ojt-primary focus:border-ojt-primary">
                                <p class="mt-1 text-sm text-gray-500">Enter hours between 200-1000</p>
                                @error('required_hours')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end gap-3">
                                <a href="{{ route('coord.students.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                                    Cancel
                                </a>
                                <button type="submit" class="px-4 py-2 bg-ojt-primary text-white rounded-lg hover:bg-maroon-700 transition-colors">
                                    Update Hours
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-medium text-blue-900 mb-2">💡 How This Works</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• <strong>Students with custom hours:</strong> Will NOT be affected by this change</li>
                    <li>• <strong>Students using default:</strong> Will automatically get the new hours</li>
                    <li>• <strong>New students:</strong> Will automatically use this default</li>
                    <li>• You can still set custom hours for individual students if needed</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>

