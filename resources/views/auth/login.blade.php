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
                       class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary transition-colors duration-200 text-ojt-dark bg-white">
                <button type="button" aria-label="Toggle password visibility" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-ojt-primary"
                        onclick="(function(btn){const input=document.getElementById('password');const eye=btn.querySelector('[data-eye]');const eyeOff=btn.querySelector('[data-eye-off]');const isPwd=input.getAttribute('type')==='password';input.setAttribute('type', isPwd?'text':'password');eye.classList.toggle('hidden', !isPwd);eyeOff.classList.toggle('hidden', isPwd);})(this)">
                    <svg data-eye class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg data-eye-off class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.284-3.95M6.223 6.223A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.178 5.021M15 12a3 3 0 00-4.95-2.121M3 3l18 18" />
                    </svg>
                </button>
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

        <!-- Register and Home Buttons -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <a href="{{ route('register') }}" 
               class="w-full inline-flex items-center justify-center border-2 border-ojt-primary text-ojt-primary px-4 py-3 rounded-lg font-medium hover:bg-ojt-primary hover:text-white transition-colors duration-200">
                Create Account
                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            <a href="{{ url('/') }}" 
               class="w-full inline-flex items-center justify-center bg-white border-2 border-gray-300 text-gray-700 px-4 py-3 rounded-lg font-medium hover:bg-gray-50 transition-colors duration-200">
                Home
            </a>
        </div>
    </form>
</x-guest-layout>
