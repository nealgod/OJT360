<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-ojt-dark leading-tight">
			{{ __('Whitelist Status') }}
		</h2>
	</x-slot>

	<div class="py-6 sm:py-12">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="bg-white rounded-lg border border-gray-200 p-6">
				<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
					<div>
						<h3 class="text-lg font-medium text-gray-900">{{ $program->name ?? 'Program' }} Whitelist</h3>
						<p class="text-sm text-gray-600">Students allowed to register for this program</p>
					</div>
					<div class="flex flex-col sm:flex-row gap-2 sm:gap-2 w-full sm:w-auto sm:justify-end">
						<a href="{{ route('coord.students.import') }}" class="inline-flex justify-center px-4 py-2 bg-ojt-primary text-white rounded text-sm hover:bg-maroon-700">
							Upload Class List
						</a>
						<form method="POST" action="{{ route('coord.students.whitelist.end-term') }}" onsubmit="return confirm('Archive all current pending and activated records?')" class="w-full sm:w-auto">
							@csrf
							<button type="submit" class="inline-flex justify-center w-full sm:w-auto px-4 py-2 bg-gray-800 text-white rounded text-sm hover:bg-gray-900">
								End Term: Archive All
							</button>
						</form>
					</div>
				</div>

				<form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
					<input type="text" name="q" value="{{ $search }}" placeholder="Search ID, name, or email" class="border rounded px-3 py-2 w-full" />
					<select name="status" class="border rounded px-3 py-2 w-full">
						<option value="">All</option>
						<option value="pending" {{ $status==='pending' ? 'selected' : '' }}>Pending</option>
						<option value="activated" {{ $status==='activated' ? 'selected' : '' }}>Activated</option>
					</select>
					<div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
						<label class="inline-flex items-center text-sm text-gray-700">
							<input type="checkbox" name="show_archived" value="1" {{ ($includeArchived ?? false) ? 'checked' : '' }} class="mr-2">
							Show archived
						</label>
						<button class="inline-flex justify-center px-4 py-2 bg-gray-800 text-white rounded text-sm hover:bg-gray-900">Filter</button>
					</div>
				</form>

				<div class="overflow-x-auto">
					<table class="min-w-full text-left border">
						<thead class="bg-gray-50">
							<tr>
								<th class="px-3 py-2 border">Student ID</th>
								<th class="px-3 py-2 border">Name</th>
								<th class="px-3 py-2 border">Email</th>
								<th class="px-3 py-2 border">Phone</th>
								<th class="px-3 py-2 border">Status</th>
								<th class="px-3 py-2 border">Uploaded</th>
							</tr>
						</thead>
						<tbody class="bg-white">
							@forelse($whitelist as $row)
							<tr>
								<td class="px-3 py-2 border font-mono">{{ $row->student_id }}</td>
								<td class="px-3 py-2 border">{{ $row->name }}</td>
								<td class="px-3 py-2 border">{{ $row->email }}</td>
								<td class="px-3 py-2 border">{{ $row->contact_number ?? '—' }}</td>
								<td class="px-3 py-2 border capitalize">
									<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
										{{ $row->status==='pending' ? 'bg-yellow-100 text-yellow-800' : ($row->status==='activated' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}
									">
										{{ $row->status }}
									</span>
								</td>
								<td class="px-3 py-2 border text-sm text-gray-600">{{ $row->created_at->format('M d, Y') }}</td>
							</tr>
							@empty
							<tr>
								<td colspan="6" class="px-3 py-6 text-center text-gray-500">No records found.</td>
							</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				<div class="mt-4">{{ $whitelist->links() }}</div>
			</div>
		</div>
	</div>
</x-app-layout>


