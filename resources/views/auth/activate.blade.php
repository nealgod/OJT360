<x-guest-layout>
	<div class="max-w-md mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-6">
		<h2 class="text-lg font-semibold text-gray-900">Student Registration</h2>
		<p class="mt-1 text-sm text-gray-600">We updated our process. Please use the new Activate page to begin registration.</p>

		<div class="mt-6 flex items-center justify-between">
			<a href="{{ route('login') }}" class="text-sm text-ojt-primary hover:underline">Back to login</a>
			<a href="{{ route('student.verify-id') }}" class="inline-flex items-center px-4 py-2 bg-ojt-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-ojt-primary/90 focus:bg-ojt-primary/90 active:bg-ojt-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ojt-primary transition ease-in-out duration-150">Activate Account</a>
		</div>
	</div>
</x-guest-layout>

