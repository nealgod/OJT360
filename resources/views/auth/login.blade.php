<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-6 p-4 bg-ojt-success/10 border border-ojt-success/20 rounded-lg text-ojt-success text-sm" :status="session('status')" />

    <!-- Welcome Message -->
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-ojt-dark mb-2">Welcome Back</h2>
        <p class="text-gray-600 text-sm">Sign in to your OJT360 account</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Input -->
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
                       autocomplete="email"
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary transition-colors duration-200 text-ojt-dark bg-white">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-ojt-danger text-sm" />
        </div>

        <!-- Password Input -->
        <div>
            <label for="password" class="block text-sm font-medium text-ojt-dark mb-2">
                Password
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input id="password" 
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="current-password"
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary transition-colors duration-200 text-ojt-dark bg-white">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-ojt-danger text-sm" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="flex items-center">
                <input id="remember_me" 
                       type="checkbox" 
                       name="remember"
                       class="h-4 w-4 text-ojt-primary focus:ring-ojt-primary border-gray-300 rounded">
                <span class="ml-2 text-sm text-gray-600">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" 
                   class="text-sm text-ojt-primary hover:text-ojt-secondary transition-colors duration-200">
                    Forgot password?
                </a>
            @endif
        </div>

        <!-- Login Button -->
        <button type="submit" 
                class="w-full bg-gradient-to-r from-ojt-primary to-maroon-700 text-white py-3 px-4 rounded-lg font-medium hover:from-maroon-700 hover:to-ojt-primary focus:outline-none focus:ring-2 focus:ring-ojt-primary focus:ring-offset-2 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
            Sign In
        </button>

        <!-- Divider -->
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">New to OJT360?</span>
            </div>
        </div>

        <!-- Register Link -->
        <div class="text-center">
            <a href="{{ route('register') }}" 
               class="inline-flex items-center text-ojt-primary hover:text-ojt-secondary transition-colors duration-200 font-medium">
                Create an account
                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </form>
</x-guest-layout>
