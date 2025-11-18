<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                {{ __('Document Requirements') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Simple Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-ojt-dark mb-2">Document Requirements</h1>
                <p class="text-gray-600">Submit your required documents for OJT</p>
            </div>

            <!-- Pre‑requirements Checklist & Progress -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
                @php
                    // Compute overall progress
                    $totalRequired = $prePlacement->where('is_required', true)->count() + 
                                     $postPlacement->where('is_required', true)->count() + 
                                     $ongoing->where('is_required', true)->count();
                    $submittedRequired = 0;
                    foreach([$prePlacement, $postPlacement, $ongoing] as $group) {
                        foreach($group->where('is_required', true) as $req) {
                            if(($submissions[$req->id] ?? collect())->count() > 0) {
                                $submittedRequired++;
                            }
                        }
                    }
                    $progressPercentage = $totalRequired > 0 ? round(($submittedRequired / $totalRequired) * 100) : 0;

                    // Pre‑placement checklist counts (Approved gating)
                    $preTotal = $prePlacement->where('is_required', true)->count();
                    $preApproved = 0;
                    $prePendingList = [];
                    foreach($prePlacement as $req) {
                        if(!$req->is_required) {
                            continue;
                        }
                        $first = ($submissions[$req->id] ?? collect())->first();
                        if($first && in_array($first->status, ['submitted', 'approved'])) {
                            $preApproved++;
                        } else {
                            $prePendingList[] = $req->name;
                        }
                    }
                @endphp
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Overall Progress</span>
                    <span class="text-sm text-gray-600">{{ $submittedRequired }}/{{ $totalRequired }} completed</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                    <div class="bg-ojt-primary h-2 rounded-full transition-all duration-300" style="width: {{ $progressPercentage }}%"></div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        <span class="font-medium">Pre‑requirements:</span>
                        <span>{{ $preApproved }} of {{ $preTotal }} submitted</span>
                    </div>
                    @if($preTotal > 0 && $preApproved === $preTotal)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Everything unlocked</span>
                    @else
                        @if(count($prePendingList))
                            <div class="text-xs text-gray-500">
                                Missing: {{ implode(', ', array_map(fn($n)=>Str::limit($n, 20), $prePendingList)) }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" id="searchInput" placeholder="Search documents..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-ojt-primary focus:border-ojt-primary">
                    </div>
                    <div class="flex gap-3">
                        <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-ojt-primary focus:border-ojt-primary">
                            <option value="all">All Status</option>
                            <option value="submitted">Submitted</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="pending">Not Submitted</option>
                        </select>
                        <select id="typeFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-ojt-primary focus:border-ojt-primary">
                            <option value="all">All Types</option>
                            <option value="pre_placement">Pre-placement</option>
                            <option value="post_placement">Post-placement</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Pre-Placement Requirements -->
            @if($prePlacement->count() > 0)
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-ojt-dark mb-3 flex items-center">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                        Pre-Placement Requirements
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($prePlacement as $requirement)
                            @php
                                $submission = ($submissions[$requirement->id] ?? collect())->first();
                                $fileCount = ($submissions[$requirement->id] ?? collect())->count();
                            @endphp
                            <div class="bg-white rounded-lg border border-gray-200 p-5 hover:shadow-lg transition-all duration-200 document-card" 
                                 data-type="pre_placement" 
                                 data-status="{{ $submission ? $submission->status : 'pending' }}"
                                 data-name="{{ strtolower($requirement->name) }}">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 text-sm leading-tight mb-2">{{ Str::limit($requirement->name, 35) }}</h3>
                                        @if($requirement->is_required)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Required</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Optional</span>
                                        @endif
                                    </div>
                                    @if($submission)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $submission->status_badge }} ml-2">
                                            {{ $submission->status_text }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 ml-2">
                                            Not Submitted
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="space-y-3">
                                    <div class="text-xs text-gray-500 space-y-1">
                                        <div class="flex items-center">
                                            <svg class="w-3 h-3 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            {{ $requirement->file_types_string }}
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-3 h-3 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4z" />
                                            </svg>
                                            Max {{ $requirement->max_file_size_string }}
                                        </div>
                                        @if($fileCount > 1)
                                            <div class="flex items-center text-green-600">
                                                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                {{ $fileCount }} files submitted
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <a href="{{ route('documents.show', $requirement) }}" 
                                       class="block w-full text-center px-4 py-2 text-sm font-medium text-white bg-ojt-primary rounded-lg hover:bg-maroon-700 transition-colors">
                                        {{ $submission ? 'View Details' : 'Submit Now' }}
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
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($postPlacement as $requirement)
                            @php
                                $submission = ($submissions[$requirement->id] ?? collect())->first();
                                $fileCount = ($submissions[$requirement->id] ?? collect())->count();
                            @endphp
                            <div class="bg-white rounded-lg border border-gray-200 p-5 hover:shadow-lg transition-all duration-200 document-card" 
                                 data-type="post_placement" 
                                 data-status="{{ $submission ? $submission->status : 'pending' }}"
                                 data-name="{{ strtolower($requirement->name) }}">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 text-sm leading-tight mb-2">{{ Str::limit($requirement->name, 35) }}</h3>
                                        @if($requirement->is_required)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Required</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Optional</span>
                                        @endif
                                    </div>
                                    @if($submission)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $submission->status_badge }} ml-2">
                                            {{ $submission->status_text }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 ml-2">
                                            Not Submitted
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="space-y-3">
                                    <div class="text-xs text-gray-500 space-y-1">
                                        <div class="flex items-center">
                                            <svg class="w-3 h-3 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            {{ $requirement->file_types_string }}
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-3 h-3 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4z" />
                                            </svg>
                                            Max {{ $requirement->max_file_size_string }}
                                        </div>
                                        @if($fileCount > 1)
                                            <div class="flex items-center text-green-600">
                                                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                {{ $fileCount }} files submitted
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <a href="{{ route('documents.show', $requirement) }}" 
                                       class="block w-full text-center px-4 py-2 text-sm font-medium text-white bg-ojt-primary rounded-lg hover:bg-maroon-700 transition-colors">
                                        {{ $submission ? 'View Details' : 'Submit Now' }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($prePlacement->count() === 0 && $postPlacement->count() === 0)
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const typeFilter = document.getElementById('typeFilter');
            const documentCards = document.querySelectorAll('.document-card');

            function filterDocuments() {
                const searchTerm = searchInput.value.toLowerCase();
                const statusValue = statusFilter.value;
                const typeValue = typeFilter.value;

                documentCards.forEach(card => {
                    const cardName = card.getAttribute('data-name');
                    const cardStatus = card.getAttribute('data-status');
                    const cardType = card.getAttribute('data-type');

                    const matchesSearch = cardName.includes(searchTerm);
                                const matchesStatus = statusValue === 'all' || 
                        (statusValue === 'pending' && cardStatus === 'pending') ||
                        (statusValue === 'submitted' && cardStatus === 'submitted') ||
                        (statusValue === 'approved' && cardStatus === 'approved') ||
                        (statusValue === 'rejected' && cardStatus === 'rejected');
                    const matchesType = typeValue === 'all' || cardType === typeValue;

                    if (matchesSearch && matchesStatus && matchesType) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Hide empty sections
                const sections = document.querySelectorAll('.mb-6');
                sections.forEach(section => {
                    const visibleCards = section.querySelectorAll('.document-card[style*="block"], .document-card:not([style*="none"])');
                    if (visibleCards.length === 0 && section.querySelector('.document-card')) {
                        section.style.display = 'none';
                    } else if (section.querySelector('.document-card')) {
                        section.style.display = 'block';
                    }
                });
            }

            searchInput.addEventListener('input', filterDocuments);
            statusFilter.addEventListener('change', filterDocuments);
            typeFilter.addEventListener('change', filterDocuments);
        });
    </script>
</x-app-layout>
