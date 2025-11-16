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

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">File Types:</span>
                        <span class="text-gray-600">{{ $requirement->file_types_string }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Max File Size:</span>
                        <span class="text-gray-600">{{ $requirement->max_file_size_string }}</span>
                    </div>
                    @if($requirement->max_files_per_submission && $requirement->max_files_per_submission > 2)
                        <div>
                            <span class="font-medium text-gray-700">Max Files:</span>
                            <span class="text-gray-600">{{ $requirement->max_files_per_submission }}</span>
                        </div>
                    @endif
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
                                    @if(!empty($sub->feedback))
                                        <div class="mb-3 bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-800">
                                            <div class="font-medium mb-1">Coordinator Feedback</div>
                                            <p>{{ $sub->feedback }}</p>
                                        </div>
                                    @endif
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
                                        @elseif($sub->status === 'rejected')
                                            <a href="#resubmit" onclick="document.getElementById('resubmitForm').scrollIntoView({behavior: 'smooth'}); return false;" class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white text-sm font-medium rounded-lg hover:bg-maroon-700 transition-colors">
                                                Resubmit
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Resubmit / First Submission Form -->
            @php($latest = isset($submissionsAll) ? $submissionsAll->first() : null)
            @if(!$latest || $latest->status === 'rejected')
                <!-- Check if this is Letter of Acceptance -->
                @if($requirement->name === 'Letter of Acceptance')
                    <!-- Special: Request Acceptance Letter from Supervisor -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-ojt-dark mb-4">Request OJT Acceptance Letter</h2>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-blue-900 mb-1">Digital Process</h3>
                                    <p class="text-sm text-blue-800">
                                        Request your supervisor to generate an official OJT Acceptance Letter digitally. 
                                        Your supervisor will receive an email with a link to fill out the form and generate 
                                        the letter automatically. The letter will be submitted to your documents once completed.
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if(!$hasResume || !$hasApplication)
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-medium text-yellow-900 mb-1">Requirements Not Met</h4>
                                        <p class="text-sm text-yellow-800">
                                            You must submit the following before requesting an acceptance letter:
                                        </p>
                                        <ul class="text-sm text-yellow-800 list-decimal list-inside mt-2 space-y-1">
                                            @if(!$hasResume)
                                                <li><strong>Resume</strong> - <a href="{{ route('resume.index') }}" class="underline font-medium hover:text-yellow-900">Create your resume using the Resume Builder</a></li>
                                            @endif
                                            @if(!$hasApplication)
                                                <li><strong>Application Letter and PDS/Resume</strong> - <a href="{{ route('documents.index') }}" class="underline font-medium hover:text-yellow-900">Submit in Documents section</a></li>
                                            @endif
                                        </ul>
                                        @if(!$hasResume || !$hasApplication)
                                            <p class="text-xs text-yellow-700 mt-3 italic">
                                                Note: Both requirements must be completed before you can request an acceptance letter from your supervisor.
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center justify-center py-8">
                                <a href="{{ route('acceptance.request.create') }}" 
                                   class="inline-flex items-center px-6 py-3 text-base font-medium text-white bg-ojt-primary rounded-lg hover:bg-maroon-700 transition-colors shadow-sm">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Request Acceptance Letter from Supervisor
                                </a>
                            </div>
                        @endif

                        <div class="bg-gray-50 rounded-lg p-4 mt-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">How it works:</h4>
                            <ol class="text-sm text-gray-700 space-y-1 list-decimal list-inside">
                                <li>Click the button above and fill out the request form</li>
                                <li>Your supervisor receives an email with a secure link</li>
                                <li>Supervisor creates an account (one-time setup)</li>
                                <li>Supervisor fills out the acceptance letter details</li>
                                <li>Letter is automatically generated and submitted to your documents</li>
                                <li>Coordinator reviews and approves the letter</li>
                            </ol>
                        </div>
                    </div>
                @else
                    <!-- Regular Document Submission Form -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-ojt-dark mb-4">Submit Document</h2>
                        
                        <form id="resubmitForm" method="POST" action="{{ route('documents.submit', $requirement) }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                        
                        <div>
                            <label for="files" class="block text-sm font-medium text-gray-700 mb-2">
                                Select Files (up to {{ $requirement->max_files_per_submission ?? 2 }})
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
                                Max files: {{ $requirement->max_files_per_submission ?? 2 }} per requirement
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
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('files');
            
            // Only run if file input exists (not on Letter of Acceptance page)
            if (!fileInput) return;
            
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
