<x-guest-layout>
	<div class="max-w-md mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-6">
		<div class="flex items-start gap-3">
			<div class="flex-shrink-0 h-10 w-10 rounded-full bg-ojt-primary/10 flex items-center justify-center text-ojt-primary">
				<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
					<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v5.5a.75.75 0 01-1.5 0v-5.5A.75.75 0 0110 5zm0 9a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
				</svg>
			</div>
			<div>
				<h2 class="text-lg font-semibold text-gray-900">Verification Link {{ $reason === 'expired' ? 'Expired' : 'Invalid' }}</h2>
				<p class="mt-1 text-sm text-gray-600">
					{{ $reason === 'expired' ? 'Your verification link has expired. Request a new link to continue.' : 'This verification link is invalid. Request a new link to continue.' }}
				</p>
			</div>
		</div>

		@if ($errors->any())
			<div class="mt-4 rounded-md bg-red-50 p-3 text-sm text-red-700">
				<ul class="list-disc list-inside">
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		@if (session('status'))
			<div class="mt-4 rounded-md bg-green-50 p-3 text-sm text-green-700">
				{{ session('status') }}
			</div>
		@endif

		@if (session('error'))
			<div class="mt-4 rounded-md bg-red-50 p-3 text-sm text-red-700">
				{{ session('error') }}
			</div>
		@endif

		<div class="mt-6 space-y-6">
			@if($email)
				<!-- Resend to same email -->
				<form method="POST" action="{{ route('supervisor.register.resend') }}" class="space-y-3">
					@csrf
					<input type="hidden" name="email" value="{{ $email }}" />
					<x-primary-button class="w-full justify-center">Resend Link to {{ $email }}</x-primary-button>
				</form>

				<div class="relative">
					<div class="absolute inset-0 flex items-center">
						<div class="w-full border-t border-gray-300"></div>
					</div>
					<div class="relative flex justify-center text-sm">
						<span class="px-2 bg-white text-gray-500">Or</span>
					</div>
				</div>
			@endif

			<!-- Enter different email -->
			<div>
				<p class="text-sm text-gray-600 mb-2">{{ $email ? 'Use a different email address:' : 'Enter your email to request a new link:' }}</p>
				<form method="POST" action="{{ route('supervisor.register.resend') }}" class="flex gap-2">
					@csrf
					<input id="email" name="email" type="email" value="{{ $email ?? '' }}" placeholder="Email address" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-ojt-primary focus:border-ojt-primary" />
					<x-primary-button>Send Link</x-primary-button>
				</form>
			</div>

			<div class="text-center">
				<a class="underline text-sm text-ojt-primary hover:text-ojt-primary/80" href="{{ route('supervisor.register') }}">Back to Registration</a>
				<span class="text-gray-400 mx-2">|</span>
				<a class="underline text-sm text-ojt-primary hover:text-ojt-primary/80" href="{{ route('login') }}">Login</a>
			</div>
		</div>
	</div>
</x-guest-layout>
