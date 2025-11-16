<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Expired - EVSU OJT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <img class="mx-auto h-16 w-auto" src="{{ asset('images/evsu-logo.png') }}" alt="EVSU Logo" onerror="this.style.display='none'">
            <h2 class="mt-6 text-center text-3xl font-bold text-gray-900">
                Link Expired
            </h2>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                
                <!-- Expired Notice -->
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-red-900 mb-1">Registration Link Expired</h3>
                            <p class="text-sm text-red-800">
                                This registration link has expired. The link was valid until 
                                <strong>{{ $request->expires_at->format('M d, Y g:i A') }}</strong>.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Student Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="text-sm font-medium text-blue-900 mb-2">Original Request From:</h3>
                    <p class="text-sm text-blue-800">
                        <strong>{{ $request->student->name }}</strong><br>
                        {{ $request->student->studentProfile->course ?? 'Student' }}<br>
                        Eastern Visayas State University<br>
                        Position: {{ $request->position }}
                    </p>
                </div>

                <!-- Resend Option -->
                <div class="text-center">
                    <p class="text-sm text-gray-600 mb-4">
                        If you still want to complete your registration and generate the acceptance letter, you can request a new link:
                    </p>
                    
                    <form method="POST" action="{{ route('supervisor.acceptance.resend', $request->id) }}">
                        @csrf
                        <button type="submit" 
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-ojt-primary hover:bg-maroon-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ojt-primary">
                            Send New Link to My Email
                        </button>
                    </form>
                    
                    <p class="mt-3 text-xs text-gray-500">
                        A new link will be sent to: <strong>{{ $request->supervisor_email }}</strong>
                    </p>
                    <p class="mt-1 text-xs text-gray-500">
                        The new link will be valid for 7 days.
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
