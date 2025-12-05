<x-app-layout>
	<div class="py-6 sm:py-12">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<!-- Header -->
			<div class="mb-8">
				<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
					<div>
						<h1 class="text-2xl sm:text-3xl font-bold text-ojt-dark mb-2">Manage Users</h1>
						<p class="text-gray-600">Create and manage user accounts</p>
					</div>
					<a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center bg-ojt-primary text-white px-6 py-3 rounded-lg font-medium hover:bg-maroon-700 transition-colors">
						<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
						</svg>
						Create User
					</a>
				</div>
			</div>

			@if(session('success'))
				<div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center">
					<svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
						<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
					</svg>
					{{ session('success') }}
				</div>
			@endif

			<!-- Quick Stats -->
			<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
				<div class="bg-white rounded-lg border border-gray-200 p-4">
					<div class="flex items-center justify-between">
						<div>
							<p class="text-sm text-gray-500">Total Users</p>
							<p class="text-2xl font-bold text-ojt-dark">{{ $stats['total'] }}</p>
						</div>
						<div class="bg-gray-100 rounded-full p-3">
							<svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
							</svg>
						</div>
					</div>
				</div>
				<div class="bg-white rounded-lg border border-gray-200 p-4">
					<div class="flex items-center justify-between">
						<div>
							<p class="text-sm text-gray-500">Coordinators</p>
							<p class="text-2xl font-bold text-blue-600">{{ $stats['coordinators'] }}</p>
						</div>
						<div class="bg-blue-50 rounded-full p-3">
							<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
							</svg>
						</div>
					</div>
				</div>
				<div class="bg-white rounded-lg border border-gray-200 p-4">
					<div class="flex items-center justify-between">
						<div>
							<p class="text-sm text-gray-500">Supervisors</p>
							<p class="text-2xl font-bold text-green-600">{{ $stats['supervisors'] }}</p>
						</div>
						<div class="bg-green-50 rounded-full p-3">
							<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
							</svg>
						</div>
					</div>
				</div>
				<div class="bg-white rounded-lg border border-gray-200 p-4">
					<div class="flex items-center justify-between">
						<div>
							<p class="text-sm text-gray-500">Interns</p>
							<p class="text-2xl font-bold text-purple-600">{{ $stats['students'] }}</p>
						</div>
						<div class="bg-purple-50 rounded-full p-3">
							<svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
							</svg>
						</div>
					</div>
				</div>
			</div>

			<!-- Filters -->
			<div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
				<form method="GET" action="{{ route('admin.users') }}" class="flex flex-col sm:flex-row gap-4">
					<div class="flex-1">
						<label for="role" class="block text-sm font-medium text-gray-700 mb-1">Filter by Role</label>
						<select name="role" id="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary" onchange="this.form.submit()">
							<option value="all" {{ request('role') === 'all' || !request('role') ? 'selected' : '' }}>All Roles</option>
							<option value="coordinator" {{ request('role') === 'coordinator' ? 'selected' : '' }}>Coordinators</option>
							<option value="supervisor" {{ request('role') === 'supervisor' ? 'selected' : '' }}>Supervisors</option>
							<option value="intern" {{ request('role') === 'intern' ? 'selected' : '' }}>Interns</option>
						</select>
					</div>
					<div class="flex-1">
						<label for="status" class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
						<select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary" onchange="this.form.submit()">
							<option value="" {{ !request('status') ? 'selected' : '' }}>All Status</option>
							<option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
							<option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
						</select>
					</div>
					@if(request('role') || request('status'))
						<div class="flex items-end">
							<a href="{{ route('admin.users') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
								Clear Filters
							</a>
						</div>
					@endif
				</form>
			</div>

			<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-gray-200">
						<thead class="bg-gray-50">
							<tr>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
								<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
							</tr>
						</thead>
						<tbody class="bg-white divide-y divide-gray-200">
							@forelse($users as $user)
								<tr>
									<td class="px-6 py-4 whitespace-nowrap">
										<div class="flex items-center">
											<div class="flex-shrink-0 h-10 w-10">
												<x-user-avatar :user="$user" size="h-10 w-10" />
											</div>
											<div class="ml-4">
												<div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
												<div class="text-xs text-gray-500">{{ $user->email }}</div>
											</div>
										</div>
									</td>
									<td class="px-6 py-4 whitespace-nowrap">
										<div class="flex flex-col items-start">
											<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
												@if($user->role === 'admin') bg-red-100 text-red-800
												@elseif($user->role === 'coordinator') bg-blue-100 text-blue-800
												@elseif($user->role === 'supervisor') bg-green-100 text-green-800
												@elseif($user->role === 'intern') bg-purple-100 text-purple-800
												@else bg-gray-100 text-gray-800
												@endif">
												{{ ucfirst($user->role ?? 'unknown') }}
											</span>
											
											{{-- Role-specific details --}}
											@if($user->role === 'supervisor' && $user->supervisorProfile?->company)
												<div class="text-xs text-gray-500 mt-1 flex items-center">
													<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
													{{ $user->supervisorProfile->company->name }}
												</div>
											@elseif($user->role === 'intern' && $user->studentProfile)
												@if($user->studentProfile->program)
													<div class="text-xs text-gray-500 mt-1 flex items-center">
														<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
														{{ $user->studentProfile->program->name }}
													</div>
												@endif
												@if($user->studentProfile->supervisor?->supervisorProfile?->company)
													<div class="text-xs text-gray-500 mt-0.5 flex items-center">
														<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
														{{ $user->studentProfile->supervisor->supervisorProfile->company->name }}
													</div>
												@endif
											@elseif($user->role === 'coordinator' && $user->coordinatorProfile)
												@if($user->coordinatorProfile->program)
													<div class="text-xs text-gray-500 mt-1 flex items-center">
														<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
														{{ $user->coordinatorProfile->program->name }}
													</div>
												@endif
											@endif
										</div>
									</td>
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
									<td colspan="4" class="px-6 py-12 text-center text-gray-500">
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
