<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Acceptance Letter Generated') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Success Message -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-8 mb-6">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Acceptance Letter Generated Successfully!</h3>
                    <p class="text-gray-600">The acceptance letter has been generated and sent to the student.</p>
                </div>

                <!-- Letter Details -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h4 class="font-semibold text-gray-900 mb-4">Letter Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Document ID:</span>
                            <span class="font-medium text-gray-900 ml-2">{{ $letter->document_id }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Student:</span>
                            <span class="font-medium text-gray-900 ml-2">{{ $letter->student->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Position:</span>
                            <span class="font-medium text-gray-900 ml-2">{{ $letter->job_title }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Department:</span>
                            <span class="font-medium text-gray-900 ml-2">{{ $letter->department }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Start Date:</span>
                            <span class="font-medium text-gray-900 ml-2">{{ $letter->start_date->format('M d, Y') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Total Hours:</span>
                            <span class="font-medium text-gray-900 ml-2">{{ $letter->total_hours }} hours</span>
                        </div>
                    </div>
                </div>

                <!-- Notifications Sent -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-1">Notifications Sent:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Student has been notified via email and in-app notification</li>
                                <li>Coordinator has been notified for review</li>
                                <li>Letter has been added to student's documents</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('acceptance-letters.download', $letter) }}" 
                       class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-ojt-primary text-white rounded-lg hover:bg-maroon-700 transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Letter
                    </a>
                    <a href="{{ route('supervisor.students.search') }}" 
                       class="flex-1 inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Accept Another Student
                    </a>
                    <a href="{{ route('supervisor.acceptance.index') }}" 
                       class="flex-1 inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        View All Letters
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
