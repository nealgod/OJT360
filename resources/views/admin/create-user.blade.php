<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center space-x-4 mb-4">
                    <a href="{{ route('admin.users') }}" class="text-ojt-primary hover:text-maroon-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-ojt-dark">Create User Account</h1>
                        <p class="text-gray-600">Create a new coordinator or supervisor account</p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <form action="{{ route('admin.users.store') }}" method="POST" class="p-6">
                    @csrf
                    
                    <!-- Name -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary @error('name') border-red-500 @enderror"
                               placeholder="Enter full name"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary @error('email') border-red-500 @enderror"
                               placeholder="Enter email address"
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password auto-generated notice -->
                    <div class="mb-6">
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <p class="text-sm text-gray-700">
                                A temporary password will be generated automatically. The user will receive
                                an email to verify their account and set a new password.
                            </p>
                        </div>
                    </div>

                    <!-- Role -->
                    <div class="mb-8">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <select id="role" 
                                name="role" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary @error('role') border-red-500 @enderror"
                                required>
                            <option value="">Select a role</option>
                            <option value="coordinator" {{ old('role') === 'coordinator' ? 'selected' : '' }}>Coordinator</option>
                            <option value="supervisor" {{ old('role') === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Coordinator fields -->
                    <div id="coordinator-fields" class="mb-8" style="display: none;">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                <select id="department_id" name="department_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary @error('department_id') border-red-500 @enderror">
                                    <option value="">Select a department</option>
                                    @foreach(($departments ?? []) as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="program_id" class="block text-sm font-medium text-gray-700 mb-2">Program/Course</label>
                                <select id="program_id" name="program_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary @error('program_id') border-red-500 @enderror">
                                    <option value="">Select a program</option>
                                </select>
                                @error('program_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="submit" class="flex-1 bg-ojt-primary text-white py-3 px-6 rounded-lg font-medium hover:bg-maroon-700 transition-colors duration-200">
                            Create User Account
                        </button>
                        <a href="{{ route('admin.users') }}" class="flex-1 bg-white border border-gray-300 text-gray-700 py-3 px-6 rounded-lg font-medium hover:bg-gray-50 transition-colors duration-200 text-center">
                            Cancel
                        </a>
                    </div>
                    <script>
                        (function() {
                            const roleEl = document.getElementById('role');
                            const coordFields = document.getElementById('coordinator-fields');
                            const deptEl = document.getElementById('department_id');
                            const progEl = document.getElementById('program_id');
                            const departments = @json(($departments ?? []));

                            function setPrograms() {
                                const deptId = deptEl.value;
                                const selected = departments.find(d => String(d.id) === String(deptId));
                                const programs = (selected && selected.programs) ? selected.programs : [];
                                progEl.innerHTML = '<option value="">Select a program</option>' + programs.map(p => {
                                    const sel = String({{ json_encode(old('program_id')) ?? 'null' }}) === String(p.id) ? ' selected' : '';
                                    return `<option value="${p.id}"${sel}>${p.name}</option>`;
                                }).join('');
                            }

                            function toggleRoleFields() {
                                const show = roleEl.value === 'coordinator';
                                coordFields.style.display = show ? '' : 'none';
                            }

                            roleEl.addEventListener('change', toggleRoleFields);
                            deptEl && deptEl.addEventListener('change', setPrograms);

                            // init
                            toggleRoleFields();
                            if (deptEl && deptEl.value) setPrograms();
                        })();
                    </script>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>