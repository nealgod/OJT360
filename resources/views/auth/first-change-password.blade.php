<x-guest-layout>
    <div class="max-w-md mx-auto mt-10 bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <h1 class="text-2xl font-bold text-ojt-dark mb-2">Set your new password</h1>
        <p class="text-gray-600 mb-6">For security, please set a new password before continuing.</p>

        @if (session('status'))
            <div class="mb-4 text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.first-change.update') }}">
            @csrf

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New password</label>
                <div class="relative" x-data="{ show: false }">
                    <input id="password" name="password" :type="show ? 'text' : 'password'" required class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary @error('password') border-red-500 @enderror">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" @click="show = !show">
                        <svg class="h-5 w-5 text-gray-400" :class="{ 'hidden': show, 'block': !show }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg class="h-5 w-5 text-gray-400" :class="{ 'block': show, 'hidden': !show }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 .98-3.18 3.659-5.54 6.793-6.54M12 5c1.53 0 2.984.371 4.318 1.02M17.125 10.125A3 3 0 0115 12c0 .79-.27 1.52-.732 2.09M9 12a3 3 0 013-3m-3 3c0 .79.27 1.52.732 2.09M4.268 4.268L19.732 19.732" />
                        </svg>
                    </div>
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm password</label>
                <div class="relative" x-data="{ show: false }">
                    <input id="password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" required class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" @click="show = !show">
                        <svg class="h-5 w-5 text-gray-400" :class="{ 'hidden': show, 'block': !show }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg class="h-5 w-5 text-gray-400" :class="{ 'block': show, 'hidden': !show }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 .98-3.18 3.659-5.54 6.793-6.54M12 5c1.53 0 2.984.371 4.318 1.02M17.125 10.125A3 3 0 0115 12c0 .79-.27 1.52-.732 2.09M9 12a3 3 0 013-3m-3 3c0 .79.27 1.52.732 2.09M4.268 4.268L19.732 19.732" />
                        </svg>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-ojt-primary text-white py-3 px-6 rounded-lg font-medium hover:bg-maroon-700 transition-colors duration-200">Save and continue</button>
        </form>
    </div>
</x-guest-layout>


