<x-guest-layout>
	<div class="mb-4 text-sm text-gray-600">
		{{ __('Activate your account using your Student ID and EVSU email that your coordinator uploaded in the class list.') }}
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

	@if (session('status'))
		<div class="mb-4 font-medium text-sm text-green-600">
			{{ session('status') }}
		</div>
	@endif

	<form method="POST" action="{{ route('activate') }}" class="space-y-4">
		@csrf

		<div>
			<label for="student_id" class="block text-sm font-medium text-gray-700">Student ID</label>
			<input id="student_id" name="student_id" type="text" value="{{ old('student_id') }}" required autofocus class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
		</div>

		<div>
			<label for="email" class="block text-sm font-medium text-gray-700">EVSU Email</label>
			<input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@evsu.edu.ph" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
		</div>

		<div>
			<label for="password" class="block text-sm font-medium text-gray-700">Password</label>
			<input id="password" name="password" type="password" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
		</div>

		<div>
			<label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
			<input id="password_confirmation" name="password_confirmation" type="password" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
		</div>

		<div class="flex items-center justify-between">
			<a href="{{ route('login') }}" class="text-sm text-ojt-primary hover:underline">Back to login</a>
			<button type="submit" class="inline-flex items-center px-4 py-2 bg-ojt-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-ojt-primary/90 focus:bg-ojt-primary/90 active:bg-ojt-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ojt-primary transition ease-in-out duration-150">
				Activate Account
			</button>
		</div>
	</form>
</x-guest-layout>


