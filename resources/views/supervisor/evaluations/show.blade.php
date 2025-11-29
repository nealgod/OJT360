<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                    Monthly Evaluation - {{ $evaluation->getMonthYearLabel() }}
                </h2>
                <p class="text-sm text-gray-500">{{ $evaluation->student->name }} - Month {{ $evaluation->month_number }}</p>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <a href="{{ route('supervisor.evaluations.pdf', ['evaluation' => $evaluation->id, 'view' => 1]) }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white rounded-lg hover:bg-maroon-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    View PDF
                </a>
                <a href="{{ route('supervisor.students.view', $evaluation->student) }}" 
                   class="inline-flex items-center text-ojt-primary hover:text-maroon-700 font-medium">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Student
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 rounded-md bg-green-50 border border-green-100 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Status Badge -->
            <div class="bg-white shadow sm:rounded-lg p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Evaluation Status</p>
                        <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold
                            @if($evaluation->status === 'reviewed') bg-green-100 text-green-800
                            @elseif($evaluation->status === 'submitted') bg-blue-100 text-blue-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst($evaluation->status) }}
                        </span>
                    </div>
                    @if($evaluation->submitted_at)
                        <p class="text-sm text-gray-600">Submitted on {{ $evaluation->submitted_at->format('M d, Y g:i A') }}</p>
                    @endif
                </div>
            </div>

            <!-- Information -->
            <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-ojt-dark mb-4">Information</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
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
                        <dd class="text-ojt-dark">{{ $evaluation->work_schedule }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Supervisor Name</dt>
                        <dd class="text-ojt-dark">{{ $evaluation->supervisor_name }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Report For</dt>
                        <dd class="text-ojt-dark">{{ $evaluation->getMonthYearLabel() }} - Month {{ $evaluation->month_number }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Ratings by Category -->
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
                    4 => 'Very Satisfactory',
                    3 => 'Satisfactory',
                    2 => 'Fair',
                    1 => 'Needs Improvement',
                ];
            @endphp

            @foreach($categories as $categoryName => $rows)
                <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-ojt-dark mb-4">{{ $categoryName }}</h3>
                    <div class="space-y-3">
                        @foreach($rows as $rowNum)
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-700">{{ $rowNum }}. {{ $attributeNames[$rowNum] }}</span>
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
                                    @if($evaluation->{"rating_row_$rowNum"} == 5) bg-green-100 text-green-800
                                    @elseif($evaluation->{"rating_row_$rowNum"} == 4) bg-blue-100 text-blue-800
                                    @elseif($evaluation->{"rating_row_$rowNum"} == 3) bg-yellow-100 text-yellow-800
                                    @elseif($evaluation->{"rating_row_$rowNum"} == 2) bg-orange-100 text-orange-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $evaluation->{"rating_row_$rowNum"} }} - {{ $ratingLabels[$evaluation->{"rating_row_$rowNum"}] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

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

            <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
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

            <!-- Comments -->
            @if($evaluation->comments_recommendations)
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-ojt-dark mb-4">Comments and Recommendations</h3>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap break-words">{{ $evaluation->comments_recommendations }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
