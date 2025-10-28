<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                {{ __('Document Review') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-ojt-dark mb-2">Document Review</h1>
                <p class="text-gray-600">Review student document submissions by document type.</p>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                @php
                    $totalSubmissions = $students->sum(function($student) {
                        return $student->documentSubmissions->count();
                    });
                    $pendingSubmissions = $students->sum(function($student) {
                        return $student->documentSubmissions->where('status', 'submitted')->count();
                    });
                    $underReviewSubmissions = $students->sum(function($student) {
                        return $student->documentSubmissions->where('status', 'under_review')->count();
                    });
                    $approvedSubmissions = $students->sum(function($student) {
                        return $student->documentSubmissions->where('status', 'approved')->count();
                    });
                @endphp
                
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ $totalSubmissions }}</p>
                        <p class="text-sm text-gray-500">Total</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-yellow-600">{{ $pendingSubmissions }}</p>
                        <p class="text-sm text-gray-500">Pending</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-orange-600">{{ $underReviewSubmissions }}</p>
                        <p class="text-sm text-gray-500">Reviewing</p>
                    </div>
            </div>

                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600">{{ $approvedSubmissions }}</p>
                        <p class="text-sm text-gray-500">Approved</p>
                            </div>
                            </div>
                        </div>

            <!-- Document Types Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($requirements as $requirement)
                    @php
                        $submissions = collect();
                        foreach($students as $student) {
                            $studentSubmissions = $student->documentSubmissions->where('document_requirement_id', $requirement->id);
                            $submissions = $submissions->merge($studentSubmissions);
                        }
                        
                        $pendingCount = $submissions->where('status', 'submitted')->count();
                        $underReviewCount = $submissions->where('status', 'under_review')->count();
                        $approvedCount = $submissions->where('status', 'approved')->count();
                        $rejectedCount = $submissions->where('status', 'rejected')->count();
                        $totalCount = $submissions->count();
                            @endphp
                            
                    <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow cursor-pointer" 
                         onclick="showDocumentDetails('{{ $requirement->id }}', '{{ $requirement->name }}')">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $requirement->name }}</h3>
                                <p class="text-sm text-gray-600 mb-3">{{ $requirement->description }}</p>
                                
                                <!-- Document Type Badge -->
                                <div class="mb-4">
                                    @if($requirement->type === 'pre_placement')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Pre-Placement
                                        </span>
                                    @elseif($requirement->type === 'post_placement')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Post-Placement
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            Ongoing
                                                </span>
                                    @endif
                                </div>
                            </div>
                                            </div>
                                            
                        <!-- Submission Stats -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Total Submissions:</span>
                                <span class="font-medium">{{ $totalCount }}</span>
                                            </div>

                            @if($pendingCount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-yellow-600">Pending:</span>
                                    <span class="font-medium text-yellow-600">{{ $pendingCount }}</span>
                                </div>
                            @endif
                            
                            @if($underReviewCount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-orange-600">Under Review:</span>
                                    <span class="font-medium text-orange-600">{{ $underReviewCount }}</span>
                                                </div>
                                            @endif

                            @if($approvedCount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-green-600">Approved:</span>
                                    <span class="font-medium text-green-600">{{ $approvedCount }}</span>
                                </div>
                            @endif
                            
                            @if($rejectedCount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-red-600">Rejected:</span>
                                    <span class="font-medium text-red-600">{{ $rejectedCount }}</span>
                                </div>
                                                @endif
                                            </div>
                        
                        <!-- Click to view message -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-xs text-gray-500 text-center">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Click to view submissions
                            </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                        </div>
                    </div>

    <!-- Document Details Modal -->
    <div id="documentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-6xl w-full max-h-[90vh] overflow-hidden">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900" id="modalDocumentName">Document Submissions</h3>
                    <button onclick="closeDocumentModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                    </button>
                </div>
                
                <!-- Modal Content -->
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <!-- Search -->
                    <div class="mb-6">
                        <input type="text" id="studentSearch" placeholder="Search by student name..." 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-ojt-primary focus:border-ojt-primary">
                    </div>
                    
                    <!-- Students List -->
                    <div id="studentsList" class="space-y-4">
                        <!-- Students will be loaded here -->
                        </div>
                    </div>
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
        let currentDocumentId = null;
        let allStudents = @json($students);

        function showDocumentDetails(documentId, documentName) {
            currentDocumentId = documentId;
            document.getElementById('modalDocumentName').textContent = `${documentName} - Submissions`;
            
            // Filter students who have submissions for this document
            const studentsWithSubmissions = allStudents.filter(student => {
                return student.document_submissions.some(submission => 
                    submission.document_requirement_id == documentId
                );
            });
            
            renderStudentsList(studentsWithSubmissions, documentId);
            document.getElementById('documentModal').classList.remove('hidden');
        }

        function renderStudentsList(students, documentId) {
            const container = document.getElementById('studentsList');
            
            if (students.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p>No submissions for this document type yet.</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = students.map(student => {
                const submissions = student.document_submissions.filter(sub => 
                    sub.document_requirement_id == documentId
                );
                
                return submissions.map(submission => `
                    <div class="bg-gray-50 rounded-lg p-4" data-student="${student.name}" data-status="${submission.status}">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h4 class="font-medium text-gray-900">${student.name}</h4>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${getStatusBadge(submission.status)}">
                                        ${getStatusText(submission.status)}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600 mb-2">
                                    ${student.student_profile?.course || 'Unknown'} - ${student.student_profile?.department || 'Unknown'}
                                </div>
                                <div class="text-sm text-gray-500">
                                    <strong>File:</strong> ${submission.original_filename || 'Unknown'} • 
                                    <strong>Size:</strong> ${formatFileSize(submission.file_size)} • 
                                    <strong>Submitted:</strong> ${formatDate(submission.created_at)}
                                </div>
                                ${submission.feedback ? `
                                    <div class="mt-2 text-sm text-gray-600 bg-white p-2 rounded border">
                                        <strong>Feedback:</strong> ${submission.feedback}
                                    </div>
                                ` : ''}
                            </div>
                            
                            <div class="flex items-center space-x-2 ml-4">
                                <a href="/documents/submissions/${submission.id}/download" 
                                   class="text-sm text-ojt-primary hover:text-maroon-700 underline">
                                    Download
                                </a>
                                
                                ${submission.status === 'submitted' || submission.status === 'under_review' ? `
                                    <button onclick="openReviewModal(${submission.id}, '${student.name}', '${submission.status}')" 
                                            class="text-sm text-blue-600 hover:text-blue-800 underline">
                                        Review
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `).join('');
            }).join('');
        }

        function getStatusBadge(status) {
            const badges = {
                'submitted': 'bg-blue-100 text-blue-800',
                'under_review': 'bg-yellow-100 text-yellow-800',
                'approved': 'bg-green-100 text-green-800',
                'rejected': 'bg-red-100 text-red-800'
            };
            return badges[status] || 'bg-gray-100 text-gray-800';
        }

        function getStatusText(status) {
            const texts = {
                'submitted': 'Submitted',
                'under_review': 'Under Review',
                'approved': 'Approved',
                'rejected': 'Rejected'
            };
            return texts[status] || 'Unknown';
        }

        function formatFileSize(bytes) {
            if (!bytes) return 'Unknown';
            const units = ['B', 'KB', 'MB', 'GB'];
            let size = parseInt(bytes);
            let unitIndex = 0;
            
            while (size >= 1024 && unitIndex < units.length - 1) {
                size /= 1024;
                unitIndex++;
            }
            
            return Math.round(size * 100) / 100 + ' ' + units[unitIndex];
        }

        function formatDate(dateString) {
            if (!dateString) return 'Unknown';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
        }

        function closeDocumentModal() {
            document.getElementById('documentModal').classList.add('hidden');
            currentDocumentId = null;
        }

        // Search functionality
        document.getElementById('studentSearch').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const studentCards = document.querySelectorAll('#studentsList > div');
            
            studentCards.forEach(card => {
                const studentName = card.getAttribute('data-student').toLowerCase();
                if (studentName.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Review Modal Functions
        function openReviewModal(submissionId, studentName, currentStatus) {
            document.getElementById('modalTitle').textContent = `Review: ${studentName}`;
            document.getElementById('reviewForm').action = `/coord/documents/submissions/${submissionId}/review`;
            
            const statusSelect = document.getElementById('status');
            statusSelect.value = currentStatus || 'under_review';
            
            document.getElementById('reviewModal').classList.remove('hidden');
        }

        function closeReviewModal() {
            document.getElementById('reviewModal').classList.add('hidden');
            document.getElementById('reviewForm').reset();
        }

        // Close modals when clicking outside
        document.getElementById('documentModal').addEventListener('click', function(e) {
            if (e.target === this) closeDocumentModal();
        });

        document.getElementById('reviewModal').addEventListener('click', function(e) {
            if (e.target === this) closeReviewModal();
        });
    </script>
</x-app-layout>
