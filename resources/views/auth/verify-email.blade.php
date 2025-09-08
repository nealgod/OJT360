<x-guest-layout>
    <!-- Back to Home Button -->
    <div class="mb-6">
        <a href="{{ url('/') }}" class="inline-flex items-center text-ojt-primary hover:text-ojt-secondary transition-colors duration-200 font-medium">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Home
        </a>
    </div>

    <!-- Email Verification Notice -->
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-ojt-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-ojt-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-ojt-dark mb-2">Verify Your Email</h2>
        <p class="text-gray-600 text-sm">Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-6 p-4 bg-ojt-success/10 border border-ojt-success/20 rounded-lg text-ojt-success text-sm">
            {{ session('status') }}
        </div>
    @endif

    <!-- Verification Info -->
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <p class="text-sm text-blue-800">
            If you didn't receive the email, we will gladly send you another.
        </p>
    </div>

    <!-- Resend Verification Form -->
    <form method="POST" action="{{ route('verification.send') }}" class="space-y-6">
        @csrf
        
        <!-- Resend Button -->
        <button type="submit" 
                class="w-full bg-gradient-to-r from-ojt-primary to-maroon-700 text-white py-3 px-4 rounded-lg font-medium hover:from-maroon-700 hover:to-ojt-primary focus:outline-none focus:ring-2 focus:ring-ojt-primary focus:ring-offset-2 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
            Resend Verification Email
        </button>
    </form>

    <!-- Logout Option -->
    <div class="text-center mt-6">
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" 
                    class="text-ojt-primary hover:text-ojt-secondary transition-colors duration-200 font-medium">
                Log Out
            </button>
        </form>
    </div>
</x-guest-layout>
