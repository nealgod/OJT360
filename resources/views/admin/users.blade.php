<x-app-layout>
	<div class="py-6 sm:py-12">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="mb-8">
				<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
					<div>
						<h1 class="text-2xl sm:text-3xl font-bold text-ojt-dark mb-2">Manage Users</h1>
						<p class="text-gray-600">Create and manage coordinator and supervisor accounts</p>
					</div>
					<div class="mt-4 sm:mt-0">
						<a href="{{ route('admin.users.create') }}" class="bg-ojt-primary text-white px-6 py-3 rounded-lg font-medium hover:bg-maroon-700 transition-colors duration-200 flex items-center">
							<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
							</svg>
							Create User
						</a>
					</div>
				</div>
			</div>

			@if(session('success'))
				<div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
					{{ session('success') }}
				</div>
			@endif

			<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-gray-200">
						<thead class="bg-gray-50">
							<tr>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
							</tr>
						</thead>
						<tbody class="bg-white divide-y divide-gray-200">
							@forelse($users as $user)
								<tr class="hover:bg-gray-50">
									<td class="px-6 py-4 whitespace-nowrap">
										<div class="flex items-center">
											<div class="w-10 h-10 bg-ojt-primary rounded-full flex items-center justify-center text-white text-sm font-bold">
												{{ substr($user->name, 0, 1) }}
											</div>
											<div class="ml-4">
												<div class="text-sm font-medium text-ojt-dark">{{ $user->name }}</div>
												<div class="text-xs text-gray-500">{{ $user->email }}</div>
											</div>
										</div>
									</td>
									<td class="px-6 py-4 whitespace-nowrap">
										<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
											@if($user->role === 'admin') bg-red-100 text-red-800
											@elseif($user->role === 'coordinator') bg-blue-100 text-blue-800
											@elseif($user->role === 'supervisor') bg-green-100 text-green-800
											@else bg-gray-100 text-gray-800
											@endif">
											{{ ucfirst($user->role ?? 'unknown') }}
										</span>
									</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
									<td class="px-6 py-4 whitespace-nowrap">
										@if($user->email_verified_at)
											<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Verified</span>
										@else
											<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
										@endif
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="5" class="px-6 py-12 text-center text-gray-500">
										<div class="flex flex-col items-center">
											<svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
											</svg>
											<p class="text-lg font-medium text-gray-900 mb-2">No users found</p>
											<p class="text-gray-500">Create a coordinator or supervisor account to get started.</p>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>

			@if($users->hasPages())
				<div class="mt-6">
					{{ $users->links() }}
				</div>
			@endif
		</div>
	</div>
</x-app-layout>
