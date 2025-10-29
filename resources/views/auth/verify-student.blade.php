<x-guest-layout>
	<div class="mb-4 text-sm text-gray-600">
		{{ __('Enter your Student ID to receive a verification link at your school email.') }}
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

	<form method="POST" action="{{ route('student.send-verification') }}" class="space-y-4">
		@csrf

		<div>
			<label for="student_id" class="block text-sm font-medium text-gray-700">Student ID</label>
			<input id="student_id" name="student_id" type="text" value="{{ old('student_id') }}" required autofocus class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
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


