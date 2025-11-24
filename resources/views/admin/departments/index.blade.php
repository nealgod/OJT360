<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-ojt-dark">Departments & Programs</h1>
                    <p class="text-gray-600 mt-1">Manage academic departments and their programs</p>
                </div>
                <button onclick="document.getElementById('addDeptModal').classList.remove('hidden')" class="bg-ojt-primary text-white px-6 py-3 rounded-lg hover:bg-maroon-700 transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Department
                </button>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-r-lg shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-green-700 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-r-lg shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-red-700 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Validation Errors -->
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-r-lg shadow-sm">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-red-700 font-medium mb-2">Please fix the following errors:</p>
                            <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Departments List -->
            <div class="space-y-6">
                @forelse($departments as $dept)
                    <div class="bg-white rounded-lg border shadow-sm hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-12 h-12 bg-ojt-primary bg-opacity-10 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-ojt-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-bold text-ojt-dark">{{ $dept->name }}</h3>
                                            @if($dept->slug)
                                                <p class="text-sm text-gray-500">Slug: <span class="font-medium">{{ $dept->slug }}</span></p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex gap-4 mt-3">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            {{ $dept->programs_count }} Programs
                                        </span>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            {{ $dept->coordinators_count }} Coordinators
                                        </span>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button onclick="openEditDept({{ $dept->id }}, '{{ addslashes($dept->name) }}', '{{ $dept->slug }}')" class="px-4 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                        Edit
                                    </button>
                                    <button onclick="openAddProgram({{ $dept->id }}, '{{ addslashes($dept->name) }}')" class="px-4 py-2 text-sm font-medium text-green-600 hover:bg-green-50 rounded-lg transition-colors">
                                        Add Program
                                    </button>
                                    <form action="{{ route('admin.departments.destroy', $dept) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this department? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>

                            @if($dept->programs->count() > 0)
                                <div class="border-t pt-4 mt-4">
                                    <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        Programs
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($dept->programs as $program)
                                            <div class="bg-gray-50 border border-gray-200 p-4 rounded-lg hover:bg-gray-100 transition-colors">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1">
                                                        <p class="font-semibold text-gray-900">{{ $program->name }}</p>
                                                        <div class="mt-1 space-y-1">
                                                            @if($program->slug)
                                                                <p class="text-xs text-gray-600">Slug: <span class="font-medium">{{ $program->slug }}</span></p>
                                                            @endif
                                                            <p class="text-xs text-gray-600">Required Hours: <span class="font-medium text-ojt-primary">{{ $program->required_hours ?? 'Not set' }}</span></p>
                                                        </div>
                                                    </div>
                                                    <div class="flex gap-1 ml-2">
                                                        <button onclick="openEditProgram({{ $program->id }}, '{{ addslashes($program->name) }}', '{{ $program->slug }}', {{ $program->required_hours ?? 0 }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded transition-colors" title="Edit">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                        </button>
                                                        <form action="{{ route('admin.programs.destroy', $program) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this program?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded transition-colors" title="Delete">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg border p-12 text-center">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <p class="text-gray-500 text-lg font-medium">No departments found</p>
                        <p class="text-gray-400 text-sm mt-1">Click "Add Department" to create your first department</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
        </div>
    </div>

    <!-- Add Department Modal -->
    <div id="addDeptModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
            <div class="p-6 border-b">
                <h3 class="text-xl font-bold text-ojt-dark">Add New Department</h3>
                <p class="text-sm text-gray-600 mt-1">Create a new academic department</p>
            </div>
            <form action="{{ route('admin.departments.store') }}" method="POST" class="p-6">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required value="{{ old('name') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary" placeholder="e.g., Information Technology">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department Code</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary" placeholder="e.g., it">
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-ojt-primary text-white px-6 py-3 rounded-lg hover:bg-maroon-700 transition-colors font-medium">
                        Create Department
                    </button>
                    <button type="button" onclick="document.getElementById('addDeptModal').classList.add('hidden')" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Department Modal -->
    <div id="editDeptModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
            <div class="p-6 border-b">
                <h3 class="text-xl font-bold text-ojt-dark">Edit Department</h3>
                <p class="text-sm text-gray-600 mt-1">Update department information</p>
            </div>
            <form id="editDeptForm" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="editDeptName" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department Code</label>
                    <input type="text" name="slug" id="editDeptSlug" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary">
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-ojt-primary text-white px-6 py-3 rounded-lg hover:bg-maroon-700 transition-colors font-medium">
                        Update Department
                    </button>
                    <button type="button" onclick="document.getElementById('editDeptModal').classList.add('hidden')" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Program Modal -->
    <div id="addProgramModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
            <div class="p-6 border-b">
                <h3 class="text-xl font-bold text-ojt-dark">Add Program</h3>
                <p class="text-sm text-gray-600 mt-1">Add a new program to <span id="addProgramDeptName" class="font-semibold text-ojt-primary"></span></p>
            </div>
            <form id="addProgramForm" method="POST" class="p-6">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Program Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary" placeholder="e.g., Bachelor of Science in Information Technology">
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Program Code</label>
                    <input type="text" name="slug" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary" placeholder="e.g., bsit">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Required OJT Hours</label>
                    <input type="number" name="required_hours" min="0" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary" placeholder="e.g., 486">
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-ojt-primary text-white px-6 py-3 rounded-lg hover:bg-maroon-700 transition-colors font-medium">
                        Create Program
                    </button>
                    <button type="button" onclick="document.getElementById('addProgramModal').classList.add('hidden')" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Program Modal -->
    <div id="editProgramModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
            <div class="p-6 border-b">
                <h3 class="text-xl font-bold text-ojt-dark">Edit Program</h3>
                <p class="text-sm text-gray-600 mt-1">Update program information</p>
            </div>
            <form id="editProgramForm" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Program Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="editProgramName" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary">
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Program Code</label>
                    <input type="text" name="slug" id="editProgramSlug" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Required OJT Hours</label>
                    <input type="number" name="required_hours" id="editProgramHours" min="0" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary">
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-ojt-primary text-white px-6 py-3 rounded-lg hover:bg-maroon-700 transition-colors font-medium">
                        Update Program
                    </button>
                    <button type="button" onclick="document.getElementById('editProgramModal').classList.add('hidden')" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditDept(id, name, slug) {
            document.getElementById('editDeptForm').action = `/admin/departments/${id}`;
            document.getElementById('editDeptName').value = name;
            document.getElementById('editDeptSlug').value = slug || '';
            document.getElementById('editDeptModal').classList.remove('hidden');
        }

        function openAddProgram(deptId, deptName) {
            document.getElementById('addProgramForm').action = `/admin/departments/${deptId}/programs`;
            document.getElementById('addProgramDeptName').textContent = deptName;
            document.getElementById('addProgramModal').classList.remove('hidden');
        }

        function openEditProgram(id, name, slug, hours) {
            document.getElementById('editProgramForm').action = `/admin/programs/${id}`;
            document.getElementById('editProgramName').value = name;
            document.getElementById('editProgramSlug').value = slug || '';
            document.getElementById('editProgramHours').value = hours || '';
            document.getElementById('editProgramModal').classList.remove('hidden');
        }
    </script>
</x-app-layout>
