<x-guest-layout>
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        <h2 class="text-xl font-semibold text-gray-900 mb-2">Check Your Email</h2>
        <p class="text-sm text-gray-600">We've sent a verification link to</p>
        <p class="text-sm font-medium text-gray-900 mt-1">{{ $email }}</p>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
        <div class="flex">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-medium mb-2">Next Steps:</p>
                <ol class="list-decimal list-inside space-y-1">
                    <li>Check your email inbox (and spam folder)</li>
                    <li>Click the verification link in the email</li>
                    <li>Complete your supervisor profile</li>
                    <li>Start accepting students!</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="text-center">
        <p class="text-sm text-gray-600 mb-2">Didn't receive the email?</p>
        <a href="{{ route('supervisor.register') }}" class="text-sm text-ojt-primary hover:text-maroon-700 underline">
            Try again with a different email
        </a>
    </div>
</x-guest-layout>
