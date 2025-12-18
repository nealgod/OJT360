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
                        Attendance Logs
                    </h2>
                    <p class="text-sm text-gray-500">View and filter All Intern attendance records</p>
                </div>
                <div class="text-left sm:text-right">
                    <p class="text-xs sm:text-sm text-gray-600">Total Records</p>
                    <p class="text-xl sm:text-2xl font-bold text-ojt-primary">{{ $logs->total() }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Filters -->
            <div class="bg-white rounded-lg border p-6 mb-6 shadow-sm">
                <h3 class="font-semibold text-gray-700 mb-4">Filters</h3>
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                        <select name="department_id" id="department_filter_attendance" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary">
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
                        <select name="program_id" id="program_filter_attendance" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary">
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search Intern</label>
                        <select name="user_id" id="student_filter_attendance" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary">
                            <option value="">All Interns</option>
                            @foreach($interns as $intern)
                                <option value="{{ $intern->id }}" 
                                        data-department="{{ $intern->studentProfile->department_id ?? '' }}"
                                        data-program="{{ $intern->studentProfile->program_id ?? '' }}"
                                        {{ request('user_id') == $intern->id ? 'selected' : '' }}>
                                    {{ $intern->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="bg-ojt-primary text-white px-6 py-2 rounded-lg hover:bg-maroon-700 transition-colors">
                            Apply Filters
                        </button>
                        @if(request()->hasAny(['user_id', 'date_from', 'date_to', 'department_id', 'program_id']))
                            <a href="{{ route('admin.reports.attendance') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const departmentSelect = document.getElementById('department_filter_attendance');
                    const programSelect = document.getElementById('program_filter_attendance');
                    const studentSelect = document.getElementById('student_filter_attendance');
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Intern</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time In</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time Out</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $log->work_date->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $log->work_date->format('l') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($log->user->studentProfile?->profile_image)
                                                <img src="{{ Storage::url($log->user->studentProfile->profile_image) }}" alt="{{ $log->user->name }}" class="w-10 h-10 rounded-full object-cover mr-3">
                                            @else
                                                <div class="w-10 h-10 {{ $log->user->getAvatarColor() }} rounded-full flex items-center justify-center text-white font-bold mr-3">
                                                    {{ substr($log->user->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $log->user->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $log->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">{{ $log->time_in_formatted }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">{{ $log->time_out_formatted ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-ojt-primary">{{ number_format(($log->minutes_worked ?? 0) / 60, 1) }}h</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-1">
                                            @foreach(['am_in', 'am_out', 'pm_in', 'pm_out'] as $type)
                                                @php
                                                    $photoCol = $type.'_photo';
                                                    $latCol = $type.'_lat';
                                                    $lngCol = $type.'_lng';
                                                    
                                                    $hasPhoto = $log->$photoCol;
                                                    $lat = $log->$latCol;
                                                    $lng = $log->$lngCol;
                                                @endphp
                                                @if($hasPhoto)
                                                    <button 
                                                        onclick="showPhotoMap('{{ Storage::url($hasPhoto) }}', '{{ $lat }}', '{{ $lng }}', '{{ strtoupper(str_replace('_', ' ', $type)) }}')"
                                                        class="w-6 h-6 rounded flex items-center justify-center transition-all bg-blue-100 text-blue-600 hover:bg-blue-200 focus:outline-none"
                                                        title="{{ strtoupper(str_replace('_', ' ', $type)) }}"
                                                    >
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                    </button>
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($log->is_recovered && $log->recovery_approved === true)
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                                Recovered
                                            </span>
                                        @elseif($log->is_recovered && $log->recovery_approved === false)
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                                Recovery Rejected
                                            </span>
                                        @elseif($log->is_recovered && is_null($log->recovery_approved))
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 animate-pulse">
                                                Pending Recovery
                                            </span>
                                        @elseif($log->status === 'approved' || $log->time_out_formatted)
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                Completed
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                                Incomplete
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">No attendance logs found</p>
                                        <p class="text-xs text-gray-400 mt-1">Try adjusting your filters</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $logs->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
    <!-- Photo & Map Modal -->
    <div id="photoMapModal" class="fixed inset-0 z-50 hidden overflow-y-auto" onclick="closePhotoMap()">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full" onclick="event.stopPropagation()">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="photoMapTitle">Attendance Logic</h3>
                        <button type="button" onclick="closePhotoMap()" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Photo Column -->
                        <div class="bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center h-full min-h-[300px]">
                            <img id="modalPhoto" src="" alt="Attendance Photo" class="max-w-full max-h-[500px] object-contain">
                        </div>
                        <!-- Map Column -->
                        <div class="bg-gray-100 rounded-lg overflow-hidden h-[300px] md:h-auto relative">
                            <div id="noMapMessage" class="hidden absolute inset-0 flex items-center justify-center text-gray-500 text-sm">
                                No location data available
                            </div>
                            <iframe id="googleMap" class="w-full h-full" frameborder="0" style="border:0" allowfullscreen loading="lazy" src=""></iframe>
                        </div>
                    </div>
                    <!-- External Link -->
                    <div class="mt-4 text-right">
                        <a id="externalMapLink" href="#" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 font-medium inline-flex items-center">
                            Open in Google Maps
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showPhotoMap(photoUrl, lat, lng, title) {
            document.getElementById('photoMapTitle').textContent = title;
            document.getElementById('modalPhoto').src = photoUrl;
            
            const mapFrame = document.getElementById('googleMap');
            const mapLink = document.getElementById('externalMapLink');
            const noMap = document.getElementById('noMapMessage');
            
            if (lat && lng && lat != 'null' && lng != 'null') {
                const mapUrl = `https://maps.google.com/maps?q=${lat},${lng}&t=&z=15&ie=UTF8&iwloc=&output=embed`;
                mapFrame.src = mapUrl;
                mapFrame.classList.remove('hidden');
                noMap.classList.add('hidden');
                
                mapLink.href = `https://www.google.com/maps?q=${lat},${lng}`;
                mapLink.style.display = 'inline-flex';
            } else {
                mapFrame.classList.add('hidden');
                noMap.classList.remove('hidden');
                mapLink.style.display = 'none';
            }

            document.getElementById('photoMapModal').classList.remove('hidden');
        }

        function closePhotoMap() {
            document.getElementById('photoMapModal').classList.add('hidden');
            document.getElementById('googleMap').src = ''; // Clear source to stop loading
        }
    </script>
</x-app-layout>
