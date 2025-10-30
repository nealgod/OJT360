<x-guest-layout>
	<div class="max-w-md mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-6">
		<div class="flex items-start gap-3">
			<div class="flex-shrink-0 h-10 w-10 rounded-full bg-ojt-primary/10 flex items-center justify-center text-ojt-primary">
				<!-- Exclamation icon -->
				<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
					<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v5.5a.75.75 0 01-1.5 0v-5.5A.75.75 0 0110 5zm0 9a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
				</svg>
			</div>
			<div>
				<h2 class="text-lg font-semibold text-gray-900">Registration Link {{ $reason === 'expired' ? 'Expired' : 'Invalid' }}</h2>
				<p class="mt-1 text-sm text-gray-600">
					{{ $reason === 'expired' ? 'Your registration link has expired. Request a new link to continue.' : 'This registration link is invalid. Request a new link to continue.' }}
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

	<div class="mt-6 space-y-6">
		@if($studentId)
			<form method="POST" action="{{ route('student.send-verification') }}" class="space-y-3">
				@csrf
				<input type="hidden" name="student_id" value="{{ $studentId }}" />
				<x-primary-button>Resend Link to Email</x-primary-button>
			</form>

			<div>
				<p class="text-sm text-gray-600">Or enter your Student ID to request a new link:</p>
				<form method="POST" action="{{ route('student.send-verification') }}" class="mt-2 flex gap-2">
					@csrf
					<input id="student_id" name="student_id" type="text" placeholder="Student ID" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-ojt-primary focus:border-ojt-primary" />
					<x-primary-button>Send Link</x-primary-button>
				</form>
			</div>

			<div>
				<a class="underline text-sm text-ojt-primary hover:text-ojt-primary/80" href="{{ route('student.verify-id') }}">Back to Activate Account</a>
			</div>
		@else
			<div class="space-y-4">
				<div class="rounded-md bg-yellow-50 p-3 text-sm text-yellow-800">
					@if(!empty($email))
						<p>The coordinator invitation for <strong>{{ $email }}</strong> has {{ $reason === 'expired' ? 'expired' : 'become invalid' }}.</p>
					@else
						<p>This coordinator invitation link is not valid.</p>
					@endif
				</div>
				<form method="POST" action="{{ route('coordinator.invite.resend') }}" class="flex gap-2">
					@csrf
					<input id="coord_email" name="email" type="email" value="{{ $email ?? '' }}" placeholder="Coordinator email" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-ojt-primary focus:border-ojt-primary" />
					<x-primary-button>Resend Invitation</x-primary-button>
				</form>
			</div>
		@endif
	</div>
</x-guest-layout>

