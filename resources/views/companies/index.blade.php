<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
            {{ __('Company Directory') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-ojt-dark mb-2">
                    @if(Auth::user()->isStudent())
                        Approved OJT Companies for {{ Auth::user()->studentProfile->department ?? 'Your Department' }}
                    @elseif(Auth::user()->isCoordinator())
                        Your Assigned Companies
                    @else
                        All OJT Companies
                    @endif
                </h1>
                <p class="text-gray-600">
                    @if(Auth::user()->isStudent())
                        Browse through companies approved for your department where you can complete your internship.
                    @elseif(Auth::user()->isCoordinator())
                        Manage companies assigned to your department.
                    @else
                        Browse through all partner companies in the system.
                    @endif
                </p>
                @if(Auth::user()->isCoordinator())
                    <div class="mt-4">
                        <a href="{{ route('coord.companies.create') }}" class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white text-sm font-medium rounded-lg hover:bg-maroon-700 transition-colors duration-200">
                            Add Company
                        </a>
                    </div>
                @endif
            </div>


            <!-- Companies Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="companiesGrid">
                @forelse($companies as $company)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200 {{ $company->status === 'inactive' ? 'opacity-75' : '' }}">
                        <div class="p-6">
                            <!-- Company Logo/Icon -->
                            <div class="w-16 h-16 {{ $company->status === 'inactive' ? 'bg-gray-400' : 'bg-ojt-primary' }} rounded-lg flex items-center justify-center mb-4">
                                <span class="text-white text-xl font-bold">
                                    {{ substr($company->name, 0, 1) }}
                                </span>
                            </div>

                            <!-- Company Info -->
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-lg font-semibold text-ojt-dark">{{ $company->name }}</h3>
                                @if(Auth::user()->isCoordinator())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $company->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($company->status) }}
                                    </span>
                                @endif
                            </div>
                            
                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <div class="flex items-start">
                                    <svg class="w-4 h-4 mt-0.5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="line-clamp-2">{{ $company->address }}</span>
                                </div>
                                
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>{{ $company->contact_person }}</span>
                                </div>
                                
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>{{ $company->contact_email }}</span>
                                </div>
                                
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <span>{{ $company->contact_phone }}</span>
                                </div>
                            </div>

                            @if(Auth::user()->isCoordinator() && ($company->coordinator_id === Auth::id() || $company->department === Auth::user()->coordinatorProfile?->department))
                                <div class="flex flex-wrap gap-2">
                                    <!-- Status Toggle -->
                                    <form method="POST" action="{{ route('coord.companies.toggle-status', $company) }}" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ $company->status === 'active' ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}"
                                                onclick="return confirm('{{ $company->status === 'active' ? 'Deactivate' : 'Activate' }} this company?')">
                                            {{ $company->status === 'active' ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    
                                    <!-- Edit Button -->
                                    <a href="{{ route('coord.companies.edit', $company) }}" 
                                       class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                        Edit
                                    </a>
                                    
                                    <!-- Delete Button -->
                                    <form method="POST" action="{{ route('coord.companies.destroy', $company) }}" class="inline-block" onsubmit="return confirm('Delete this company?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex items-center px-3 py-2 bg-white border border-red-300 text-red-700 text-sm font-medium rounded-lg hover:bg-red-50 transition-colors duration-200">Delete</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Companies Available</h3>
                        <p class="text-gray-500">
                            @if(Auth::user()->isStudent())
                                No companies have been assigned to your department yet. Please contact your OJT coordinator.
                            @elseif(Auth::user()->isCoordinator())
                                You haven't assigned any companies yet. Add companies to make them available to your students.
                            @else
                                There are currently no companies in the system.
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</x-app-layout>
