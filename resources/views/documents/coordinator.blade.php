<x-app-layout>
    <x-slot name="header">
            <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
            Document Checklist
            </h2>
        <p class="text-sm text-gray-500">Monitor and review student document submissions</p>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                @php
                    // Total document submissions across all students
                    $totalSubmissions = $students->sum(function($student) {
                        return $student->documentSubmissions->count();
                    });
                    
                    // Total students in department
                    $totalStudents = $students->count();
                    
                    // Students with all required pre-placement documents submitted (ready for OJT)
                    $studentsReady = $students->filter(function($student) {
                        return $student->studentProfile?->preplacement_complete;
                    })->count();
                    
                    // Students still missing required documents (pending)
                    $studentsPending = max(0, $totalStudents - $studentsReady);
                    
                    // Recent submissions (last 7 days)
                    $recentSubmissions = $students->sum(function($student) {
                        return $student->documentSubmissions->filter(function($submission) {
                            return $submission->created_at && $submission->created_at->isAfter(now()->subDays(7));
                        })->count();
                    });
                @endphp
                
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Submissions</p>
                            <p class="text-2xl font-bold text-ojt-dark">{{ $totalSubmissions }}</p>
                            @if($recentSubmissions > 0)
                                <p class="text-xs text-gray-500 mt-1">{{ $recentSubmissions }} this week</p>
                            @endif
                    </div>
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Pre-placement Complete</p>
                        <p class="text-2xl font-bold text-green-600">{{ $studentsReady }}</p>
                            <p class="text-xs text-gray-500 mt-1">Ready for OJT</p>
                    </div>
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">OJT Status: Pending</p>
                            <p class="text-2xl font-bold text-yellow-600">{{ $studentsPending }}</p>
                            <p class="text-xs text-gray-500 mt-1">Awaiting pre-placement completion</p>
                        </div>
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
                            </div>

            <!-- Tabs and Filters -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <button class="px-4 py-2 rounded-lg text-sm font-medium bg-ojt-primary text-white hover:bg-maroon-700 transition-colors" id="tabQueue">Latest Submissions</button>
                    <button class="px-4 py-2 rounded-lg text-sm font-medium bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors" id="tabAll">All Submissions</button>
                    <button class="px-4 py-2 rounded-lg text-sm font-medium bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors" id="tabPerReq">Per‑Requirement</button>
                    <button class="px-4 py-2 rounded-lg text-sm font-medium bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors" id="tabByStudent">By Student</button>
                        </div>
                        </div>
                <div class="grid grid-cols-1 gap-6 mt-4">
                    <div class="space-y-4">
                        <!-- Latest / All submissions containers -->
                        <div id="queueContainer">
                            <div class="bg-white shadow sm:rounded-lg p-4 sm:p-6">
                                <div class="mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">Latest Submissions</h3>
                                    <p class="text-sm text-gray-500">Most recent document uploads from your students</p>
                                    </div>
                                <div class="max-h-[640px] overflow-y-auto border border-gray-200 rounded-lg">
                                    <div id="queueList" class="divide-y divide-gray-200"></div>
                                </div>
                            </div>
                        </div>
                        <div id="allContainer" class="hidden">
                            <div class="bg-white shadow sm:rounded-lg p-4 sm:p-6">
                                <div class="mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">All Submissions</h3>
                                    <p class="text-sm text-gray-500">Complete history of document submissions</p>
                                    </div>
                                <div class="max-h-[640px] overflow-y-auto border border-gray-200 rounded-lg">
                                    <div id="allList" class="divide-y divide-gray-200"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Per requirement grid -->
                        <div id="perReqGrid" class="hidden">
                            <div class="bg-white shadow sm:rounded-lg p-4 sm:p-6">
                                <div class="mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">Document Requirements</h3>
                                    <p class="text-sm text-gray-500">Click on a requirement to view student submissions</p>
                                </div>
                                <div class="max-h-[640px] overflow-y-auto">
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($requirements as $requirement)
                                            <div class="bg-white rounded-lg border border-gray-200 p-5 hover:shadow-lg transition-all duration-200 border-l-4
                                                {{ $requirement->type === 'pre_placement' ? 'border-l-blue-500' : ($requirement->type === 'post_placement' ? 'border-l-green-500' : 'border-l-purple-500') }}">
                                                <div class="flex items-start justify-between mb-4">
                                                    <div class="flex-1">
                                                        <h3 class="font-semibold text-gray-900 text-sm leading-tight mb-2">
                                                            {{ \Illuminate\Support\Str::limit($requirement->name, 40) }}
                                                        </h3>
                                                        @if($requirement->is_required)
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                Required
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                                Optional
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                        {{ $requirement->type === 'pre_placement' ? 'bg-blue-100 text-blue-800' : ($requirement->type === 'post_placement' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">
                                                        {{ $requirement->type === 'pre_placement' ? 'Pre-Placement' : ($requirement->type === 'post_placement' ? 'Post-Placement' : ucfirst(str_replace('_',' ', $requirement->type))) }}
                                                    </span>
                                                </div>

                                                <div class="space-y-3">
                                                    @php
                                                        $submissionCount = $students->sum(function($student) use ($requirement) {
                                                            return $student->documentSubmissions->where('document_requirement_id', $requirement->id)->count();
                                                        });
                                                        $studentsSubmitted = $students->filter(function($student) use ($requirement) {
                                                            return $student->documentSubmissions->where('document_requirement_id', $requirement->id)->count() > 0;
                                                        })->count();
                                                    @endphp
                                                    
                                                    <div class="grid grid-cols-2 gap-3 text-xs">
                                                        <div class="text-center p-2 bg-gray-50 rounded-lg border border-gray-200">
                                                            <div class="font-semibold text-ojt-dark">{{ $submissionCount }}</div>
                                                            <div class="text-gray-600">Submissions</div>
                                                        </div>
                                                        <div class="text-center p-2 bg-gray-100 rounded-lg border border-gray-300">
                                                            <div class="font-semibold text-ojt-dark">{{ $studentsSubmitted }}</div>
                                                            <div class="text-gray-600">Students</div>
                                                        </div>
                                                    </div>

                                                    <button
                                                        type="button"
                                                        class="block w-full text-center px-4 py-2 text-sm font-medium text-white bg-ojt-primary rounded-lg hover:bg-maroon-700 transition-colors"
                                                        onclick="showDocumentDetails('{{ $requirement->id }}', '{{ addslashes($requirement->name) }}')"
                                                    >
                                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        View Submissions
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- By Student Pane -->
                        <div id="byStudentPane" class="hidden space-y-4">
                            <!-- 1) Student Search & Selection -->
                            <div class="bg-white rounded-lg border border-gray-200 p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div class="relative">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Search Student</label>
                                        <input id="studentSearchInput" placeholder="Type at least 2 digits of student ID..." 
                                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary" />
                                        <!-- Live search suggestions -->
                                        <div id="studentSearchResults" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg hidden max-h-64 overflow-y-auto">
                                            <!-- Suggestions will be injected here -->
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">Search by student ID. Suggestions show after 2 digits.</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Student</label>
                                        <select id="studentPicker" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary">
                                        <option value="">Select a student...</option>
                                    </select>
                                    </div>
                                </div>
                            </div>

                            <!-- 2) Student Info (Profile / Quick summary) -->
                            <aside id="studentSidebar" class="hidden bg-white rounded-lg border border-gray-200 p-4">
                                <div class="flex items-center space-x-3 mb-3">
                                    <div id="sidebarAvatar" class="w-12 h-12 rounded-full bg-ojt-primary flex items-center justify-center text-white font-bold overflow-hidden">S</div>
                                    <div>
                                        <h3 class="text-sm font-semibold text-ojt-dark" id="sidebarName">Student</h3>
                                        <div class="text-xs text-gray-600" id="sidebarId">—</div>
                                </div>
                                </div>
                                <div id="sidebarContent" class="text-sm text-gray-700 space-y-1"></div>
                                <div class="mt-4 pt-4 border-t">

                                    <div id="sidebarChecklist" class="text-xs text-gray-600 space-y-1"></div>
                            </div>
                            </aside>

                            <!-- 3) Requirements Checklist -->
                            <div class="bg-white shadow sm:rounded-lg p-3 sm:p-4 lg:p-6 overflow-hidden">
                                <div class="mb-4">
                                    <h3 class="text-base sm:text-lg font-medium text-gray-900">Student Requirements Checklist</h3>
                                    <p class="text-xs sm:text-sm text-gray-500">Document submission status for selected student</p>
                                </div>
                                <div class="max-h-[640px] overflow-y-auto overflow-x-hidden">
                                    <div id="studentChecklist" class="space-y-3 sm:space-y-4"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Removed old grid; replaced with pinned Quick Stats above -->

    <!-- Enhanced Document Details Modal -->
    <div id="documentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-7xl w-full max-h-[90vh] overflow-hidden shadow-xl">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-ojt-primary rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                    <h3 class="text-lg font-medium text-gray-900" id="modalDocumentName">Document Submissions</h3>
                            <p class="text-sm text-gray-500" id="modalDocumentStats">Loading...</p>
                        </div>
                    </div>
                    <button onclick="closeDocumentModal()" class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                    </button>
                </div>
                
                <!-- Modal Content -->
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <!-- Search -->
                    <div class="mb-6">
                        <input type="text" id="studentSearch" placeholder="Search by student name or ID..." 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-ojt-primary focus:border-ojt-primary">
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="mb-6 flex flex-wrap gap-2">
                        <button onclick="downloadAllSubmissions()" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download All
                        </button>
                        <button onclick="exportSubmissionsList()" class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export List
                        </button>
                    </div>
                    
                    <!-- Students List -->
                    <div id="studentsList" class="space-y-3">
                        <!-- Students will be loaded here -->
                        </div>
                    </div>
            </div>
        </div>
    </div>

    <!-- Review modal removed: coordinators now only preview/download documents -->

    <script>
        let currentDocumentId = null;
        let allStudents = @json($students);
        let allRequirements = @json($requirements);
        const storageBase = "{{ \Illuminate\Support\Facades\Storage::url('') }}";
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
        const queueContainer = document.getElementById('queueContainer');
        const allContainer = document.getElementById('allContainer');
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
            const activeClass = 'px-4 py-2 rounded-lg text-sm font-medium bg-ojt-primary text-white hover:bg-maroon-700 transition-colors';
            const inactiveClass = 'px-4 py-2 rounded-lg text-sm font-medium bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors';
            
            document.getElementById('tabQueue').className = tab==='queue' ? activeClass : inactiveClass;
            document.getElementById('tabAll').className = tab==='all' ? activeClass : inactiveClass;
            document.getElementById('tabPerReq').className = tab==='per' ? activeClass : inactiveClass;
            document.getElementById('tabByStudent').className = tab==='student' ? activeClass : inactiveClass;
            queueContainer.classList.toggle('hidden', tab!=='queue');
            allContainer.classList.toggle('hidden', tab!=='all');
            perReqGrid.classList.toggle('hidden', tab!=='per');
            document.getElementById('byStudentPane').classList.toggle('hidden', tab!=='student');

            if (tab !== 'student') {
                // Leaving By Student tab – clear selection and hide sidebar
                resetByStudentUI();
            } else {
                // Entering By Student tab – ensure picker/search are initialized and blank
                initStudentPicker();
                resetByStudentUI();
            }

            renderLists();
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
                    // Latest submissions: most recent first, limited to keep list compact
                    const queue = filtered
                        .slice()
                        .sort((a,b)=> new Date(b.submission.created_at) - new Date(a.submission.created_at))
                        .slice(0, 50);
                    queueList.innerHTML = queue.length ? queue.map(renderRow).join('') : emptyState('No recent submissions');
                    allList.innerHTML = '';
                } else {
                    const allSorted = filtered.slice().sort((a,b)=> new Date(b.submission.created_at) - new Date(a.submission.created_at));
                    allList.innerHTML = allSorted.length ? allSorted.map(renderRow).join('') : emptyState('No submissions found');
                    queueList.innerHTML = '';
                }
            } else if (currentTab === 'per') {
                // Per‑Requirement tab now uses the server-rendered grid layout;
                // no dynamic re-render is needed here.
            } else if (currentTab === 'student') {
                // Rendering handled by initStudentPicker/select change
            }
        }

        function resetByStudentUI() {
            selectedStudentId = null;
            // Clear sidebar
            sidebar.classList.add('hidden');
            document.getElementById('sidebarName').textContent = 'Student';
            document.getElementById('sidebarId').textContent = '—';
            sidebarContent.innerHTML = '';
            sidebarChecklist.innerHTML = '';

            // Clear search + suggestions
            const search = document.getElementById('studentSearchInput');
            const resultsDiv = document.getElementById('studentSearchResults');
            if (search) search.value = '';
            if (resultsDiv) {
                resultsDiv.innerHTML = '';
                resultsDiv.classList.add('hidden');
            }

            // Reset picker selection
            const picker = document.getElementById('studentPicker');
            if (picker) picker.value = '';

            // Reset checklist message
            const checklist = document.getElementById('studentChecklist');
            if (checklist) checklist.innerHTML = emptyState('Select a student to view requirements');
        }

        function initStudentPicker(){
            const picker = document.getElementById('studentPicker');
            const search = document.getElementById('studentSearchInput');
            const resultsDiv = document.getElementById('studentSearchResults');
            if (!picker || !search || !resultsDiv) return;

            // Helper: handle student selection from either dropdown or live search
            const selectStudent = (id) => {
                const numericId = parseInt(id, 10);
                const s = allStudents.find(x => x.id === numericId);
                if (s) {
                    selectedStudentId = numericId;
                    picker.value = numericId;
                    openSidebar(numericId);
                    renderStudentChecklist(s);
                } else {
                    selectedStudentId = null;
                    sidebar.classList.add('hidden');
                    document.getElementById('studentChecklist').innerHTML = emptyState('Select a student to view requirements');
                }
            };

            const buildPickerOptions = () => {
                const opts = allStudents.map(s =>
                    `<option value="${s.id}">${escapeHtml(s.student_profile?.student_id || '')} • ${escapeHtml(s.name || 'Student')}</option>`
                ).join('');
                picker.innerHTML = `<option value="">Select a student...</option>` + opts;
            };

            const renderLiveSuggestions = () => {
                const q = (search.value || '').trim();

                if (q.length < 2) {
                    resultsDiv.classList.add('hidden');
                    resultsDiv.innerHTML = '';
                    return;
                }

                const qLower = q.toLowerCase();
                const filtered = allStudents
                    .filter(s => {
                        const studentId = (s.student_profile?.student_id || '').toString().toLowerCase();
                        return studentId.includes(qLower);
                    })
                    .slice(0, 3); // show at most 3

                if (!filtered.length) {
                    resultsDiv.innerHTML = '<div class="p-3 text-xs text-gray-500 text-center">No matching students</div>';
                    resultsDiv.classList.remove('hidden');
                    return;
                }

                resultsDiv.innerHTML = filtered.map(s => {
                    const studentId = s.student_profile?.student_id || '';
                    const name = s.name || 'Student';
                    const course = s.student_profile?.course || '';
                    const profileImage = s.student_profile?.profile_image || '';
                    const initials = (name || 'S').trim().split(' ').map(p => p[0]).join('').slice(0,2).toUpperCase();

                    const avatarHtml = profileImage
                        ? `<img src="${storageBase}${profileImage.startsWith('public/') ? profileImage.replace(/^public\//, '') : profileImage}"
                                 alt="${escapeHtml(name)}"
                                 class="w-8 h-8 rounded-full object-cover border border-gray-200">`
                        : `<div class="w-8 h-8 rounded-full bg-ojt-primary flex items-center justify-center text-white text-xs font-semibold">
                               ${initials}
                           </div>`;

                    return `
                        <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0"
                             data-student-id="${s.id}">
                            <div class="flex items-center gap-2">
                                ${avatarHtml}
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-medium text-gray-900 truncate">${escapeHtml(studentId)}</div>
                                    <div class="text-[11px] text-gray-600 truncate">${escapeHtml(name)}</div>
                                    ${course ? `<div class="text-[10px] text-gray-500 truncate">${escapeHtml(course)}</div>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

                // Attach click handlers
                resultsDiv.querySelectorAll('[data-student-id]').forEach(el => {
                    el.addEventListener('click', () => {
                        const studentId = el.getAttribute('data-student-id');
                        const student = allStudents.find(s => s.id === Number(studentId));
                        if (student) {
                            search.value = student.student_profile?.student_id || '';
                        }
                        resultsDiv.classList.add('hidden');
                        selectStudent(studentId);
                    });
                });

                resultsDiv.classList.remove('hidden');
            };
            
            if (!picker.dataset.initialized){
                // Build full dropdown list once
                buildPickerOptions();

                // Live search suggestions based on student ID
                search.addEventListener('input', debounce(renderLiveSuggestions, 200));

                // Dropdown change selection
                picker.addEventListener('change', () => {
                    const id = picker.value;
                    if (id) {
                        selectStudent(id);
                    } else {
                        selectedStudentId = null;
                        sidebar.classList.add('hidden');
                        document.getElementById('studentChecklist').innerHTML = emptyState('Select a student to view requirements');
                    }
                });

                // Hide suggestions when clicking outside
                document.addEventListener('click', (e) => {
                    if (!search.contains(e.target) && !resultsDiv.contains(e.target)) {
                        resultsDiv.classList.add('hidden');
                    }
                });

                picker.dataset.initialized = '1';
            }
        }

        function renderStudentChecklist(student){
            const container = document.getElementById('studentChecklist');
            if (!student){ container.innerHTML = emptyState('Select a student to view requirements'); return; }

            // Build a map of requirementId -> latest submission status
            const subMap = {};
            (student.document_submissions||[]).forEach(sub => {
                const key = String(sub.document_requirement_id);
                if (!subMap[key] || new Date(sub.created_at) > new Date(subMap[key].created_at)) {
                    subMap[key] = sub;
                }
            });

            const groups = {
                pre_placement: [],
                post_placement: []
            };

            allRequirements.forEach(req => {
                // Only include pre_placement and post_placement, skip ongoing
                if (req.type === 'ongoing') return;
                
                const latest = subMap[String(req.id)] || null;
                const hasSubmission = !!latest;
                // Only show "Missing" or "Submitted" - no other statuses
                const displayStatus = hasSubmission ? 'submitted' : 'missing';
                const required = !!req.is_required;
                groups[req.type||'pre_placement'].push({req, latest, displayStatus, required, hasSubmission});
            });

            const renderGroup = (title, arr) => {
                if (arr.length === 0) return '';
                
                const submittedCount = arr.filter(x => x.hasSubmission).length;
                const totalCount = arr.length;
                const missingCount = totalCount - submittedCount;
                
                const header = `
                    <div class=\"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4 pb-2 border-b border-gray-200\">
                        <h3 class=\"text-base font-semibold text-ojt-dark\">${title}</h3>
                        <div class=\"flex items-center gap-2 sm:gap-3 text-xs flex-wrap\">
                            <span class=\"text-green-600 font-medium whitespace-nowrap\">${submittedCount} Submitted</span>
                            <span class=\"text-gray-400 hidden sm:inline\">•</span>
                            <span class=\"text-red-600 font-medium whitespace-nowrap\">${missingCount} Missing</span>
                        </div>
                    </div>`;
                
                const items = arr.map(x => {
                    const isSubmitted = x.hasSubmission;
                    const statusColor = isSubmitted ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200';
                    const statusText = isSubmitted ? 'Submitted' : 'Missing';
                    const statusIcon = isSubmitted 
                        ? '<svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>'
                        : '<svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>';
                    
                    return `
                        <div class=\"border-l-4 ${isSubmitted ? 'border-l-green-500' : 'border-l-red-500'} bg-white border border-gray-200 rounded-lg p-3 sm:p-4 mb-3 hover:shadow-md transition-shadow\">
                            <div class=\"flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3\">
                                <div class=\"flex-1 min-w-0\">
                                    <div class=\"flex flex-wrap items-center gap-2 mb-2\">
                                        <h4 class=\"font-medium text-gray-900 break-words\">${escapeHtml(x.req.name)}</h4>
                                        ${x.required ? '<span class="text-xs px-2 py-0.5 bg-red-100 text-red-700 rounded-full font-medium whitespace-nowrap flex-shrink-0">Required</span>' : '<span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full whitespace-nowrap flex-shrink-0">Optional</span>'}
                        </div>
                            ${x.latest ? `
                                        <div class=\"text-xs text-gray-500 mb-2 break-words\">
                                            <div class=\"mb-1\"><span class=\"font-medium\">File:</span> <span class=\"break-all\">${escapeHtml(x.latest.original_filename || 'Unknown')}</span></div>
                                            <div class=\"flex flex-wrap gap-2\">
                                                <span><span class=\"font-medium\">Size:</span> ${formatFileSize(x.latest.file_size)}</span>
                                                <span><span class=\"font-medium\">Date:</span> ${formatDate(x.latest.created_at)}</span>
                        </div>
                    </div>
                                    ` : ''}
                                </div>
                                <div class=\"flex flex-col sm:items-end gap-2 flex-shrink-0\">
                                    <div class=\"inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap ${isSubmitted ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}\">
                                        ${statusIcon}
                                        ${statusText}
                                    </div>
                                    ${x.latest ? `
                                        <div class=\"flex flex-wrap gap-2 sm:justify-end\">
                                            <a href=\"/documents/submissions/${x.latest.id}/stream\" target=\"_blank\" 
                                               class=\"inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-ojt-primary hover:text-maroon-700 border border-ojt-primary rounded hover:bg-ojt-primary hover:text-white transition-colors whitespace-nowrap\">
                                                Preview
                                            </a>
                                            <a href=\"/documents/submissions/${x.latest.id}/download\" 
                                               class=\"inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-gray-700 border border-gray-300 rounded hover:bg-gray-50 transition-colors whitespace-nowrap\">
                                                Download
                                            </a>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
                
                return `<div class=\"mb-6\">${header}${items}</div>`;
            };

            container.innerHTML = `
                ${renderGroup('Pre‑placement', groups.pre_placement)}
                ${renderGroup('Post‑placement', groups.post_placement)}
            `;
        }

        function renderRow(item) {
            const { submission, student, requirement } = item;
            const avatarInitial = (student.name || 'S').charAt(0).toUpperCase();
            const profileImage = student.student_profile?.profile_image;
            
            return `
                <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1 min-w-0">
                            <div class="flex-shrink-0 h-10 w-10">
                                ${profileImage ? 
                                    `<img class="h-10 w-10 rounded-full object-cover" src="${storageBase}${profileImage}" alt="${escapeHtml(student.name)}">` :
                                    `<div class="h-10 w-10 rounded-full bg-ojt-primary flex items-center justify-center">
                                        <span class="text-white font-medium text-sm">${avatarInitial}</span>
                                    </div>`
                                }
                            </div>
                            <div class="ml-4 flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <div class="text-sm font-medium text-gray-900 truncate">${escapeHtml(student.name)}</div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${getStatusBadge(submission.status)}">
                                        ${getStatusText(submission.status)}
                                    </span>
                            </div>
                                <div class="text-sm text-gray-500 mb-1">${escapeHtml(student.student_profile?.student_id || 'N/A')} • ${escapeHtml(student.student_profile?.course || 'N/A')}</div>
                                <div class="text-xs text-gray-500">
                                    <strong>${escapeHtml(requirement?.name || 'Unknown')}</strong> • ${escapeHtml(submission.original_filename || '')} • ${formatFileSize(submission.file_size)} • ${formatDate(submission.created_at)}
                            </div>
                        </div>
                        </div>
                        <div class="flex items-center space-x-2 ml-4">
                            <a href="/documents/submissions/${submission.id}/stream" target="_blank" 
                               class="text-ojt-primary hover:text-maroon-700 text-sm font-medium">
                                Preview
                            </a>
                            <span class="text-gray-300">|</span>
                            <a href="/documents/submissions/${submission.id}/download" 
                               class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                                Download
                            </a>
                        </div>
                    </div>
                </div>
            `;
        }

        function emptyState(text) { 
            return `<div class=\"text-center py-12 px-6\">
                <svg class=\"w-16 h-16 text-gray-400 mx-auto mb-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\" />
                </svg>
                <p class=\"text-gray-500 text-lg\">${text}</p>
            </div>`; 
        }

        function showDocumentDetails(documentId, documentName) {
            currentDocumentId = documentId;
            document.getElementById('modalDocumentName').textContent = documentName;
            
            // Filter students who have submissions for this document
            const studentsWithSubmissions = allStudents.filter(student => {
                return student.document_submissions.some(submission => 
                    submission.document_requirement_id == documentId
                );
            });
            
            // Update stats
            const totalSubmissions = studentsWithSubmissions.reduce((total, student) => {
                return total + student.document_submissions.filter(sub => sub.document_requirement_id == documentId).length;
            }, 0);
            
            document.getElementById('modalDocumentStats').textContent = 
                `${totalSubmissions} submissions from ${studentsWithSubmissions.length} students`;
            
            renderStudentsList(studentsWithSubmissions, documentId);
            document.getElementById('documentModal').classList.remove('hidden');
            
            // Reset filters
            document.getElementById('studentSearch').value = '';
            document.getElementById('statusFilter').value = '';
        }

        function renderStudentsList(students, documentId) {
            const container = document.getElementById('studentsList');
            
            if (students.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-gray-500 text-lg">No submissions found</p>
                        <p class="text-gray-400 text-sm mt-2">Students haven't submitted this document yet</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = students.map(student => {
                const submissions = student.document_submissions.filter(sub => 
                    sub.document_requirement_id == documentId
                );
                const avatarInitial = (student.name || 'S').charAt(0).toUpperCase();
                const profileImage = student.student_profile?.profile_image;
                
                return submissions.map(submission => `
                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow" 
                         data-student="${student.name.toLowerCase()}" 
                         data-student-id="${student.student_profile?.student_id || ''}" 
                         data-status="${submission.status}">
                        <div class="flex items-start gap-4">
                            <!-- Student Avatar -->
                            <div class="flex-shrink-0">
                                ${profileImage ? 
                                    `<img class="h-12 w-12 rounded-full object-cover border-2 border-gray-200" src="${storageBase}${profileImage}" alt="${escapeHtml(student.name)}">` :
                                    `<div class="h-12 w-12 rounded-full bg-ojt-primary flex items-center justify-center border-2 border-gray-200">
                                        <span class="text-white font-bold text-lg">${avatarInitial}</span>
                                    </div>`
                                }
                            </div>
                            
                            <!-- Student Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <h4 class="font-semibold text-gray-900 truncate">${escapeHtml(student.name)}</h4>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusBadge(submission.status)}">
                                        ${getStatusText(submission.status)}
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-600 mb-3">
                                    <div><strong>ID:</strong> ${escapeHtml(student.student_profile?.student_id || 'N/A')}</div>
                                    <div><strong>Course:</strong> ${escapeHtml(student.student_profile?.course || 'N/A')}</div>
                                    <div><strong>File:</strong> ${escapeHtml(submission.original_filename || 'Unknown')}</div>
                                    <div><strong>Size:</strong> ${formatFileSize(submission.file_size)}</div>
                                </div>
                                
                                <div class="text-xs text-gray-500 mb-3">
                                    <strong>Submitted:</strong> ${formatDate(submission.created_at)}
                                </div>
                                
                                ${submission.feedback ? `
                                    <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                        <div class="text-xs font-medium text-blue-800 mb-1">Coordinator Feedback:</div>
                                        <div class="text-sm text-blue-700">${escapeHtml(submission.feedback)}</div>
                                    </div>
                                ` : ''}
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex flex-col gap-2">
                                <a href="/documents/submissions/${submission.id}/stream" target="_blank" 
                                   class="inline-flex items-center justify-center px-4 py-2 bg-ojt-primary text-white text-sm font-medium rounded-lg hover:bg-maroon-700 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Preview
                                </a>
                                <a href="/documents/submissions/${submission.id}/download" 
                                   class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download
                                </a>
                            </div>
                        </div>
                    </div>
                `).join('');
            }).join('');
            
            // Add search and filter functionality
            setupModalFilters();
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

        // Enhanced modal functionality
        function setupModalFilters() {
            const searchInput = document.getElementById('studentSearch');
            
            function filterSubmissions() {
                const searchTerm = searchInput.value.toLowerCase();
                const studentCards = document.querySelectorAll('#studentsList > div');
                
                studentCards.forEach(card => {
                    const studentName = card.getAttribute('data-student') || '';
                    const studentId = card.getAttribute('data-student-id') || '';
                    
                    const matchesSearch = studentName.includes(searchTerm) || studentId.includes(searchTerm);
                    
                    card.style.display = matchesSearch ? 'block' : 'none';
                });
            }
            
            searchInput.addEventListener('input', filterSubmissions);
        }
        
        function downloadAllSubmissions() {
            if (!currentDocumentId) return;
            
            // Get all cards (visible ones will be those without display:none)
            const allCards = document.querySelectorAll('#studentsList > div');
            const cardsToDownload = Array.from(allCards).filter(card => 
                card.style.display !== 'none'
            );
            
            // If filter hasn't been used, all cards will be visible
            const finalCards = cardsToDownload.length > 0 ? cardsToDownload : Array.from(allCards);
            
            if (finalCards.length === 0) {
                alert('No submissions to download');
                return;
            }
            
            // Download each submission with a delay
            finalCards.forEach((card, index) => {
                const downloadLink = card.querySelector('a[href*="/download"]');
                if (downloadLink) {
                    setTimeout(() => {
                        downloadLink.click();
                    }, index * 1000); // 1 second delay between downloads
                }
            });
        }
        
        function exportSubmissionsList() {
            if (!currentDocumentId) return;
            
            const visibleCards = Array.from(document.querySelectorAll('#studentsList > div')).filter(card => 
                card.style.display !== 'none'
            );
            
            if (visibleCards.length === 0) {
                alert('No submissions to export');
                return;
            }
            
            // Create CSV content
            let csvContent = "Student Name,Student ID,Course,File Name,File Size,Status,Submitted Date\n";
            
            visibleCards.forEach(card => {
                const studentName = card.querySelector('h4').textContent;
                const studentId = card.getAttribute('data-student-id');
                const status = card.getAttribute('data-status');
                const fileInfo = card.textContent;
                
                // Extract file info (this is a simplified version)
                const fileName = fileInfo.match(/File: ([^•]+)/)?.[1]?.trim() || 'Unknown';
                const fileSize = fileInfo.match(/Size: ([^•]+)/)?.[1]?.trim() || 'Unknown';
                const submittedDate = fileInfo.match(/Submitted: ([^\n]+)/)?.[1]?.trim() || 'Unknown';
                const course = fileInfo.match(/Course: ([^\n]+)/)?.[1]?.trim() || 'Unknown';
                
                csvContent += `"${studentName}","${studentId}","${course}","${fileName}","${fileSize}","${status}","${submittedDate}"\n`;
            });
            
            // Download CSV
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `document_submissions_${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
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

            const preReqs = allRequirements.filter(r => r.type === 'pre_placement');
            const postReqs = allRequirements.filter(r => r.type === 'post_placement');

            const renderReqColumn = (title, reqs, extraClass) => {
                if (!reqs.length) {
                    return `
                        <div class=\"${extraClass}\">
                            <h5 class=\"text-[11px] font-semibold text-gray-600 mb-2\">${title}</h5>
                            <div class=\"text-[11px] text-gray-400\">No ${title.toLowerCase()} requirements configured</div>
                        </div>
                    `;
                }

                const items = reqs.map(r => {
                    const sub = (s.document_submissions || []).find(ss => String(ss.document_requirement_id) === String(r.id));
                    const submitted = !!sub;

                    return `
                        <div class=\"flex items-center justify-between py-1\">
                            <div class=\"flex items-start gap-1.5\">
                                <span class=\"mt-[6px] w-1.5 h-1.5 rounded-full ${submitted ? 'bg-green-500' : 'bg-gray-300'}\"></span>
                                <span class=\"text-[11px] text-gray-700\">${escapeHtml(r.name)}</span>
                            </div>
                            <span class=\"inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium
                                ${submitted ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-red-50 text-red-700 border border-red-100'}\">
                                ${submitted ? 'Submitted' : 'Missing'}
                            </span>
                        </div>
                    `;
            }).join('');

                return `
                    <div class=\"${extraClass}\">
                        <h5 class=\"text-[11px] font-semibold text-gray-600 mb-2\">${title}</h5>
                        <div class=\"space-y-1\">
                            ${items}
                        </div>
                    </div>
                `;
            };

            sidebarChecklist.innerHTML = `
                <div class=\"grid grid-cols-1 sm:grid-cols-2 gap-4 sm:divide-x sm:divide-gray-200\">
                    ${renderReqColumn('Pre‑placement', preReqs, 'sm:pr-4')}
                    ${renderReqColumn('Post‑placement', postReqs, 'sm:pl-4')}
                </div>
            `;
        }

        function escapeHtml(str){
            return (str||'').toString().replace(/[&<>\"]/g, function(m){return ({'&':'&amp;','<':'&lt;','>':'&gt;','\"':'&quot;'} )[m];});
        }

        // Init
        setTab('queue');

        // Close modals when clicking outside
        document.getElementById('documentModal').addEventListener('click', function(e) {
            if (e.target === this) closeDocumentModal();
        });

    </script>
</x-app-layout>
