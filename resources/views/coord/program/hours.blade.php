<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
            Program Hours Management
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('coord.students.index') }}" class="text-ojt-primary hover:text-maroon-700 text-sm font-medium">
                    ← Back to Students
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-2xl font-bold text-ojt-dark">{{ $program->name }}</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $program->department->name ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-500 mt-2">{{ $totalStudents }} students enrolled</p>
                </div>

                <div class="p-6">
                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Required Hours</label>
                        <div class="text-5xl font-bold text-ojt-primary">
                            {{ $program->required_hours ?? '—' }}
                        </div>
                        <p class="text-sm text-gray-500 mt-1">hours</p>
                    </div>

                    <form method="POST" action="{{ route('coord.program.update-hours') }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-6">
                            <label for="required_hours" class="block text-sm font-medium text-gray-700 mb-2">
                                Update Required Hours
                            </label>
                            <input type="number" 
                                   id="required_hours" 
                                   name="required_hours" 
                                   value="{{ old('required_hours', $program->required_hours) }}" 
                                   min="200" 
                                   max="1000" 
                                   required
                                   class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-ojt-primary focus:border-ojt-primary">
                            <p class="mt-1 text-xs text-gray-500">Enter any value between 200-1000 hours</p>
                            @error('required_hours')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <p class="text-sm text-blue-800">
                                All {{ $totalStudents }} students in this program will be notified of the change.
                            </p>
                        </div>

                        <div class="flex justify-end gap-3">
                            <a href="{{ route('coord.students.index') }}" 
                               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-ojt-primary text-white rounded-lg hover:bg-maroon-700">
                                Update Hours
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
