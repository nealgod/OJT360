@php($title = 'Complete Coordinator Registration')
<x-guest-layout>
	<div class="mb-4 text-sm text-gray-600">
		Please set your name and password to complete your coordinator account.
	</div>

	@if ($errors->any())
		<div class="mb-4">
			<ul class="text-sm text-red-600 list-disc list-inside">
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	<form method="POST" action="{{ route('coordinator.complete') }}" class="space-y-4">
		@csrf
		<input type="hidden" name="token" value="{{ $token }}" />

		<div>
			<label for="email" class="block text-sm font-medium text-gray-700">Email</label>
			<input id="email" type="email" value="{{ $email }}" disabled readonly class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed" />
		</div>

		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
			<div>
				<label for="department" class="block text-sm font-medium text-gray-700">Department</label>
				<input id="department" type="text" value="{{ $department ?? 'N/A' }}" disabled readonly class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed" />
			</div>
			<div>
				<label for="program" class="block text-sm font-medium text-gray-700">Program</label>
				<input id="program" type="text" value="{{ $program ?? 'N/A' }}" disabled readonly class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed" />
			</div>
		</div>

		<div>
			<label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
			<input id="name" type="text" name="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required autofocus placeholder="Lastname, Firstname, Middlename" />
		</div>

		<div>
			<label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
			<input id="phone" type="tel" name="phone" placeholder="+63 912 345 6789" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required />
		</div>


		<div>
			<label for="password" class="block text-sm font-medium text-gray-700">Password</label>
			<div class="mt-1 relative">
				<x-text-input id="password" type="password" name="password" class="block w-full pr-10 border-gray-300 rounded-md shadow-sm" required />
				<button type="button" aria-label="Toggle password" data-target="password" class="toggle-pass absolute inset-y-0 right-0 px-3 text-gray-500 hover:text-gray-700">
					<!-- eye icon -->
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5s8.577 3.01 9.964 7.183c.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5S3.423 16.49 2.036 12.322z"/>
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
					</svg>
				</button>
			</div>
		</div>

		<div>
			<label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
			<div class="mt-1 relative">
				<x-text-input id="password_confirmation" type="password" name="password_confirmation" class="block w-full pr-10 border-gray-300 rounded-md shadow-sm" required />
				<button type="button" aria-label="Toggle confirm password" data-target="password_confirmation" class="toggle-pass absolute inset-y-0 right-0 px-3 text-gray-500 hover:text-gray-700">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5s8.577 3.01 9.964 7.183c.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5S3.423 16.49 2.036 12.322z"/>
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
					</svg>
				</button>
			</div>
		</div>

		<div class="flex items-center justify-end">
			<x-primary-button>Complete Registration</x-primary-button>
		</div>
	</form>

	<script>
		(function(){
			const toggles = document.querySelectorAll('.toggle-pass');
			toggles.forEach(btn => {
				btn.addEventListener('click', () => {
					const targetId = btn.getAttribute('data-target');
					const input = document.getElementById(targetId);
					if (!input) return;
					input.type = input.type === 'password' ? 'text' : 'password';
				});
			});
		})();
	</script>
</x-guest-layout>


