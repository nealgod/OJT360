<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-ojt-dark leading-tight">
			{{ __('Import Preview') }}
		</h2>
	</x-slot>

	<div class="py-6 sm:py-12">
		<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="bg-white rounded-lg border border-gray-200 p-6">
				<div class="flex items-center justify-between mb-4">
					<h3 class="text-lg font-medium text-gray-900">Validation Results</h3>
					<div class="space-x-4 text-sm">
						<a href="{{ route('coord.students.whitelist') }}" class="text-gray-700 underline">Back to Class List</a>
					</div>
				</div>

				<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
					<div class="bg-green-50 border border-green-200 rounded-lg p-4">
						<p class="text-sm text-green-800">Valid rows</p>
						<p class="text-2xl font-bold text-green-900">{{ count($results['valid']) }}</p>
					</div>
					<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
						<p class="text-sm text-yellow-800">Invalid rows</p>
						<p class="text-2xl font-bold text-yellow-900">{{ count($results['invalid']) }}</p>
					</div>
					<div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
						<p class="text-sm text-gray-800">Total processed</p>
						<p class="text-2xl font-bold text-gray-900">{{ $results['meta']['total_rows'] ?? (count($results['valid']) + count($results['invalid'])) }}</p>
					</div>
				</div>

				<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
					<div>
						<h4 class="font-semibold mb-2">Valid Rows ({{ count($results['valid']) }})</h4>
						<div class="border rounded p-3 max-h-96 overflow-auto text-sm bg-white">
							<table class="min-w-full text-left border">
								<thead class="bg-gray-50">
									<tr>
										<th class="px-2 py-1 border">Student ID</th>
										<th class="px-2 py-1 border">Student Name</th>
										<th class="px-2 py-1 border">E-Mail</th>
										<th class="px-2 py-1 border">Phone</th>
									</tr>
								</thead>
								<tbody>
									@foreach($results['valid'] as $r)
									<tr>
										<td class="px-2 py-1 border">{{ $r['student_id'] }}</td>
										<td class="px-2 py-1 border">{{ $r['name'] }}</td>
										<td class="px-2 py-1 border">{{ $r['email'] }}</td>
										<td class="px-2 py-1 border">{{ $r['contact_number'] ?? '' }}</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
					<div>
						<h4 class="font-semibold mb-2">Invalid Rows ({{ count($results['invalid']) }})</h4>
						<div class="border rounded p-3 max-h-96 overflow-auto text-sm bg-white">
							<table class="min-w-full text-left border">
								<thead class="bg-gray-50">
									<tr>
								<th class="px-2 py-1 border">Line</th>
										<th class="px-2 py-1 border">Row</th>
								<th class="px-2 py-1 border">Errors</th>
									</tr>
								</thead>
								<tbody>
									@foreach($results['invalid'] as $e)
									<tr>
										<td class="px-2 py-1 border">{{ $e['line'] }}</td>
								<td class="px-2 py-1 border text-xs">
									<div class="space-y-1">
										<div><span class="font-medium">Student ID:</span> {{ $e['row']['student_id'] ?? ($e['row']['student id'] ?? '') }}</div>
										<div><span class="font-medium">Name:</span> {{ $e['row']['name'] ?? ($e['row']['student name'] ?? '') }}</div>
										<div><span class="font-medium">Email:</span> {{ $e['row']['email'] ?? ($e['row']['e-mail'] ?? '') }}</div>
										<div><span class="font-medium">Phone:</span> {{ $e['row']['contact_number'] ?? ($e['row']['phone'] ?? '') }}</div>
									</div>
								</td>
								<td class="px-2 py-1 border text-xs">
											<ul class="list-disc list-inside">
												@foreach($e['errors'] as $err)
												<li>{{ $err }}</li>
												@endforeach
											</ul>
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>

				@if(count($results['valid']))
					<form action="{{ route('coord.students.import.commit') }}" method="POST" class="mt-6">
						@csrf
						<input type="hidden" name="rows" value='@json($results['valid'])'>
						<div class="flex items-center justify-between">
							<p class="text-sm text-gray-600">Only valid rows will be imported. Invalid rows are ignored.</p>
							<button type="submit" class="px-4 py-2 bg-ojt-primary text-white rounded">Commit Import</button>
						</div>
					</form>
				@endif
			</div>
		</div>
	</div>
</x-app-layout>


