<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Registration - EVSU OJT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <img class="mx-auto h-16 w-auto" src="{{ asset('images/evsu-logo.png') }}" alt="EVSU Logo" onerror="this.style.display='none'">
            <h2 class="mt-6 text-center text-3xl font-bold text-gray-900">
                Supervisor Account Setup
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Create your account to generate the acceptance letter
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                
                <!-- Student Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="text-sm font-medium text-blue-900 mb-2">Acceptance Letter Request From:</h3>
                    <p class="text-sm text-blue-800">
                        <strong>{{ $request->student->name }}</strong><br>
                        {{ $request->student->studentProfile->course ?? 'Student' }}<br>
                        Eastern Visayas State University
                    </p>
                </div>

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                        <ul class="text-sm text-red-800 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('supervisor.acceptance.register', $token) }}" class="space-y-6">
                    @csrf

                    <!-- Your Name (Editable) -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Your Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $request->supervisor_name) }}" required
                               placeholder="Lastname, Firstname Middlename"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-ojt-primary focus:border-ojt-primary">
                        <p class="mt-1 text-xs text-gray-500">You can edit this if needed</p>
                    </div>

                    <!-- Email (Read-only) -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ $request->supervisor_email }}" readonly
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-100 text-gray-600 cursor-not-allowed">
                        <p class="mt-1 text-xs text-gray-500">Email cannot be changed (verified via link)</p>
                    </div>

                    <!-- Company Name (Editable) -->
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700">
                            Company Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="company_name" name="company_name" value="{{ old('company_name', $request->company_name) }}" required
                               placeholder="Company name"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-ojt-primary focus:border-ojt-primary">
                        <p class="mt-1 text-xs text-gray-500">You can edit this if needed</p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 pr-10 focus:ring-ojt-primary focus:border-ojt-primary">
                            <button type="button" onclick="togglePassword('password')" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-600 hover:text-gray-800">
                                <svg id="password-eye" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Minimum 8 characters</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 pr-10 focus:ring-ojt-primary focus:border-ojt-primary">
                            <button type="button" onclick="togglePassword('password_confirmation')" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-600 hover:text-gray-800">
                                <svg id="password_confirmation-eye" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Position -->
                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700">
                            Your Position/Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="position" name="position" required
                               placeholder="e.g., HR Manager, Department Head"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-ojt-primary focus:border-ojt-primary">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="phone" name="phone" required
                               placeholder="e.g., (555) 123-4567"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-ojt-primary focus:border-ojt-primary">
                    </div>

                    <!-- Company Address -->
                    <div>
                        <label for="company_address" class="block text-sm font-medium text-gray-700">
                            Company Address <span class="text-red-500">*</span>
                        </label>
                        <textarea id="company_address" name="company_address" rows="2" required
                                  placeholder="Full company address"
                                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-ojt-primary focus:border-ojt-primary"></textarea>
                    </div>

                    <!-- Company Phone -->
                    <div>
                        <label for="company_phone" class="block text-sm font-medium text-gray-700">
                            Company Phone (Optional)
                        </label>
                        <input type="text" id="company_phone" name="company_phone"
                               placeholder="Company contact number"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-ojt-primary focus:border-ojt-primary">
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-ojt-primary hover:bg-maroon-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ojt-primary">
                            Create Account & Continue
                        </button>
                    </div>
                </form>

                <div class="mt-6">
                    <p class="text-xs text-center text-gray-500">
                        By creating an account, you agree to generate an official OJT acceptance letter for the student.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
            field.setAttribute('type', type);
        }
    </script>
</body>
</html>
