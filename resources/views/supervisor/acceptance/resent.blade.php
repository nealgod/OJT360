<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Link Sent - EVSU OJT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <img class="mx-auto h-16 w-auto" src="{{ asset('images/evsu-logo.png') }}" alt="EVSU Logo" onerror="this.style.display='none'">
            <h2 class="mt-6 text-center text-3xl font-bold text-gray-900">
                New Link Sent
            </h2>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                
                <!-- Success Notice -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-green-900 mb-1">{{ $message }}</h3>
                            <p class="text-sm text-green-800">
                                Please check your email at <strong>{{ $request->supervisor_email }}</strong> for the new registration link.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Student Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="text-sm font-medium text-blue-900 mb-2">Request From:</h3>
                    <p class="text-sm text-blue-800">
                        <strong>{{ $request->student->name }}</strong><br>
                        {{ $request->student->studentProfile->course ?? 'Student' }}<br>
                        Eastern Visayas State University<br>
                        Position: {{ $request->position }}
                    </p>
                </div>

                <!-- Instructions -->
                <div class="text-center">
                    <p class="text-sm text-gray-600 mb-4">
                        The new link will be valid for <strong>7 days</strong>.
                    </p>
                    <p class="text-xs text-gray-500">
                        If you don't receive the email within a few minutes, please check your spam folder.
                    </p>
                </div>

                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500">
                        If you have questions, please contact the student directly at <strong>{{ $request->student->email }}</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
