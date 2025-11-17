<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Enter your email address to receive a verification link.') }}
    </div>

    @if ($errors->any())
        <div class="mb-4">
            <ul class="list-disc list-inside text-sm text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 font-medium text-sm text-red-600">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('supervisor.register.send') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
        </div>

        <div class="flex items-center justify-between">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                {{ __('Back to login') }}
            </a>

            <x-primary-button>
                {{ __('Send Verification Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
