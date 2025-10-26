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
            @if(isset($submissionsAll) && $submissionsAll->count() > 0)
                <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                    <h2 class="text-lg font-semibold text-ojt-dark mb-4">Your Submission{{ $submissionsAll->count() > 1 ? 's' : '' }}</h2>
                    <div class="space-y-4">
                        @foreach($submissionsAll as $sub)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border rounded-lg p-4">
                                <div>
                                    <h3 class="font-medium text-gray-700 mb-2">File Details</h3>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Filename:</span>
                                            <span class="font-medium">{{ $sub->original_filename }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Size:</span>
                                            <span class="font-medium">{{ $sub->file_size_formatted }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Submitted:</span>
                                            <span class="font-medium">{{ $sub->created_at->format('M d, Y g:i A') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-700 mb-2">Status</h3>
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Status:</span>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $sub->status_badge }}">{{ $sub->status_text }}</span>
                                        </div>
                                        @if($sub->reviewed_at)
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Reviewed:</span>
                                                <span class="font-medium">{{ $sub->reviewed_at->format('M d, Y g:i A') }}</span>
                                            </div>
                                        @endif
                                        @if($sub->reviewer)
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Reviewed by:</span>
                                                <span class="font-medium">{{ $sub->reviewer->name }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="md:col-span-2">
                                    <div class="flex space-x-3">
                                        <a href="{{ route('documents.download', $sub) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Download File
                                        </a>
                                        @if($sub->status === 'submitted')
                                            <form method="POST" action="{{ route('documents.cancel', $sub) }}" class="inline" onsubmit="return confirm('Are you sure you want to cancel this submission? This action cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 text-sm font-medium rounded-lg hover:bg-red-200 transition-colors">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    Cancel Submission
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <!-- Submission Form -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-ojt-dark mb-4">Submit Document</h2>
                    
                    <form method="POST" action="{{ route('documents.submit', $requirement) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label for="files" class="block text-sm font-medium text-gray-700 mb-2">
                                Select Files (up to 2)
                            </label>
                            <input type="file" 
                                   id="files" 
                                   name="files[]" 
                                   multiple
                                   accept="{{ $requirement->file_types ? '.' . implode(',.', $requirement->file_types) : '' }}"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-ojt-primary file:text-white hover:file:bg-maroon-700"
                                   required>
                            <p class="mt-1 text-sm text-gray-500">
                                Accepted formats: {{ $requirement->file_types_string }} | 
                                Max size: {{ $requirement->max_file_size_string }} |
                                Max files: 2 per requirement
                            </p>
                            
                            <!-- Selected Files Preview -->
                            <div id="selectedFiles" class="mt-3 hidden">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Selected Files:</h4>
                                <div id="fileList" class="space-y-2"></div>
                            </div>
                            
                            @error('files')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('files.*')
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('files');
            const selectedFilesDiv = document.getElementById('selectedFiles');
            const fileListDiv = document.getElementById('fileList');
            const maxFiles = 2;

            fileInput.addEventListener('change', function() {
                const files = Array.from(this.files);
                
                if (files.length > maxFiles) {
                    alert(`You can only select up to ${maxFiles} files.`);
                    this.value = '';
                    selectedFilesDiv.classList.add('hidden');
                    return;
                }

                if (files.length > 0) {
                    selectedFilesDiv.classList.remove('hidden');
                    fileListDiv.innerHTML = '';

                    files.forEach((file, index) => {
                        const fileItem = document.createElement('div');
                        fileItem.className = 'flex items-center justify-between bg-gray-50 p-3 rounded-lg';
                        fileItem.innerHTML = `
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">${file.name}</p>
                                    <p class="text-xs text-gray-500">${(file.size / 1024).toFixed(2)} KB</p>
                                </div>
                            </div>
                            <button type="button" onclick="removeFile(${index})" class="text-red-500 hover:text-red-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        `;
                        fileListDiv.appendChild(fileItem);
                    });
                } else {
                    selectedFilesDiv.classList.add('hidden');
                }
            });

            window.removeFile = function(index) {
                const dt = new DataTransfer();
                const files = Array.from(fileInput.files);
                files.splice(index, 1);
                
                files.forEach(file => dt.items.add(file));
                fileInput.files = dt.files;
                
                // Trigger change event to update preview
                fileInput.dispatchEvent(new Event('change'));
            };
        });
    </script>
</x-app-layout>
