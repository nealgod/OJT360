<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                {{ $requirement->name }}
            </h2>
            <a href="{{ route('documents.index') }}" class="text-ojt-primary hover:text-maroon-700 text-sm font-medium">
                ← Back to Requirements
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Requirement Details -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-ojt-dark">{{ $requirement->name }}</h1>
                        @if($requirement->description)
                            <p class="text-gray-600 mt-2">{{ $requirement->description }}</p>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $requirement->type === 'pre_placement' ? 'bg-blue-100 text-blue-800' : ($requirement->type === 'post_placement' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst(str_replace('_', ' ', $requirement->type)) }}
                        </span>
                        @if($requirement->is_required)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                Required
                            </span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">File Types:</span>
                        <span class="text-gray-600">{{ $requirement->file_types_string }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Max File Size:</span>
                        <span class="text-gray-600">{{ $requirement->max_file_size_string }}</span>
                    </div>
                </div>

                @if($requirement->instructions)
                    <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                        <h3 class="font-medium text-blue-900 mb-2">Instructions:</h3>
                        <p class="text-blue-800 text-sm">{{ $requirement->instructions }}</p>
                    </div>
                @endif
            </div>

            <!-- Submission Status -->
            @if($submission)
                <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                    <h2 class="text-lg font-semibold text-ojt-dark mb-4">Your Submission</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="font-medium text-gray-700 mb-2">File Details</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Filename:</span>
                                    <span class="font-medium">{{ $submission->original_filename }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Size:</span>
                                    <span class="font-medium">{{ $submission->file_size_formatted }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Submitted:</span>
                                    <span class="font-medium">{{ $submission->created_at->format('M d, Y g:i A') }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="font-medium text-gray-700 mb-2">Status</h3>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $submission->status_badge }}">
                                        {{ $submission->status_text }}
                                    </span>
                                </div>
                                @if($submission->reviewed_at)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Reviewed:</span>
                                        <span class="font-medium">{{ $submission->reviewed_at->format('M d, Y g:i A') }}</span>
                                    </div>
                                @endif
                                @if($submission->reviewer)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Reviewed by:</span>
                                        <span class="font-medium">{{ $submission->reviewer->name }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($submission->feedback)
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-medium text-gray-700 mb-2">Coordinator Feedback:</h3>
                            <p class="text-gray-600 text-sm">{{ $submission->feedback }}</p>
                        </div>
                    @endif

                    <div class="mt-4 flex space-x-4">
                        <a href="{{ route('documents.download', $submission) }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download File
                        </a>
                    </div>
                </div>
            @else
                <!-- Submission Form -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-ojt-dark mb-4">Submit Document</h2>
                    
                    <form method="POST" action="{{ route('documents.submit', $requirement) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                                Select File
                            </label>
                            <input type="file" 
                                   id="file" 
                                   name="file" 
                                   accept="{{ $requirement->file_types ? '.' . implode(',.', $requirement->file_types) : '' }}"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-ojt-primary file:text-white hover:file:bg-maroon-700"
                                   required>
                            <p class="mt-1 text-sm text-gray-500">
                                Accepted formats: {{ $requirement->file_types_string }} | 
                                Max size: {{ $requirement->max_file_size_string }}
                            </p>
                            @error('file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('documents.index') }}" 
                               class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 text-sm font-medium text-white bg-ojt-primary rounded-lg hover:bg-maroon-700 transition-colors">
                                Submit Document
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
