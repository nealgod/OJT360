<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.reports.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex-1 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                        Weekly Reports
                    </h2>
                    <p class="text-sm text-gray-500">Monitor all intern weekly report submissions</p>
                </div>
                <div class="text-left sm:text-right">
                    <p class="text-xs sm:text-sm text-gray-600">Total Reports</p>
                    <p class="text-xl sm:text-2xl font-bold text-ojt-primary">{{ $reports->total() }}</p>
                </div>
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
                        <select name="department_id" id="department_filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-ojt-primary">
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
                        <select name="program_id" id="program_filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-ojt-primary">
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
                        <select name="student_id" id="student_filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-ojt-primary">
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
                            <a href="{{ route('admin.reports.weekly') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const departmentSelect = document.getElementById('department_filter');
                    const programSelect = document.getElementById('program_filter');
                    const studentSelect = document.getElementById('student_filter');
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



            <div class="bg-white rounded-lg border shadow-sm overflow-hidden">
                <div class="overflow-x-auto max-h-[560px] overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Week Period</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Intern</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coordinator</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($reports as $report)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">Week {{ $report->week_number }}</div>
                                        <div class="text-xs text-gray-500">{{ $report->week_start_date->format('M d') }} - {{ $report->week_end_date->format('M d, Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($report->student->studentProfile?->profile_image)
                                                <img src="{{ Storage::url($report->student->studentProfile->profile_image) }}" alt="{{ $report->student->name }}" class="w-10 h-10 rounded-full object-cover mr-3">
                                            @else
                                                <div class="w-10 h-10 {{ $report->student->getAvatarColor() }} rounded-full flex items-center justify-center text-white font-bold mr-3">
                                                    {{ substr($report->student->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $report->student->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $report->student->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $report->coordinator?->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-blue-600">{{ number_format($report->total_hours, 1) }}h</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $report->submitted_at ? $report->submitted_at->format('M d, Y') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">No weekly reports found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $reports->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
