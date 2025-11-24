<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
            Final Evaluation Status
        </h2>
        <p class="text-sm text-gray-500">Your final OJT performance evaluation status</p>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                @if($evaluation)
                    <div class="text-center py-8">
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-ojt-dark mb-2">Final Evaluation Completed</h3>
                        <p class="text-gray-600 mb-6">Your supervisor has submitted your final OJT performance evaluation.</p>
                        
                        <div class="max-w-md mx-auto space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">Submitted Date</span>
                                <span class="text-sm text-gray-900">{{ $evaluation->submitted_at->format('F d, Y') }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">Status</span>
                                <x-final-evaluation-status-badge :evaluation="$evaluation" />
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">Evaluated By</span>
                                <span class="text-sm text-gray-900">{{ $evaluation->supervisor_name }}</span>
                            </div>
                        </div>
                        
                        @if($evaluation->status === 'reviewed')
                            <div class="mt-6 p-4 bg-green-50 border border-green-100 rounded-lg">
                                <p class="text-sm text-green-800">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Your final evaluation has been reviewed and approved by your coordinator.
                                </p>
                            </div>
                        @elseif($evaluation->status === 'submitted')
                            <div class="mt-6 p-4 bg-blue-50 border border-blue-100 rounded-lg">
                                <p class="text-sm text-blue-800">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Your evaluation is currently being reviewed by your coordinator.
                                </p>
                            </div>
                        @endif
                        
                        <div class="mt-8">
                            <a href="{{ route('dashboard') }}" 
                               class="inline-flex items-center px-6 py-3 bg-ojt-primary text-white rounded-lg hover:bg-maroon-700 transition-colors">
                                ← Back to Dashboard
                            </a>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No final evaluation yet</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Your supervisor will create your final evaluation when your OJT period is complete.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('dashboard') }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                ← Back to Dashboard
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
