<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.reports.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                    Evaluations
                </h2>
                <p class="text-sm text-gray-500">Monthly and final evaluations</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Filters -->
            <div class="bg-white rounded-lg border p-4 mb-6 shadow-sm">
                <h3 class="font-semibold text-gray-700 mb-4">Filters</h3>
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                        <select name="department_id" id="department_filter_eval" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-ojt-primary">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Program</label>
                        <select name="program_id" id="program_filter_eval" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-ojt-primary">
                            <option value="">All Programs</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" 
                                        data-department="{{ $program->department_id }}"
                                        {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                    {{ $program->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Student</label>
                        <select name="student_id" id="student_filter_eval" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-ojt-primary">
                            <option value="">All Students</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" 
                                        data-department="{{ $student->studentProfile->department_id ?? '' }}"
                                        data-program="{{ $student->studentProfile->program_id ?? '' }}"
                                        {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="bg-ojt-primary text-white px-6 py-2 rounded-lg hover:bg-maroon-700 transition-colors">
                            Apply
                        </button>
                        @if(request()->hasAny(['department_id', 'program_id', 'student_id']))
                            <a href="{{ route('admin.reports.evaluations') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const departmentSelect = document.getElementById('department_filter_eval');
                    const programSelect = document.getElementById('program_filter_eval');
                    const studentSelect = document.getElementById('student_filter_eval');
                    const allProgramOptions = Array.from(programSelect.options);
                    const allStudentOptions = Array.from(studentSelect.options);

                    function filterPrograms() {
                        const selectedDept = departmentSelect.value;
                        
                        programSelect.innerHTML = '';
                        programSelect.add(allProgramOptions[0].cloneNode(true));

                        allProgramOptions.slice(1).forEach(option => {
                            if (!selectedDept || option.dataset.department === selectedDept) {
                                programSelect.add(option.cloneNode(true));
                            }
                        });

                        const currentValue = programSelect.querySelector(`option[value="${programSelect.value}"]`);
                        if (!currentValue) {
                            programSelect.value = '';
                        }
                        
                        filterStudents();
                    }

                    function filterStudents() {
                        const selectedDept = departmentSelect.value;
                        const selectedProg = programSelect.value;
                        
                        studentSelect.innerHTML = '';
                        studentSelect.add(allStudentOptions[0].cloneNode(true));

                        allStudentOptions.slice(1).forEach(option => {
                            const matchDept = !selectedDept || option.dataset.department === selectedDept;
                            const matchProg = !selectedProg || option.dataset.program === selectedProg;
                            
                            if (matchDept && matchProg) {
                                studentSelect.add(option.cloneNode(true));
                            }
                        });

                        const currentValue = studentSelect.querySelector(`option[value="${studentSelect.value}"]`);
                        if (!currentValue) {
                            studentSelect.value = '';
                        }
                    }

                    departmentSelect.addEventListener('change', filterPrograms);
                    programSelect.addEventListener('change', filterStudents);
                    filterPrograms();
                });
            </script>

            <!-- Monthly Evaluations -->
            <div class="bg-white rounded-lg border shadow-sm p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4">Monthly Evaluations</h2>
                <div class="overflow-x-auto max-h-[560px] overflow-y-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Month</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Intern</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supervisor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($monthlyEvals as $eval)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm">{{ \Carbon\Carbon::create($eval->evaluation_year, $eval->evaluation_month, 1)->format('F Y') }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if($eval->student->studentProfile?->profile_image)
                                                <img src="{{ Storage::url($eval->student->studentProfile->profile_image) }}" alt="{{ $eval->student->name }}" class="w-8 h-8 rounded-full object-cover mr-2">
                                            @else
                                                <div class="w-8 h-8 {{ $eval->student->getAvatarColor() }} rounded-full flex items-center justify-center text-white text-xs font-bold mr-2">
                                                    {{ substr($eval->student->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <span class="text-sm font-medium">{{ $eval->student->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm">{{ $eval->supervisor->name }}</td>
                                    <td class="px-6 py-4">
                                        @if($eval->reviewed_at)
                                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">✓ Reviewed</span>
                                        @else
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">⏳ Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">No monthly evaluations</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($monthlyEvals->hasPages())
                    <div class="mt-4">
                        {{ $monthlyEvals->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>

            <!-- Final Evaluations -->
            <div class="bg-white rounded-lg border shadow-sm p-6">
                <h2 class="text-xl font-semibold mb-4">Final Evaluations</h2>
                <div class="overflow-x-auto max-h-[560px] overflow-y-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Intern</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supervisor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($finalEvals as $eval)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if($eval->student->studentProfile?->profile_image)
                                                <img src="{{ Storage::url($eval->student->studentProfile->profile_image) }}" alt="{{ $eval->student->name }}" class="w-8 h-8 rounded-full object-cover mr-2">
                                            @else
                                                <div class="w-8 h-8 {{ $eval->student->getAvatarColor() }} rounded-full flex items-center justify-center text-white text-xs font-bold mr-2">
                                                    {{ substr($eval->student->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <span class="text-sm font-medium">{{ $eval->student->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm">{{ $eval->supervisor->name }}</td>
                                    <td class="px-6 py-4">
                                        @if($eval->reviewed_at)
                                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">✓ Reviewed</span>
                                        @else
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">⏳ Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $eval->created_at->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">No final evaluations</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($finalEvals->hasPages())
                    <div class="mt-4">
                        {{ $finalEvals->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
