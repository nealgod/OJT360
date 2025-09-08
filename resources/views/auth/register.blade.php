<x-guest-layout>
    <!-- Welcome Message -->
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-ojt-dark mb-2">Join OJT360</h2>
        <p class="text-gray-600 text-sm">Create your account to start monitoring internships</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
    @csrf

    <!-- Full Name -->
    <div>
        <label for="name" class="block text-sm font-medium text-ojt-dark mb-2">
            Full Name
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <input id="name" 
                   type="text" 
                   name="name" 
                   value="{{ old('name') }}" 
                   required 
                   autofocus 
                   autocomplete="name"
                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary transition-colors duration-200 text-ojt-dark placeholder-gray-400"
                   placeholder="Enter your full name">
        </div>
        <x-input-error :messages="$errors->get('name')" class="mt-2 text-ojt-danger text-sm" />
    </div>

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
                   autocomplete="username"
                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary transition-colors duration-200 text-ojt-dark placeholder-gray-400"
                   placeholder="Enter your email">
        </div>
        <x-input-error :messages="$errors->get('email')" class="mt-2 text-ojt-danger text-sm" />
    </div>

    <!-- Role selection removed: all self-registrations are interns by default. -->

    <!-- Password -->
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
                   autocomplete="new-password"
                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary transition-colors duration-200 text-ojt-dark placeholder-gray-400"
                   placeholder="Create a password">
        </div>
        <x-input-error :messages="$errors->get('password')" class="mt-2 text-ojt-danger text-sm" />
    </div>

    <!-- Confirm Password -->
    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-ojt-dark mb-2">
            Confirm Password
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <input id="password_confirmation" 
                   type="password" 
                   name="password_confirmation" 
                   required 
                   autocomplete="new-password"
                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary transition-colors duration-200 text-ojt-dark placeholder-gray-400"
                   placeholder="Confirm your password">
        </div>
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-ojt-danger text-sm" />
    </div>

    <!-- Terms and Conditions -->
    <div class="flex items-start">
        <input id="terms" 
               type="checkbox" 
               name="terms" 
               required
               class="h-4 w-4 mt-1 text-ojt-primary focus:ring-ojt-primary border-gray-300 rounded">
        <label for="terms" class="ml-2 text-sm text-gray-600">
            I agree to the 
            <a href="#" class="text-ojt-primary hover:text-ojt-secondary transition-colors duration-200">Terms of Service</a> 
            and 
            <a href="#" class="text-ojt-primary hover:text-ojt-secondary transition-colors duration-200">Privacy Policy</a>
        </label>
    </div>

    <!-- Register Button -->
    <button type="submit" 
            class="w-full bg-gradient-to-r from-ojt-primary to-maroon-700 text-white py-3 px-4 rounded-lg font-medium hover:from-maroon-700 hover:to-ojt-primary focus:outline-none focus:ring-2 focus:ring-ojt-primary focus:ring-offset-2 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
        Create Account
    </button>

    <!-- Divider -->
    <div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-gray-500">Already have an account?</span>
        </div>
    </div>

    <!-- Login Link -->
    <div class="text-center">
        <a href="{{ route('login') }}" 
           class="inline-flex items-center text-ojt-primary hover:text-ojt-secondary transition-colors duration-200 font-medium">
            <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Sign in instead
        </a>
    </div>
</form>
</x-guest-layout>
