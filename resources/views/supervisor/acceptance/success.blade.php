<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Letter Generated - EVSU OJT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-2xl">
            <img class="mx-auto h-16 w-auto" src="{{ asset('images/evsu-logo.png') }}" alt="EVSU Logo" onerror="this.style.display='none'">
            <h2 class="mt-6 text-center text-3xl font-bold text-gray-900">
                Acceptance Letter Generated!
            </h2>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-2xl">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                
                <!-- Success Message -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-green-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-green-900 mb-1">Letter Successfully Generated</h3>
                            <p class="text-sm text-green-800">
                                The acceptance letter has been created and sent to the student.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Letter Details -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Letter Details</h3>
                    <dl class="grid grid-cols-1 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-600">Document ID:</dt>
                            <dd class="font-medium text-gray-900">{{ $letter->document_id }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-600">Student:</dt>
                            <dd class="font-medium text-gray-900">{{ $acceptanceRequest->student->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-600">Position:</dt>
                            <dd class="font-medium text-gray-900">{{ $letter->job_title }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-600">Effective Date:</dt>
                            <dd class="font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($letter->start_date)->format('M d, Y') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-600">Total Hours:</dt>
                            <dd class="font-medium text-gray-900">{{ $letter->total_hours }} hours</dd>
                        </div>
                    </dl>
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    <a href="{{ route('acceptance-letters.download', $letter) }}" 
                       class="w-full flex justify-center items-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-ojt-primary hover:bg-maroon-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download PDF
                    </a>

                    <a href="{{ route('supervisor.acceptance.index') }}" 
                       class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        View All Acceptance Letters
                    </a>

                    <a href="{{ route('dashboard') }}" 
                       class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Go to Dashboard
                    </a>
                </div>

                <!-- What's Next -->
                <div class="mt-6 pt-6 border-t">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">What happens next?</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>✓ Student receives the acceptance letter automatically</li>
                        <li>✓ Letter is added to student's document submissions</li>
                        <li>✓ You are now linked as the student's supervisor</li>
                        <li>✓ You can track the student's progress in your dashboard</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
