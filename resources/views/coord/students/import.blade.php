<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-ojt-dark leading-tight">
			{{ __('Import Students (Whitelist)') }}
		</h2>
	</x-slot>

	<div class="py-6 sm:py-12">
		<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="bg-white rounded-lg border border-gray-200 p-6">
				@if ($errors->any())
					<div class="mb-4">
						<ul class="list-disc list-inside text-sm text-red-600">
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif

				<h3 class="text-lg font-medium text-gray-900 mb-4">Upload CSV/XLSX</h3>
				<p class="text-sm text-gray-600 mb-4">Accepted columns (external format): <strong>Student ID, Student Name, Phone, E-Mail</strong>. Name may be in <strong>Lastname, Firstname Middlename</strong> format.</p>
				<!-- Removed download link per request -->
				<form action="{{ route('coord.students.import.preview') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
					@csrf
					<input type="file" name="file" accept=".csv,.xlsx,.xls,.txt" class="border rounded px-3 py-2 w-full" required>
					<div class="flex justify-between">
						<button type="submit" name="import_now" value="1" class="px-4 py-2 bg-green-600 text-white rounded">Upload</button>
						<button type="submit" class="px-4 py-2 bg-ojt-primary text-white rounded">Preview</button>
					</div>
					<div class="mt-4 flex justify-end">
						<a href="{{ route('coord.students.whitelist') }}" class="text-gray-700 underline text-sm">Back to Class List</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</x-app-layout>


