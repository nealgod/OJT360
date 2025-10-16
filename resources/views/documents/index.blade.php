<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                {{ __('Document Requirements') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-6">
                <h1 class="text-xl font-bold text-ojt-dark mb-1">Document Requirements</h1>
                <p class="text-sm text-gray-600">Submit required documents and track your submission status.</p>
            </div>

            <!-- Quick Stats -->
            <div class="flex items-center justify-center space-x-8 mb-6 text-sm text-gray-600">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                    <span>Pre-placement: {{ $prePlacement->count() }}</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    <span>Post-placement: {{ $postPlacement->count() }}</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                    <span>Ongoing: {{ $ongoing->count() }}</span>
                </div>
            </div>

            <!-- Pre-Placement Requirements -->
            @if($prePlacement->count() > 0)
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-ojt-dark mb-3 flex items-center">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                        Pre-Placement Requirements
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($prePlacement as $requirement)
                            @php
                                $submission = $submissions->get($requirement->id);
                            @endphp
                            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-sm transition-shadow">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900 text-sm">{{ $requirement->name }}</h3>
                                        @if($requirement->is_required)
                                            <span class="text-xs text-red-600 font-medium">Required</span>
                                        @else
                                            <span class="text-xs text-gray-500">Optional</span>
                                        @endif
                                    </div>
                                    @if($submission)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $submission->status_badge }}">
                                            {{ $submission->status_text }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="flex items-center justify-between mt-3">
                                    <div class="text-xs text-gray-500">
                                        {{ $requirement->file_types_string }} • Max {{ $requirement->max_file_size_string }}
                                    </div>
                                    <a href="{{ route('documents.show', $requirement) }}" 
                                       class="text-xs text-ojt-primary hover:text-maroon-700 font-medium">
                                        {{ $submission ? 'View' : 'Submit' }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Post-Placement Requirements -->
            @if($postPlacement->count() > 0)
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-ojt-dark mb-3 flex items-center">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                        Post-Placement Requirements
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($postPlacement as $requirement)
                            @php
                                $submission = $submissions->get($requirement->id);
                            @endphp
                            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-sm transition-shadow">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900 text-sm">{{ $requirement->name }}</h3>
                                        @if($requirement->is_required)
                                            <span class="text-xs text-red-600 font-medium">Required</span>
                                        @else
                                            <span class="text-xs text-gray-500">Optional</span>
                                        @endif
                                    </div>
                                    @if($submission)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $submission->status_badge }}">
                                            {{ $submission->status_text }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="flex items-center justify-between mt-3">
                                    <div class="text-xs text-gray-500">
                                        {{ $requirement->file_types_string }} • Max {{ $requirement->max_file_size_string }}
                                    </div>
                                    <a href="{{ route('documents.show', $requirement) }}" 
                                       class="text-xs text-ojt-primary hover:text-maroon-700 font-medium">
                                        {{ $submission ? 'View' : 'Submit' }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Ongoing Requirements -->
            @if($ongoing->count() > 0)
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-ojt-dark mb-3 flex items-center">
                        <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                        Ongoing Requirements
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($ongoing as $requirement)
                            @php
                                $submission = $submissions->get($requirement->id);
                            @endphp
                            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-sm transition-shadow">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900 text-sm">{{ $requirement->name }}</h3>
                                        @if($requirement->is_required)
                                            <span class="text-xs text-red-600 font-medium">Required</span>
                                        @else
                                            <span class="text-xs text-gray-500">Optional</span>
                                        @endif
                                    </div>
                                    @if($submission)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $submission->status_badge }}">
                                            {{ $submission->status_text }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="flex items-center justify-between mt-3">
                                    <div class="text-xs text-gray-500">
                                        {{ $requirement->file_types_string }} • Max {{ $requirement->max_file_size_string }}
                                    </div>
                                    <a href="{{ route('documents.show', $requirement) }}" 
                                       class="text-xs text-ojt-primary hover:text-maroon-700 font-medium">
                                        {{ $submission ? 'View' : 'Submit' }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($prePlacement->count() === 0 && $postPlacement->count() === 0 && $ongoing->count() === 0)
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Document Requirements</h3>
                    <p class="text-gray-500">Your coordinator hasn't set up any document requirements yet.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
