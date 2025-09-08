<x-guest-layout>
    <!-- Welcome Message -->
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-ojt-dark mb-2">Reset Password</h2>
        <p class="text-gray-600 text-sm">Enter your email to receive a reset link</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6 p-4 bg-ojt-success/10 border border-ojt-success/20 rounded-lg text-ojt-success text-sm" :status="session('status')" />

    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <p class="text-sm text-blue-800">
            Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
        </p>
    </div>

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
    @csrf

    <!-- Email Address -->
    <div>
        <label for="email" class="block text-sm font-medium text-ojt-dark mb-2">
            Email Address
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                </svg>
            </div>
            <input id="email" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autofocus
                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary transition-colors duration-200 text-ojt-dark placeholder-gray-400"
                   placeholder="Enter your email">
        </div>
        <x-input-error :messages="$errors->get('email')" class="mt-2 text-ojt-danger text-sm" />
    </div>

    <!-- Submit Button -->
    <button type="submit" 
            class="w-full bg-gradient-to-r from-ojt-primary to-maroon-700 text-white py-3 px-4 rounded-lg font-medium hover:from-maroon-700 hover:to-ojt-primary focus:outline-none focus:ring-2 focus:ring-ojt-primary focus:ring-offset-2 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
        Send Reset Link
    </button>

    <!-- Back to Login -->
    <div class="text-center">
        <a href="{{ route('login') }}" 
           class="inline-flex items-center text-ojt-primary hover:text-ojt-secondary transition-colors duration-200 font-medium">
            <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to login
        </a>
    </div>
</form>
</x-guest-layout>
