<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
            My Evaluations
        </h2>
        <p class="text-sm text-gray-500">View your evaluation status</p>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Final Evaluation Section -->
            @php
                $finalEvaluation = \App\Models\FinalEvaluation::where('student_user_id', Auth::id())->first();
            @endphp
            
            @if($finalEvaluation)
                <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-ojt-dark mb-4">Final Evaluation</h3>
                    <div class="border rounded-lg p-4 bg-green-50 border-green-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h4 class="font-semibold text-gray-900">Final OJT Performance Evaluation</h4>
                                    <x-final-evaluation-status-badge :evaluation="$finalEvaluation" />
                                </div>
                                <p class="text-sm text-gray-600">
                                    Submitted by {{ $finalEvaluation->supervisor_name }} on {{ $finalEvaluation->submitted_at->format('M d, Y') }}
                                </p>
                            </div>
                            <a href="{{ route('evaluations.final.status') }}"
                               class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white rounded-lg text-sm font-medium hover:bg-maroon-700 transition-colors">
                                View Status →
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Monthly Evaluations Section -->
            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-ojt-dark mb-4">Monthly Progress Evaluations</h3>
                @if($evaluations->count() > 0)
                    <div class="space-y-4">
                        @foreach($evaluations as $evaluation)
                            <div class="border rounded-lg p-4">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h3 class="text-lg font-semibold text-ojt-dark">
                                                {{ \Carbon\Carbon::create()->month($evaluation->evaluation_month)->format('F') }} {{ $evaluation->evaluation_year }}
                                            </h3>
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                                                @if($evaluation->status === 'reviewed') bg-green-100 text-green-800
                                                @elseif($evaluation->status === 'submitted') bg-blue-100 text-blue-800
                                                @else bg-yellow-100 text-yellow-800
                                                @endif">
                                                {{ ucfirst($evaluation->status) }}
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                            <span>Month {{ $evaluation->month_number }} of internship</span>
                                            <span>Evaluated by: {{ $evaluation->supervisor_name }}</span>
                                            @if($evaluation->submitted_at)
                                                <span>Submitted: {{ \Carbon\Carbon::parse($evaluation->submitted_at)->format('M d, Y') }}</span>
                                            @endif
                                            @if($evaluation->reviewed_at)
                                                <span>Reviewed: {{ \Carbon\Carbon::parse($evaluation->reviewed_at)->format('M d, Y') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        @if($evaluation->status === 'reviewed')
                                            <div class="flex items-center text-green-600">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="text-sm font-medium">Completed</span>
                                            </div>
                                        @elseif($evaluation->status === 'submitted')
                                            <div class="flex items-center text-blue-600">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="text-sm font-medium">Under Review</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($evaluation->status === 'reviewed')
                                    <div class="mt-3 p-3 bg-green-50 border border-green-100 rounded-lg">
                                        <p class="text-sm text-green-800">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Your supervisor has completed your monthly evaluation and it has been reviewed by your coordinator.
                                        </p>
                                    </div>
                                @elseif($evaluation->status === 'submitted')
                                    <div class="mt-3 p-3 bg-blue-50 border border-blue-100 rounded-lg">
                                        <p class="text-sm text-blue-800">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Your evaluation has been submitted and is currently being reviewed by your coordinator.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No evaluations yet</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Your supervisor will create monthly evaluations to track your progress.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
