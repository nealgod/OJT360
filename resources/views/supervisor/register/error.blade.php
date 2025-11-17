<x-guest-layout>
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
        </div>
        <h2 class="text-xl font-semibold text-red-600 mb-2">{{ $error }}</h2>
        <p class="text-sm text-gray-600">{{ $message }}</p>
    </div>

    <div class="flex flex-col space-y-3">
        <a href="{{ route('supervisor.register') }}" class="inline-flex items-center justify-center bg-ojt-primary text-white py-2 px-4 rounded-md hover:bg-maroon-700 transition-colors">
            Try Again
        </a>
        
        <a href="{{ route('login') }}" class="text-center text-sm text-ojt-primary hover:text-maroon-700 underline">
            Back to Login
        </a>
    </div>
</x-guest-layout>
