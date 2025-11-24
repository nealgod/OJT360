<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                    Final Evaluation Details
                </h2>
                <p class="text-sm text-gray-500">{{ $evaluation->student_name }} - Control No: {{ $evaluation->control_number }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('supervisor.final-evaluations.pdf', $evaluation) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download PDF
                </a>
                <a href="{{ route('supervisor.final-evaluations.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    ← Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Student Information -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-ojt-dark mb-4">Student Information</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <dt class="font-medium text-gray-500">Student Name</dt>
                                <dd class="text-ojt-dark">{{ $evaluation->student_name }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Student ID</dt>
                                <dd class="text-ojt-dark">{{ $evaluation->student_id }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Course</dt>
                                <dd class="text-ojt-dark">{{ $evaluation->course }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Department</dt>
                                <dd class="text-ojt-dark">{{ $evaluation->department }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Company</dt>
                                <dd class="text-ojt-dark">{{ $evaluation->hte_name }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Total Hours</dt>
                                <dd class="text-ojt-dark">{{ number_format($evaluation->total_hours_rendered, 2) }} hours</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Performance Ratings -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-ojt-dark mb-4">Performance Ratings</h3>
                        
                        @php
                            $criteria = [
                                ['label' => 'Quality of work', 'description' => 'Thoroughness, Accuracy, Neat, & Effectiveness', 'value' => $evaluation->rating_quality_thoroughness, 'max' => 20],
                                ['label' => 'Dependability, Reliability, and Resourcefulness', 'description' => 'Ability to work with maximum amount of supervision', 'value' => $evaluation->rating_dependability, 'max' => 15],
                                ['label' => 'Quality of work', 'description' => 'Able to complete work in allotted time', 'value' => $evaluation->rating_quality_completion, 'max' => 20],
                                ['label' => 'Attendance', 'description' => 'Regularity and punctuality', 'value' => $evaluation->rating_attendance, 'max' => 15],
                                ['label' => 'Cooperation', 'description' => 'Works well with everyone', 'value' => $evaluation->rating_cooperation, 'max' => 10],
                                ['label' => 'Judgement', 'description' => 'Sound decisions', 'value' => $evaluation->rating_judgement, 'max' => 10],
                                ['label' => 'Personality', 'description' => 'Personal grooming', 'value' => $evaluation->rating_personality, 'max' => 5],
                            ];
                        @endphp
                        
                        <div class="space-y-3">
                            @foreach($criteria as $index => $criterion)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <span class="text-sm font-medium text-gray-900">{{ $index + 1 }}. {{ $criterion['label'] }}</span>
                                        <p class="text-xs text-gray-600">{{ $criterion['description'] }}</p>
                                    </div>
                                    <div class="flex items-center gap-3 ml-4">
                                        <div class="w-32 bg-gray-200 rounded-full h-2">
                                            <div class="bg-ojt-primary h-2 rounded-full" style="width: {{ ($criterion['value'] / $criterion['max']) * 100 }}%"></div>
                                        </div>
                                        <span class="text-lg font-bold text-ojt-primary w-16 text-right">{{ number_format($criterion['value'], 2) }}%</span>
                                        <span class="text-xs text-gray-500">/ {{ $criterion['max'] }}%</span>
                                    </div>
                                </div>
                            @endforeach
                            
                            <!-- Total Rating -->
                            <div class="mt-4 p-4 bg-ojt-primary/10 border border-ojt-primary/20 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <span class="text-base font-semibold text-ojt-dark">Total Rating</span>
                                    <span class="text-2xl font-bold text-ojt-primary">{{ number_format($evaluation->total_rating, 2) }}%</span>
                                </div>
                                <p class="text-xs text-gray-600 mt-1">Maximum: 95%</p>
                            </div>
                        </div>
                    </div>

                    <!-- Comments -->
                    @if($evaluation->comments_recommendations)
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-ojt-dark mb-4">Comments and Recommendations</h3>
                            <div class="prose max-w-none">
                                <p class="text-gray-700 whitespace-pre-wrap">{{ $evaluation->comments_recommendations }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Status -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-ojt-dark mb-4">Status</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-600">Current Status</p>
                                <x-final-evaluation-status-badge :evaluation="$evaluation" class="mt-1" />
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Control Number</p>
                                <p class="font-medium text-gray-900">{{ $evaluation->control_number }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Revision</p>
                                <p class="font-medium text-gray-900">{{ $evaluation->revision_number }}</p>
                            </div>
                        </div>
                    </div>

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
                                    </div>
                                </div>
                            @endif
                            
                            @if($evaluation->reviewed_at)
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">Reviewed</p>
                                        <p class="text-xs text-gray-500">{{ $evaluation->reviewed_at->format('M d, Y g:i A') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
