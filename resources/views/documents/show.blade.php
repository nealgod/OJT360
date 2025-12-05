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
                    <!-- Letter of Acceptance - Provided by Supervisor -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-ojt-dark mb-4">OJT Acceptance Letter</h2>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-blue-900 mb-1">Provided by Supervisor</h3>
                                    <p class="text-sm text-blue-800">
                                        Your OJT Acceptance Letter will be generated by your supervisor after they accept you. 
                                        Once generated, it will automatically appear in your documents.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">How it works:</h4>
                            <ol class="text-sm text-gray-700 space-y-1 list-decimal list-inside">
                                <li>Apply for OJT at a company (offline/in-person)</li>
                                <li>Company accepts you and assigns a supervisor</li>
                                <li>Supervisor registers on OJT360 and searches for you by Student ID</li>
                                <li>Supervisor generates your acceptance letter with job details</li>
                                <li>Letter is automatically submitted to your documents</li>
                                <li>Coordinator reviews and approves the letter</li>
                            </ol>
                        </div>

                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-600">
                                Waiting for your supervisor to generate the acceptance letter.
                            </p>
                        </div>
                    </div>
                @elseif($requirement->name === "Supervisor's Evaluation Form")
                    <!-- Supervisor's Evaluation Form - System Generated -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-ojt-dark mb-4">Supervisor's Evaluation Form</h2>
                        
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-amber-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-amber-900 mb-1">System-Generated Document</h3>
                                    <p class="text-sm text-amber-800">
                                        This evaluation form is filled out by your supervisor within the OJT360 system. 
                                        You cannot manually upload this document.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Evaluation Process:</h4>
                            <ol class="text-sm text-gray-700 space-y-1 list-decimal list-inside">
                                <li>Your supervisor completes monthly/final evaluations in the system</li>
                                <li>Evaluations are reviewed by your coordinator</li>
                                <li>Approved evaluations are automatically added to your documents</li>
                                <li>You can view the status but not the detailed scores</li>
                            </ol>
                        </div>

                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-600">
                                Waiting for supervisor evaluations to be completed and approved.
                            </p>
                        </div>
                    </div>
                @else
                    <!-- Regular Document Submission Form -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-ojt-dark mb-4">Submit Document</h2>
                        
                        <form id="resubmitForm" method="POST" action="{{ route('documents.submit', $requirement) }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                @if($requirement->max_files_per_submission == 1)
                                    Select File
                                @else
                                    Select Files (up to {{ $requirement->max_files_per_submission }})
                                @endif
                            </label>
                            
                            <!-- Hidden file input -->
                            <input type="file" 
                                   id="fileInput" 
                                   accept="{{ $requirement->file_types ? '.' . implode(',.', $requirement->file_types) : '' }}"
                                   class="hidden">
                            
                            <!-- Custom button to trigger file selection -->
                            <button type="button" 
                                    id="addFilesBtn"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-ojt-primary rounded-lg hover:bg-maroon-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Files
                            </button>
                            
                            <p class="mt-2 text-sm text-gray-500">
                                Accepted formats: {{ $requirement->file_types_string }} | 
                                Max size: {{ $requirement->max_file_size_string }}
                                @if($requirement->max_files_per_submission > 1)
                                    | Max files: {{ $requirement->max_files_per_submission }}
                                @endif
                            </p>
                            @if($requirement->max_files_per_submission > 1)
                                <p class="mt-1 text-xs text-blue-600">
                                    💡 Tip: You can add files from different folders by clicking "Add Files" multiple times
                                </p>
                            @endif
                            
                            <!-- Selected Files Preview -->
                            <div id="selectedFiles" class="mt-4 hidden">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Selected Files (<span id="fileCount">0</span>/{{ $requirement->max_files_per_submission ?? 1 }}):</h4>
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
            const fileInput = document.getElementById('fileInput');
            const addFilesBtn = document.getElementById('addFilesBtn');
            
            // Only run if file input exists (not on Letter of Acceptance page)
            if (!fileInput || !addFilesBtn) return;
            
            const selectedFilesDiv = document.getElementById('selectedFiles');
            const fileListDiv = document.getElementById('fileList');
            const fileCountSpan = document.getElementById('fileCount');
            const maxFiles = {{ $requirement->max_files_per_submission ?? 1 }};
            const maxFileSize = {{ $requirement->max_file_size ?? 5 }} * 1024 * 1024; // Convert MB to bytes
            
            // Store selected files
            let selectedFiles = [];
            
            // Click "Add Files" button to trigger file input
            addFilesBtn.addEventListener('click', function() {
                if (selectedFiles.length >= maxFiles) {
                    alert(`You can only select up to ${maxFiles} files.`);
                    return;
                }
                fileInput.click();
            });
            
            // Handle file selection
            fileInput.addEventListener('change', function() {
                const newFiles = Array.from(this.files);
                
                newFiles.forEach(file => {
                    // Check if we've reached max files
                    if (selectedFiles.length >= maxFiles) {
                        alert(`Maximum ${maxFiles} files allowed. Cannot add more files.`);
                        return;
                    }
                    
                    // Check file size
                    if (file.size > maxFileSize) {
                        alert(`File "${file.name}" is too large. Maximum size is {{ $requirement->max_file_size ?? 5 }}MB.`);
                        return;
                    }
                    
                    // Check for duplicates
                    const isDuplicate = selectedFiles.some(f => f.name === file.name && f.size === file.size);
                    if (isDuplicate) {
                        alert(`File "${file.name}" is already selected.`);
                        return;
                    }
                    
                    // Add file to selected files
                    selectedFiles.push(file);
                });
                
                // Clear the file input so same file can be selected again if removed
                this.value = '';
                
                // Update display
                updateFileList();
            });
            
            // Update file list display
            function updateFileList() {
                if (selectedFiles.length > 0) {
                    selectedFilesDiv.classList.remove('hidden');
                    fileCountSpan.textContent = selectedFiles.length;
                    fileListDiv.innerHTML = '';
                    
                    selectedFiles.forEach((file, index) => {
                        const fileItem = document.createElement('div');
                        fileItem.className = 'flex items-center justify-between bg-gray-50 p-3 rounded-lg border border-gray-200';
                        fileItem.innerHTML = `
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">${file.name}</p>
                                    <p class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                                </div>
                            </div>
                            <button type="button" onclick="removeFile(${index})" class="text-red-500 hover:text-red-700 p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        `;
                        fileListDiv.appendChild(fileItem);
                    });
                    
                    // Update button text
                    if (selectedFiles.length >= maxFiles) {
                        addFilesBtn.disabled = true;
                        addFilesBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        addFilesBtn.innerHTML = `
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Maximum Files Selected
                        `;
                    } else {
                        addFilesBtn.disabled = false;
                        addFilesBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        addFilesBtn.innerHTML = `
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add More Files
                        `;
                    }
                } else {
                    selectedFilesDiv.classList.add('hidden');
                    addFilesBtn.disabled = false;
                    addFilesBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    addFilesBtn.innerHTML = `
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Files
                    `;
                }
            }
            
            // Remove file function
            window.removeFile = function(index) {
                selectedFiles.splice(index, 1);
                updateFileList();
            };
            
            // Before form submission, add files to a hidden input
            const form = document.getElementById('resubmitForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (selectedFiles.length === 0) {
                        e.preventDefault();
                        alert('Please select at least one file to upload.');
                        return false;
                    }
                    
                    // Create DataTransfer object to hold files
                    const dt = new DataTransfer();
                    selectedFiles.forEach(file => dt.items.add(file));
                    
                    // Create a new file input with the selected files
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'file';
                    hiddenInput.name = 'files[]';
                    hiddenInput.multiple = true;
                    hiddenInput.files = dt.files;
                    hiddenInput.style.display = 'none';
                    
                    form.appendChild(hiddenInput);
                });
            }
        });
    </script>
</x-app-layout>
