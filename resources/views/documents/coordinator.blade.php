<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                {{ __('Document Submissions Review') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-ojt-dark mb-2">Document Submissions Review</h1>
                <p class="text-gray-600">Review and approve student document submissions for your department.</p>
            </div>

            <!-- Students List -->
            <div class="space-y-6">
                @forelse($students as $student)
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-ojt-dark">{{ $student->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $student->studentProfile?->course }} - {{ $student->studentProfile?->department }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Company: {{ $student->studentProfile?->assignedCompany?->name ?? 'Not assigned' }}</p>
                                <p class="text-sm text-gray-600">Status: {{ ucfirst($student->studentProfile?->ojt_status ?? 'pending') }}</p>
                            </div>
                        </div>

                        <!-- Document Submissions -->
                        <div class="space-y-4">
                            @php
                                $studentSubmissions = $student->documentSubmissions()->with(['requirement', 'reviewer'])->get();
                            @endphp
                            
                            @if($studentSubmissions->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($studentSubmissions as $submission)
                                        <div class="border border-gray-200 rounded-lg p-4">
                                            <div class="flex items-start justify-between mb-2">
                                                <h4 class="font-medium text-gray-900 text-sm">{{ $submission->requirement->name }}</h4>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $submission->status_badge }}">
                                                    {{ $submission->status_text }}
                                                </span>
                                            </div>
                                            
                                            <div class="text-xs text-gray-500 mb-3">
                                                <p>File: {{ $submission->original_filename }}</p>
                                                <p>Size: {{ $submission->file_size_formatted }}</p>
                                                <p>Submitted: {{ $submission->created_at->format('M d, Y') }}</p>
                                            </div>

                                            @if($submission->feedback)
                                                <div class="mb-3 p-2 bg-gray-50 rounded text-xs">
                                                    <strong>Feedback:</strong> {{ $submission->feedback }}
                                                </div>
                                            @endif

                                            <div class="flex space-x-2">
                                                <a href="{{ route('documents.download', $submission) }}" 
                                                   class="text-xs text-ojt-primary hover:text-maroon-700 underline">
                                                    Download
                                                </a>
                                                
                                                @if($submission->status === 'submitted' || $submission->status === 'under_review')
                                                    <button onclick="openReviewModal({{ $submission->id }}, '{{ $submission->requirement->name }}')" 
                                                            class="text-xs text-blue-600 hover:text-blue-800 underline">
                                                        Review
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p>No document submissions yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Students Found</h3>
                        <p class="text-gray-500">No students are assigned to your department yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div id="reviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Review Document</h3>
                
                <form id="reviewForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="status" name="status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-ojt-primary focus:border-ojt-primary">
                            <option value="under_review">Under Review</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="feedback" class="block text-sm font-medium text-gray-700 mb-2">Feedback (Optional)</label>
                        <textarea id="feedback" name="feedback" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-ojt-primary focus:border-ojt-primary" placeholder="Provide feedback to the student..."></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeReviewModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-ojt-primary rounded-md hover:bg-maroon-700">
                            Submit Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openReviewModal(submissionId, documentName) {
            document.getElementById('modalTitle').textContent = `Review: ${documentName}`;
            document.getElementById('reviewForm').action = `/coord/documents/submissions/${submissionId}/review`;
            document.getElementById('reviewModal').classList.remove('hidden');
        }

        function closeReviewModal() {
            document.getElementById('reviewModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('reviewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReviewModal();
            }
        });
    </script>
</x-app-layout>
