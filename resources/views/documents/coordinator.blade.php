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

            <!-- Quick Stats (pinned above) -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                @php
                    $totalSubmissions = $students->sum(function($student) {
                        return $student->documentSubmissions->count();
                    });
                    $pendingSubmissions = $students->sum(function($student) {
                        return $student->documentSubmissions->where('status', 'submitted')->count();
                    });
                    $approvedSubmissions = $students->sum(function($student) {
                        return $student->documentSubmissions->where('status', 'approved')->count();
                    });
                    $rejectedSubmissions = $students->sum(function($student) {
                        return $student->documentSubmissions->where('status', 'rejected')->count();
                    });
                @endphp
                <div class="bg-white rounded-lg border border-gray-200 p-4"><div class="text-center"><p class="text-2xl font-bold text-gray-900">{{ $totalSubmissions }}</p><p class="text-sm text-gray-500">Total</p></div></div>
                <div class="bg-white rounded-lg border border-gray-200 p-4"><div class="text-center"><p class="text-2xl font-bold text-yellow-600">{{ $pendingSubmissions }}</p><p class="text-sm text-gray-500">Pending</p></div></div>
                <div class="bg-white rounded-lg border border-gray-200 p-4"><div class="text-center"><p class="text-2xl font-bold text-green-600">{{ $approvedSubmissions }}</p><p class="text-sm text-gray-500">Approved</p></div></div>
                <div class="bg-white rounded-lg border border-gray-200 p-4"><div class="text-center"><p class="text-2xl font-bold text-red-600">{{ $rejectedSubmissions }}</p><p class="text-sm text-gray-500">Rejected</p></div></div>
            </div>

            <!-- Tabs -->
            <div class="mb-6">
                <div class="flex items-center space-x-2 mb-3">
                    <button class="px-4 py-2 rounded-lg text-sm font-medium bg-ojt-primary text-white hover:bg-maroon-700" id="tabQueue">Needs Review</button>
                    <button class="px-4 py-2 rounded-lg text-sm font-medium bg-white border hover:bg-gray-50" id="tabAll">All Submissions</button>
                    <button class="px-4 py-2 rounded-lg text-sm font-medium bg-white border hover:bg-gray-50" id="tabPerReq">Per‑Requirement</button>
                    <button class="px-4 py-2 rounded-lg text-sm font-medium bg-white border hover:bg-gray-50" id="tabByStudent">By Student</button>
                </div>
                <!-- Move sidebar above lists for consistent layout -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">
                    <aside id="studentSidebar" class="hidden bg-white rounded-lg border border-gray-200 p-4 h-max lg:col-span-1 order-1 lg:order-none">
                        <div class="flex items-center space-x-3 mb-3">
                            <div id="sidebarAvatar" class="w-12 h-12 rounded-full bg-ojt-primary flex items-center justify-center text-white font-bold overflow-hidden">S</div>
                            <div>
                                <h3 class="text-sm font-semibold text-ojt-dark" id="sidebarName">Student</h3>
                                <div class="text-xs text-gray-600" id="sidebarId">—</div>
                            </div>
                        </div>
                        <div id="sidebarContent" class="text-sm text-gray-700 space-y-1"></div>
                        <div class="mt-4 pt-4 border-t">
                            <h4 class="text-xs font-medium text-gray-500 mb-2">Pre‑requirements</h4>
                            <div id="sidebarChecklist" class="text-xs text-gray-600 space-y-1"></div>
                        </div>
                    </aside>
                    <div class="lg:col-span-2 order-2 lg:order-none">
                        <div id="queueList" class="space-y-3"></div>
                        <div id="allList" class="space-y-3 hidden"></div>
                        <div id="perReqGrid" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($requirements as $requirement)
                                <div class="bg-white rounded-lg border border-gray-200 p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="text-sm font-semibold text-ojt-dark">{{ $requirement->name }}</h3>
                                        <span class="text-xs px-2 py-0.5 rounded-full {{ $requirement->type === 'pre_placement' ? 'bg-blue-100 text-blue-800' : ($requirement->type === 'post_placement' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">{{ ucfirst(str_replace('_',' ', $requirement->type)) }}</span>
                                    </div>
                                    <button class="inline-flex items-center px-3 py-1.5 bg-ojt-primary text-white text-xs font-medium rounded-lg hover:bg-maroon-700 transition-colors"
                                            onclick="showDocumentDetails('{{ $requirement->id }}', '{{ addslashes($requirement->name) }}')">
                                        View Submissions
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <!-- By Student Pane -->
                        <div id="byStudentPane" class="hidden">
                            <!-- Student Search & Selection -->
                            <div class="bg-white rounded-lg border border-gray-200 p-4 mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <input id="studentSearchInput" placeholder="Search student name or ID..." class="px-3 py-2 border rounded-md focus:ring-ojt-primary focus:border-ojt-primary" />
                                    <select id="studentPicker" class="px-3 py-2 border rounded-md focus:ring-ojt-primary focus:border-ojt-primary">
                                        <option value="">Select a student...</option>
                                    </select>
                </div>
            </div>

                            <!-- Requirements Checklist -->
                            <div id="studentChecklist" class="space-y-4"></div>
                </div>
                    </div>
                    </div>
            </div>

            <!-- Removed old grid; replaced with pinned Quick Stats above -->

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
        let allRequirements = @json($requirements);
        const storageBase = "{{ asset('storage') }}/";
        let currentTab = 'queue';
        let selectedStudentId = null;

        // Flatten submissions for filters
        const allSubmissions = [];
        allStudents.forEach(s => {
            (s.document_submissions || []).forEach(sub => {
                const req = allRequirements.find(r => r.id === sub.document_requirement_id);
                allSubmissions.push({ submission: sub, student: s, requirement: req || {} });
            });
        });

        const queueList = document.getElementById('queueList');
        const allList = document.getElementById('allList');
        const perReqGrid = document.getElementById('perReqGrid');
        const sidebar = document.getElementById('studentSidebar');
        const sidebarContent = document.getElementById('sidebarContent');
        const sidebarChecklist = document.getElementById('sidebarChecklist');

        // Tabs
        document.getElementById('tabQueue').addEventListener('click', () => setTab('queue'));
        document.getElementById('tabAll').addEventListener('click', () => setTab('all'));
        document.getElementById('tabPerReq').addEventListener('click', () => setTab('per'));
        document.getElementById('tabByStudent').addEventListener('click', () => setTab('student'));

        function setTab(tab) {
            currentTab = tab;
            document.getElementById('tabQueue').className = 'px-4 py-2 rounded-lg text-sm font-medium ' + (tab==='queue'?'bg-ojt-primary text-white':'bg-white border');
            document.getElementById('tabAll').className = 'px-4 py-2 rounded-lg text-sm font-medium ' + (tab==='all'?'bg-ojt-primary text-white':'bg-white border');
            document.getElementById('tabPerReq').className = 'px-4 py-2 rounded-lg text-sm font-medium ' + (tab==='per'?'bg-ojt-primary text-white':'bg-white border');
            document.getElementById('tabByStudent').className = 'px-4 py-2 rounded-lg text-sm font-medium ' + (tab==='student'?'bg-ojt-primary text-white':'bg-white border');
            queueList.classList.toggle('hidden', tab!=='queue');
            allList.classList.toggle('hidden', tab!=='all');
            perReqGrid.classList.toggle('hidden', tab!=='per');
            document.getElementById('byStudentPane').classList.toggle('hidden', tab!=='student');
            // Sidebar should only appear in By Student tab
            if (tab !== 'student') {
                sidebar.classList.add('hidden');
            } else if (selectedStudentId) {
                openSidebar(selectedStudentId);
            }
            renderLists();
            if (tab==='student') initStudentPicker();
        }

        // Filters scoped to current tab
        // Simple render trigger only
        function debounce(fn, delay=200){ let t; return (...args)=>{ clearTimeout(t); t=setTimeout(()=>fn(...args), delay); }; }

        function renderLists() {
            const q = '';
            const reqId = '';
            const status = '';
            const type = '';

            if (currentTab === 'queue' || currentTab === 'all') {
                const filtered = allSubmissions;

                if (currentTab === 'queue') {
                    const queue = filtered.filter(i => i.submission.status==='submitted')
                                          .sort((a,b)=> new Date(b.submission.created_at) - new Date(a.submission.created_at));
                    queueList.innerHTML = queue.length ? queue.map(renderRow).join('') : emptyState('No items to review');
                    allList.innerHTML = '';
                } else {
                    const allSorted = filtered.slice().sort((a,b)=> new Date(b.submission.created_at) - new Date(a.submission.created_at));
                    allList.innerHTML = allSorted.length ? allSorted.map(renderRow).join('') : emptyState('No submissions found');
                    queueList.innerHTML = '';
                }
            } else if (currentTab === 'per') {
                // Filter requirements by search/type only (status not applicable here)
                const reqs = allRequirements;
                if (reqs.length === 0) {
                    perReqGrid.innerHTML = emptyState('No requirements match your filters');
                } else {
                    perReqGrid.innerHTML = reqs.map(requirement => `
                        <div class=\"bg-white rounded-lg border border-gray-200 p-4\">
                            <div class=\"flex items-center justify-between mb-2\">
                                <h3 class=\"text-sm font-semibold text-ojt-dark\">${escapeHtml(requirement.name)}</h3>
                                <span class=\"text-xs px-2 py-0.5 rounded-full ${requirement.type === 'pre_placement' ? 'bg-blue-100 text-blue-800' : (requirement.type === 'post_placement' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800')}\">${(requirement.type||'').replace('_',' ')}</span>
                            </div>
                            <button class=\"inline-flex items-center px-3 py-1.5 bg-ojt-primary text-white text-xs font-medium rounded-lg hover:bg-maroon-700 transition-colors\"
                                    onclick=\"showDocumentDetails('${requirement.id}', '${escapeHtml(requirement.name)}')\">
                                View Submissions
                            </button>
                        </div>
                    `).join('');
                }
            } else if (currentTab === 'student') {
                // Rendering handled by initStudentPicker/select change
            }
        }

        function initStudentPicker(){
            const picker = document.getElementById('studentPicker');
            const search = document.getElementById('studentSearchInput');
            if (!picker || !search) return;
            
            const renderOptions = () => {
                const q = (search.value||'').toLowerCase();
                const filtered = allStudents.filter(s => 
                    ((s.name||'') + ' ' + (s.student_profile?.student_id||'')).toLowerCase().includes(q)
                );
                const opts = filtered.map(s => 
                    `<option value="${s.id}">${escapeHtml(s.name||'Student')} • ${escapeHtml(s.student_profile?.student_id||'')}</option>`
                ).join('');
                picker.innerHTML = `<option value="">Select a student...</option>` + opts;
            };
            
            if (!picker.dataset.initialized){
                search.addEventListener('input', debounce(renderOptions, 150));
                picker.addEventListener('change', () => {
                    const id = parseInt(picker.value, 10);
                    const s = allStudents.find(x => x.id === id);
                    if (s) {
                        selectedStudentId = id;
                        openSidebar(id); // Show profile in sidebar
                        renderStudentChecklist(s);
                    } else {
                        selectedStudentId = null;
                        sidebar.classList.add('hidden');
                        document.getElementById('studentChecklist').innerHTML = emptyState('Select a student to view requirements');
                    }
                });
                picker.dataset.initialized = '1';
                renderOptions();
            }
        }

        function renderStudentChecklist(student){
            const container = document.getElementById('studentChecklist');
            if (!student){ container.innerHTML = emptyState('Select a student to view requirements'); return; }

            // Build a map of requirementId -> latest submission status
            const subMap = {};
            (student.document_submissions||[]).forEach(sub => {
                const key = sub.document_requirement_id;
                if (!subMap[key] || new Date(sub.created_at) > new Date(subMap[key].created_at)) {
                    subMap[key] = sub;
                }
            });

            const groups = {
                pre_placement: [],
                ongoing: [],
                post_placement: []
            };

            allRequirements.forEach(req => {
                const latest = subMap[req.id] || null;
                const status = latest ? latest.status : 'pending';
                const satisfied = status === 'approved';
                const required = !!req.is_required;
                const missing = required && !satisfied;
                groups[req.type||'ongoing'].push({req, latest, status, required, missing});
            });

            const renderGroup = (title, arr) => {
                const missingCount = arr.filter(x => x.missing).length;
                const totalRequired = arr.filter(x => x.required).length;
                const header = `
                    <div class=\"flex items-center justify-between mb-2\">
                        <h3 class=\"text-sm font-semibold text-ojt-dark\">${title}</h3>
                        <span class=\"text-xs ${missingCount? 'text-red-700' : 'text-green-700'}\">${missingCount} missing of ${totalRequired} required</span>
                    </div>`;
                
                const items = arr.map(x => `
                    <div class=\"flex items-start justify-between border rounded-lg p-3 mb-2\">
                        <div class=\"text-sm\">
                            <div class=\"font-medium\">${escapeHtml(x.req.name)}</div>
                            <div class=\"text-xs text-gray-500\">${x.required? 'Required' : 'Optional'} • Types: ${escapeHtml((x.req.file_types||[]).join(', ')||'Any')} • Max: ${escapeHtml(String(x.req.max_file_size_mb||'')+' MB')}</div>
                            ${x.latest && x.latest.feedback ? `<div class=\"mt-1 text-xs text-blue-800 bg-blue-50 border border-blue-200 rounded px-2 py-1\">Feedback: ${escapeHtml(x.latest.feedback)}</div>` : ''}
                        </div>
                        <div class=\"text-xs text-right\">
                            <span class=\"inline-flex items-center px-2 py-0.5 rounded-full ${getStatusBadge(x.status)}\">${getStatusText(x.status)}</span>
                            ${x.latest ? `<div class=\"text-gray-500 mt-1\">${formatDate(x.latest.created_at)}</div>` : ''}
                            ${x.latest ? `
                                <div class=\"mt-2 space-x-2\">
                                    <a href=\"/documents/submissions/${x.latest.id}/stream\" target=\"_blank\" class=\"inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200\">Preview</a>
                                    <a href=\"/documents/submissions/${x.latest.id}/download\" class=\"inline-flex items-center px-2 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200\">Download</a>
                                    ${x.latest.status === 'submitted' ? `
                                        <button onclick=\"openReviewModal(${x.latest.id}, '${escapeHtml(student.name)}', '${x.latest.status}')\" class=\"inline-flex items-center px-2 py-1 bg-ojt-primary text-white rounded hover:bg-maroon-700\">Review</button>
                                    ` : ''}
                                </div>` : ''}
                        </div>
                    </div>
                `).join('');
                
                return `<div class=\"bg-white rounded-lg border border-gray-200 p-4\">${header}${items || '<div class=\"text-xs text-gray-500\">No requirements</div>'}</div>`;
            };

            container.innerHTML = `
                ${renderGroup('Pre‑placement', groups.pre_placement)}
                ${renderGroup('Ongoing', groups.ongoing)}
                ${renderGroup('Post‑placement', groups.post_placement)}
            `;
        }

        function renderRow(item) {
            const { submission, student, requirement } = item;
            return `
                <div class="bg-white border rounded-lg p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="text-sm font-medium text-ojt-dark">${student.name}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${getStatusBadge(submission.status)}">${getStatusText(submission.status)}</span>
                            </div>
                            <div class="text-xs text-gray-600 mb-1">${student.student_profile?.student_id || ''} • ${(student.student_profile?.course||'') + ' - ' + (student.student_profile?.department||'')}</div>
                            <div class="text-xs text-gray-500">${requirement?.name || '—'} • ${submission.original_filename || ''} • ${formatFileSize(submission.file_size)} • ${formatDate(submission.created_at)}</div>
                        </div>
                        <div class="flex items-center space-x-2 ml-4">
                            <a href="/documents/submissions/${submission.id}/stream" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 text-xs font-medium rounded-lg hover:bg-blue-200 transition-colors">Preview</a>
                            <a href="/documents/submissions/${submission.id}/download" class="inline-flex items-center px-3 py-1.5 bg-white border text-xs font-medium rounded-lg hover:bg-gray-50 transition-colors">Download</a>
                            ${(submission.status==='submitted') ? `
                                <button class="inline-flex items-center px-3 py-1.5 bg-ojt-primary text-white text-xs font-medium rounded-lg hover:bg-maroon-700 transition-colors" onclick="openReviewModal(${submission.id}, '${escapeHtml(student.name)}', '${submission.status}')">Review</button>
                            `: ''}
                        </div>
                    </div>
                </div>
            `;
        }

        function emptyState(text) { return `<div class=\"text-center text-gray-500 bg-white border rounded-lg p-6\">${text}</div>`; }

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
                                <a href="/documents/submissions/${submission.id}/stream" target="_blank" 
                                   class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 text-xs font-medium rounded-lg hover:bg-blue-200 transition-colors">
                                    Preview
                                </a>
                                <a href="/documents/submissions/${submission.id}/download" 
                                   class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors">
                                    Download
                                </a>
                                
                                ${submission.status === 'submitted' ? `
                                    <button onclick="openReviewModal(${submission.id}, '${student.name}', '${submission.status}')" 
                                            class="inline-flex items-center px-3 py-1.5 bg-ojt-primary text-white text-xs font-medium rounded-lg hover:bg-maroon-700 transition-colors">
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
                'approved': 'bg-green-100 text-green-800',
                'rejected': 'bg-red-100 text-red-800'
            };
            return badges[status] || 'bg-gray-100 text-gray-800';
        }

        function getStatusText(status) {
            const texts = {
                'submitted': 'Submitted',
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
            statusSelect.value = currentStatus || 'submitted';
            
            document.getElementById('reviewModal').classList.remove('hidden');
        }


        function openSidebar(studentId) {
            const s = allStudents.find(x => x.id === studentId);
            if (!s) return;
            sidebar.classList.remove('hidden');
            document.getElementById('sidebarName').textContent = s.name || 'Student';
            document.getElementById('sidebarId').textContent = s.student_profile?.student_id || '—';
            const avatar = document.getElementById('sidebarAvatar');
            const imgPath = s.student_profile?.profile_image || '';
            if (imgPath) {
                // Normalize possible 'public/' prefix
                const normalized = imgPath.startsWith('public/') ? imgPath.replace(/^public\//, '') : imgPath;
                avatar.innerHTML = `<img src="${storageBase}${normalized}" alt="Avatar" class="w-12 h-12 object-cover">`;
            } else {
            const initials = (s.name||'S').trim().split(' ').map(p=>p[0]).join('').slice(0,2).toUpperCase();
            avatar.textContent = initials;
            }
            sidebarContent.innerHTML = `
                <div><span class=\"font-medium\">Program:</span> ${escapeHtml(s.student_profile?.course||'')}</div>
                <div><span class=\"font-medium\">Department:</span> ${escapeHtml(s.student_profile?.department||'')}</div>
                <div><span class=\"font-medium\">Email:</span> ${escapeHtml(s.email||'')}</div>
            `;
            const preReqs = allRequirements.filter(r => r.type==='pre_placement');
            sidebarChecklist.innerHTML = preReqs.map(r => {
                const sub = (s.document_submissions||[]).find(ss => ss.document_requirement_id === r.id);
                const ok = sub && sub.status === 'approved';
                return `<div class=\"flex items-center justify-between\"><span>${escapeHtml(r.name)}</span><span class=\"text-xs ${ok?'text-green-700':'text-gray-500'}\">${ok?'Approved':'Pending'}</span></div>`;
            }).join('');
        }

        function escapeHtml(str){
            return (str||'').toString().replace(/[&<>\"]/g, function(m){return ({'&':'&amp;','<':'&lt;','>':'&gt;','\"':'&quot;'} )[m];});
        }

        // Init
        setTab('queue');

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
