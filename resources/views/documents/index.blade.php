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

    <!-- Pre-Placement Completion Modal -->
    @if(Auth::user()->isStudent())
        @php
            $profile = Auth::user()->studentProfile;
            // Show modal if completed within last 2 minutes (just completed)
            $justCompleted = $profile && 
                           $profile->preplacement_complete && 
                           $profile->preplacement_completed_at &&
                           $profile->preplacement_completed_at->gt(now()->subMinutes(2));
        @endphp

        @if($justCompleted)
            <div x-data="{ show: true }" 
                 x-show="show"
                 x-cloak
                 class="fixed inset-0 z-50 overflow-y-auto"
                 aria-labelledby="modal-title" 
                 role="dialog" 
                 aria-modal="true">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" 
                     @click="show = false"></div>

                <!-- Modal panel -->
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div x-show="show"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-auto overflow-hidden">
                        
                        <!-- Success Icon Header -->
                        <div class="bg-gradient-to-r from-ojt-primary to-maroon-700 px-6 py-8 text-center">
                            <div class="mx-auto w-20 h-20 bg-white rounded-full flex items-center justify-center mb-4 animate-bounce">
                                <svg class="w-12 h-12 text-ojt-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h2 class="text-2xl sm:text-3xl font-bold text-white mb-2">
                                🎉 Congratulations!
                            </h2>
                            <p class="text-green-50 text-sm sm:text-base">
                                Pre-Placement Requirements Complete
                            </p>
                        </div>

                        <!-- Content -->
                        <div class="px-6 py-6 text-center space-y-6">
                            <p class="text-gray-600">
                                You have successfully submitted all required pre-placement documents online.
                            </p>

                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-left">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">Next Step Required</h3>
                                        <div class="mt-2 text-sm text-yellow-700">
                                            <p>Please submit the <strong>hard copies</strong> (printed versions) of your documents to your department coordinator for final verification.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="px-6 py-4 bg-gray-50 flex flex-col sm:flex-row gap-3">
                            <button @click="show = false" 
                                    class="flex-1 px-4 py-3 bg-ojt-primary text-white font-semibold rounded-lg hover:bg-maroon-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                Got it, thanks!
                            </button>
                            <a href="{{ route('notifications.index') }}" 
                               class="flex-1 px-4 py-3 bg-white border-2 border-gray-300 text-gray-700 font-medium text-center rounded-lg hover:bg-gray-50 transition-colors">
                                View Notifications
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif


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
                        (statusValue === 'submitted' && cardStatus !== 'pending');
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
