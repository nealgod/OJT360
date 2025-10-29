<x-guest-layout>
	<div class="mb-4 text-sm text-gray-600">
		{{ __('Complete your registration by setting your password and details.') }}
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

	<form method="POST" action="{{ route('student.complete') }}" class="space-y-4" x-data="{ showPassword: false, showConfirm: false }">
		@csrf
		<input type="hidden" name="token" value="{{ $token }}" />

		<div class="grid grid-cols-1 gap-4">
			<div>
				<label class="block text-sm font-medium text-gray-700">Student ID</label>
				<input type="text" value="{{ $studentId }}" class="mt-1 block w-full border-gray-300 rounded-md bg-gray-100" disabled />
			</div>
			<div>
				<label class="block text-sm font-medium text-gray-700">Name</label>
				<input type="text" value="{{ $name }}" class="mt-1 block w-full border-gray-300 rounded-md bg-gray-100" disabled />
			</div>
			<div>
				<label class="block text-sm font-medium text-gray-700">Email</label>
				<input type="email" value="{{ $email }}" class="mt-1 block w-full border-gray-300 rounded-md bg-gray-100" disabled />
			</div>
		</div>

		<div>
			<label for="phone" class="block text-sm font-medium text-gray-700">Phone (optional)</label>
			<input id="phone" name="phone" type="text" value="{{ old('phone') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
		</div>

		<div>
			<label for="password" class="block text-sm font-medium text-gray-700">Password</label>
			<div class="mt-1 relative">
				<input :type="showPassword ? 'text' : 'password'" id="password" name="password" required class="block w-full border-gray-300 rounded-md shadow-sm pr-10" />
				<button type="button" class="absolute inset-y-0 right-0 px-3 text-gray-500 hover:text-gray-700" @click="showPassword = !showPassword" aria-label="Toggle password visibility">
					<svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
						<path d="M10 3c-4.5 0-8.3 2.9-9.8 7 1.5 4.1 5.3 7 9.8 7s8.3-2.9 9.8-7C18.3 5.9 14.5 3 10 3zm0 12a5 5 0 110-10 5 5 0 010 10z"/>
						<path d="M10 7a3 3 0 100 6 3 3 0 000-6z"/>
					</svg>
					<svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
						<path d="M3.98 8.223a.75.75 0 10-1.06 1.06l2.27 2.27A10.53 10.53 0 003 12c2.25 4.5 6 7 9 7 1.4 0 2.77-.36 4.06-1.02l2.96 2.96a.75.75 0 101.06-1.06l-18-18zM12 17c-2.21 0-4-1.79-4-4 0-.56.12-1.1.34-1.58l5.24 5.24c-.48.22-1.02.34-1.58.34zm6.66-1.42l-2.2-2.2c.35-.71.54-1.5.54-2.38 0-2.21-1.79-4-4-4-.88 0-1.67.19-2.38.54l-2.2-2.2C9.23 4.36 10.6 4 12 4c3 0 6.75 2.5 9 7-.61 1.22-1.44 2.32-2.34 3.22z"/>
					</svg>
				</button>
			</div>
		</div>

		<div>
			<label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
			<div class="mt-1 relative">
				<input :type="showConfirm ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" required class="block w-full border-gray-300 rounded-md shadow-sm pr-10" />
				<button type="button" class="absolute inset-y-0 right-0 px-3 text-gray-500 hover:text-gray-700" @click="showConfirm = !showConfirm" aria-label="Toggle confirm visibility">
					<svg x-show="!showConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
						<path d="M10 3c-4.5 0-8.3 2.9-9.8 7 1.5 4.1 5.3 7 9.8 7s8.3-2.9 9.8-7C18.3 5.9 14.5 3 10 3zm0 12a5 5 0 110-10 5 5 0 010 10z"/>
						<path d="M10 7a3 3 0 100 6 3 3 0 000-6z"/>
					</svg>
					<svg x-show="showConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
						<path d="M3.98 8.223a.75.75 0 10-1.06 1.06l2.27 2.27A10.53 10.53 0 003 12c2.25 4.5 6 7 9 7 1.4 0 2.77-.36 4.06-1.02l2.96 2.96a.75.75 0 101.06-1.06l-18-18zM12 17c-2.21 0-4-1.79-4-4 0-.56.12-1.1.34-1.58l5.24 5.24c-.48.22-1.02.34-1.58.34zm6.66-1.42l-2.2-2.2c.35-.71.54-1.5.54-2.38 0-2.21-1.79-4-4-4-.88 0-1.67.19-2.38.54l-2.2-2.2C9.23 4.36 10.6 4 12 4c3 0 6.75 2.5 9 7-.61 1.22-1.44 2.32-2.34 3.22z"/>
					</svg>
				</button>
			</div>
		</div>

		<div class="flex items-center justify-between">
			<a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
				{{ __('Cancel') }}
			</a>

			<x-primary-button>
				{{ __('Create Account') }}
			</x-primary-button>
		</div>
	</form>
</x-guest-layout>


