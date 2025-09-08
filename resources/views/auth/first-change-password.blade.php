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
                <input id="password" name="password" type="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary">
            </div>

            <button type="submit" class="w-full bg-ojt-primary text-white py-3 px-6 rounded-lg font-medium hover:bg-maroon-700 transition-colors duration-200">Save and continue</button>
        </form>
    </div>
</x-guest-layout>


