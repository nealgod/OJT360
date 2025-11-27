<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                    Monthly Evaluation Details
                </h2>
                <p class="text-sm text-gray-500">{{ $evaluation->student->name }} - {{ $evaluation->getMonthYearLabel() }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('coordinator.evaluations.download-pdf', $evaluation) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download PDF
                </a>
                <a href="{{ route('coordinator.evaluations.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    ← Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-md bg-green-50 border border-green-100 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Information -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-ojt-dark mb-4">Information</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                            <div>
                                <dt class="font-medium text-gray-500">Student Name</dt>
                                <dd class="text-ojt-dark">{{ $evaluation->student_name }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Student ID</dt>
                                <dd class="text-ojt-dark">{{ $evaluation->student->studentProfile->student_id ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Course/Program</dt>
                                <dd class="text-ojt-dark">{{ $evaluation->student->studentProfile->course ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Department</dt>
                                <dd class="text-ojt-dark">{{ $evaluation->student->studentProfile->department ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Host Training Establishment</dt>
                                <dd class="text-ojt-dark">{{ $evaluation->hte_name }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Company Address</dt>
                                <dd class="text-ojt-dark">{{ $evaluation->hte_address }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Work Schedule</dt>
                                <dd class="text-ojt-dark">{{ $evaluation->formatted_work_schedule }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Supervisor Name</dt>
                                <dd class="text-ojt-dark">{{ $evaluation->supervisor_name }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Report For</dt>
                                <dd class="text-ojt-dark">{{ $evaluation->getMonthYearLabel() }} - Month {{ $evaluation->month_number }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Status</dt>
                                <dd class="text-ojt-dark"><x-evaluation-status-badge :evaluation="$evaluation" /></dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Category Averages (Simple Style) -->
                    @php
                        $skillsAvg = $evaluation->getCategoryAverage('skills');
                        $qualityAvg = $evaluation->getCategoryAverage('quality');
                        $approachAvg = $evaluation->getCategoryAverage('approach');
                        $cooperationAvg = $evaluation->getCategoryAverage('cooperation');
                        $totalAvg = $evaluation->getTotalAverage();
                        
                        $skillsPct = round(($skillsAvg / 5) * 100);
                        $qualityPct = round(($qualityAvg / 5) * 100);
                        $approachPct = round(($approachAvg / 5) * 100);
                        $cooperationPct = round(($cooperationAvg / 5) * 100);
                        $totalPct = round(($totalAvg / 5) * 100);
                    @endphp

                    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                        <h3 class="text-lg font-semibold text-ojt-dark mb-4">Evaluation Summary</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">Related Skills (Items 1-5):</span>
                                <div class="text-right">
                                    <span class="text-lg font-bold text-ojt-primary">{{ number_format($skillsAvg, 1) }} / 5.0</span>
                                    <span class="text-xs text-gray-500 ml-2">({{ $skillsPct }}%)</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">Quality of Work (Items 6-10):</span>
                                <div class="text-right">
                                    <span class="text-lg font-bold text-ojt-primary">{{ number_format($qualityAvg, 1) }} / 5.0</span>
                                    <span class="text-xs text-gray-500 ml-2">({{ $qualityPct }}%)</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">Work Approach (Items 11-15):</span>
                                <div class="text-right">
                                    <span class="text-lg font-bold text-ojt-primary">{{ number_format($approachAvg, 1) }} / 5.0</span>
                                    <span class="text-xs text-gray-500 ml-2">({{ $approachPct }}%)</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">Job Interest (Items 16-20):</span>
                                <div class="text-right">
                                    <span class="text-lg font-bold text-ojt-primary">{{ number_format($cooperationAvg, 1) }} / 5.0</span>
                                    <span class="text-xs text-gray-500 ml-2">({{ $cooperationPct }}%)</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-ojt-primary/5 border-2 border-ojt-primary rounded-lg mt-4">
                                <span class="text-base font-semibold text-gray-900">Overall Average:</span>
                                <div class="text-right">
                                    <span class="text-2xl font-bold text-ojt-primary">{{ number_format($totalAvg, 1) }} / 5.0</span>
                                    <span class="text-sm text-gray-600 ml-2">({{ $totalPct }}%)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Ratings (Detailed View) -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-ojt-dark mb-4">Detailed Ratings</h3>
                        <p class="text-sm text-gray-600 mb-4">Rating Scale: 1 (Needs Improvement) to 5 (Excellent)</p>
                        
                        <div class="space-y-6">
                            @php
                                $categories = [
                                    'RELATED SKILLS AND COMPETENCIES' => [1, 2, 3, 4, 5],
                                    'QUALITY OF WORK' => [6, 7, 8, 9, 10],
                                    'WORK APPROACH' => [11, 12, 13, 14, 15],
                                    'JOB INTEREST AND COOPERATION' => [16, 17, 18, 19, 20],
                                ];
                                $attributeNames = \App\Models\MonthlyEvaluation::getAttributeNames();
                                $ratingLabels = [
                                    5 => 'Excellent',
                                    4 => 'Very Good',
                                    3 => 'Satisfactory',
                                    2 => 'Fair',
                                    1 => 'Needs Improvement',
                                ];
                            @endphp
                            
                            @foreach($categories as $categoryName => $rows)
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3 px-3 py-2 bg-gray-50 rounded-lg">
                                        {{ $categoryName }}
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($rows as $rowNum)
                                            @php
                                                $rating = $evaluation->{"rating_row_$rowNum"};
                                            @endphp
                                            <div class="flex items-center justify-between pl-6 pr-3 py-2 hover:bg-gray-50 rounded-lg transition-colors">
                                                <span class="text-sm text-gray-700">{{ $rowNum }}. {{ $attributeNames[$rowNum] }}</span>
                                                <div class="flex items-center gap-2">
                                                    <div class="flex space-x-0.5">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <svg class="w-4 h-4 {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                            </svg>
                                                        @endfor
                                                    </div>
                                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold
                                                        @if($rating == 5) bg-green-100 text-green-800
                                                        @elseif($rating == 4) bg-blue-100 text-blue-800
                                                        @elseif($rating == 3) bg-yellow-100 text-yellow-800
                                                        @elseif($rating == 2) bg-orange-100 text-orange-800
                                                        @else bg-red-100 text-red-800
                                                        @endif">
                                                        {{ $rating }} - {{ $ratingLabels[$rating] }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Comments -->
                    @if($evaluation->supervisor_comments)
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-ojt-dark mb-4">Supervisor Comments</h3>
                            <div class="prose max-w-none">
                                <p class="text-gray-700 whitespace-pre-wrap">{{ $evaluation->supervisor_comments }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Timeline -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-ojt-dark mb-4">Timeline</h3>
                        <div class="space-y-4">
                            @if($evaluation->submitted_at)
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">Submitted</p>
                                        <p class="text-xs text-gray-500">{{ $evaluation->submitted_at->format('M d, Y g:i A') }}</p>
                                        <p class="text-xs text-gray-400">{{ $evaluation->submitted_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endif
                            
                            @if($evaluation->coordinator_reviewed_at)
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">Reviewed</p>
                                        <p class="text-xs text-gray-500">{{ $evaluation->coordinator_reviewed_at->format('M d, Y g:i A') }}</p>
                                        <p class="text-xs text-gray-400">{{ $evaluation->coordinator_reviewed_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">Pending Review</p>
                                        <p class="text-xs text-gray-500">Awaiting coordinator review</p>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Supervisor</p>
                                    <p class="text-xs text-gray-500">{{ $evaluation->supervisor->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $evaluation->supervisor->email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    @if(!$evaluation->reviewed_at)
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-ojt-dark mb-4">Actions</h3>
                            <form action="{{ route('coordinator.evaluations.mark-reviewed', $evaluation) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                                    <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Mark as Reviewed
                                </button>
                            </form>
                        </div>
                    @endif

                    <!-- Quick Stats -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-ojt-dark mb-4">Quick Stats</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Total Criteria</span>
                                <span class="font-semibold text-gray-900">20</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Average Score</span>
                                <div class="text-right">
                                    <span class="font-semibold text-ojt-primary">{{ number_format($totalAvg, 1) }} / 5.0</span>
                                    <span class="text-xs text-gray-500 ml-1">({{ $totalPct }}%)</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Performance</span>
                                @php
                                    $performance = $totalAvg >= 4.5 ? 'Excellent' : ($totalAvg >= 3.5 ? 'Very Good' : ($totalAvg >= 2.5 ? 'Satisfactory' : 'Needs Improvement'));
                                    $perfColor = $totalAvg >= 4.5 ? 'text-green-600' : ($totalAvg >= 3.5 ? 'text-blue-600' : ($totalAvg >= 2.5 ? 'text-yellow-600' : 'text-red-600'));
                                @endphp
                                <span class="font-semibold {{ $perfColor }}">{{ $performance }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
