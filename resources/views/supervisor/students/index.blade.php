<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                    My Supervised Students
                </h2>
                <p class="text-sm text-gray-500">Track and manage your supervised students</p>
            </div>
            <a href="{{ route('supervisor.students.search') }}"
               class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white text-sm font-medium rounded-lg hover:bg-maroon-700 transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Accept Another Student
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($students->count())
                <!-- Stats Overview -->
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg border border-gray-200 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Total Students</p>
                                <p class="text-2xl font-bold text-ojt-dark">{{ $students->total() }}</p>
                            </div>
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    @php
                        $studentIds = $students->pluck('id');
                        $totalReports = \App\Models\WeeklyReport::whereIn('student_user_id', $studentIds)->count();
                        $totalEvaluations = \App\Models\MonthlyEvaluation::whereIn('student_user_id', $studentIds)->count();
                        $pendingEvaluations = \App\Models\MonthlyEvaluation::whereIn('student_user_id', $studentIds)->whereNull('reviewed_at')->count();
                    @endphp
                    
                    <div class="bg-white rounded-lg border border-gray-200 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Weekly Reports</p>
                                <p class="text-2xl font-bold text-green-600">{{ $totalReports }}</p>
                            </div>
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg border border-gray-200 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Evaluations</p>
                                <p class="text-2xl font-bold text-purple-600">{{ $totalEvaluations }}</p>
                            </div>
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg border border-gray-200 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Pending Review</p>
                                <p class="text-2xl font-bold text-yellow-600">{{ $pendingEvaluations }}</p>
                            </div>
                            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Search -->
                <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
                    <div class="relative">
                        <input type="text" 
                               id="studentSearch"
                               placeholder="Search by name or student ID..."
                               class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary transition-colors">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Students List -->
                <div id="studentsList" class="space-y-4">
                    @foreach ($students as $student)
                        @php
                            $profile = $student->studentProfile;
                            $company = $profile?->company;
                            $latestLetter = $student->acceptanceLetters->first();
                            $completedMinutes = $student->attendanceLogs()->sum('minutes_worked');
                            $completedHours = round(($completedMinutes ?? 0) / 60, 1);
                            $requiredHours = $latestLetter?->total_hours ?? $profile?->required_hours ?? $student->getRequiredHours();
                            $percentage = $requiredHours > 0 ? round(($completedHours / $requiredHours) * 100, 1) : 0;
                            $weeklyReportsCount = $student->weeklyReports()->count();
                            $evaluationsCount = $student->monthlyEvaluations()->count();
                        @endphp
                        <div class="student-card bg-white border border-gray-200 rounded-lg p-5 shadow-sm hover:border-ojt-primary/50 transition-colors" 
                             data-name="{{ strtolower($student->name) }}" 
                             data-id="{{ strtolower($profile?->student_id ?? '') }}">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                <!-- Student Info -->
                                <div class="flex items-start gap-4 flex-1">
                                    <div class="flex-shrink-0">
                                        {!! $student->getAvatarHtml('w-14 h-14') !!}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-1">
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $student->name }}</h3>
                                            @if ($profile?->student_id)
                                                <span class="text-xs px-2 py-1 bg-gray-100 text-gray-700 rounded-full font-medium">
                                                    {{ $profile->student_id }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-600 space-x-2">
                                            @if ($profile?->course)
                                                <span>{{ $profile->course }}</span>
                                            @endif
                                            @if ($profile?->department)
                                                <span class="text-gray-400">•</span>
                                                <span>{{ $profile->department }}</span>
                                            @endif
                                        </div>
                                        @if ($company)
                                            <div class="text-sm text-gray-500 mt-1">
                                                <span class="font-medium text-gray-700">Company:</span>
                                                {{ $company->name }}
                                            </div>
                                        @endif
                                        
                                        <!-- Quick Stats -->
                                        <div class="flex items-center gap-4 mt-3">
                                            <div class="flex items-center text-sm">
                                                <svg class="w-4 h-4 text-green-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span class="text-gray-700"><strong>{{ $weeklyReportsCount }}</strong> reports</span>
                                            </div>
                                            <div class="flex items-center text-sm">
                                                <svg class="w-4 h-4 text-purple-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                                </svg>
                                                <span class="text-gray-700"><strong>{{ $evaluationsCount }}</strong> evaluations</span>
                                            </div>
                                            <div class="flex items-center text-sm">
                                                <svg class="w-4 h-4 text-blue-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="text-gray-700"><strong>{{ number_format($completedHours, 1) }}</strong> hours</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Progress & Actions -->
                                <div class="lg:w-80">
                                    @if($requiredHours > 0)
                                        <div class="mb-3">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-sm text-gray-600">Progress</span>
                                                <span class="text-sm font-bold text-ojt-primary">{{ $percentage }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                                <div class="bg-ojt-primary h-2 rounded-full transition-all duration-300" 
                                                     style="width: {{ min($percentage, 100) }}%"></div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ number_format($completedHours, 1) }} / {{ number_format($requiredHours) }} hours
                                            </p>
                                        </div>
                                    @endif
                                    
                                    <a href="{{ route('supervisor.students.view', $student->id) }}"
                                       class="block w-full text-center px-4 py-2 bg-ojt-primary text-white text-sm font-medium rounded-lg hover:bg-maroon-700 transition-colors">
                                        View Full Details →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $students->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg border border-dashed border-gray-300 p-12 text-center">
                    <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No supervised students yet</h3>
                    <p class="text-gray-500 mb-6">
                        Once you accept a student, they'll appear here with their progress and reports
                    </p>
                    <a href="{{ route('supervisor.students.search') }}"
                       class="inline-flex items-center px-6 py-3 bg-ojt-primary text-white font-medium rounded-lg hover:bg-maroon-700 transition-colors shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Search for Students
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Live search functionality
        document.getElementById('studentSearch')?.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.student-card');
            
            cards.forEach(card => {
                const name = card.dataset.name || '';
                const id = card.dataset.id || '';
                
                if (name.includes(searchTerm) || id.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</x-app-layout>
