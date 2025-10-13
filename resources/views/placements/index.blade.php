<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">My Placement Requests</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-ojt-dark">Placement Requests</h1>
                <a href="{{ route('placements.create') }}" class="bg-ojt-primary text-white px-4 py-2 rounded-lg hover:bg-maroon-700">Notify Acceptance</a>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="divide-y divide-gray-100">
                    @forelse($requests as $req)
                        <div class="p-4 sm:p-6 {{ $req->status === 'declined' ? 'bg-red-50 border-l-4 border-red-400' : ($req->status === 'voided' ? 'bg-gray-50 border-l-4 border-gray-400' : ($req->status === 'approved' ? 'bg-green-50 border-l-4 border-green-400' : '')) }}">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <p class="text-ojt-dark font-medium text-lg">{{ $req->company?->name ?? ($req->external_company_name ?? 'External Company') }}</p>
                                    <p class="text-sm text-gray-500">
                                        Status: <span class="font-medium capitalize {{ $req->status === 'declined' ? 'text-red-600' : ($req->status === 'approved' ? 'text-green-600' : ($req->status === 'voided' ? 'text-gray-500' : 'text-yellow-600')) }}">{{ $req->status }}</span>
                                        @if($req->start_date) • Start: {{ $req->start_date->format('M d, Y') }} @endif
                                    </p>
                                    @if($req->status === 'declined' && $req->decline_reason)
                                        <p class="text-sm text-red-600 mt-1">Reason: {{ $req->decline_reason }}</p>
                                    @endif
                                    @if($req->status === 'voided')
                                        <p class="text-sm text-gray-500 mt-1">Automatically voided when another placement was approved</p>
                                    @endif
                                    @if($req->status === 'approved')
                                        <p class="text-sm text-green-600 mt-1">✅ Your placement has been approved! You can now start your OJT.</p>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="text-sm text-gray-500">{{ $req->created_at->diffForHumans() }}</div>
                                    @if($req->status === 'declined')
                                        <button onclick="dismissRequest({{ $req->id }})" class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-100 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">No placement requests yet.</div>
                    @endforelse
                </div>
            </div>

            <div class="mt-6">{{ $requests->links() }}</div>
        </div>
    </div>

    <script>
        function dismissRequest(requestId) {
            if (confirm('Are you sure you want to dismiss this declined request?')) {
                // Send AJAX request to mark as dismissed
                fetch(`/placements/${requestId}/dismiss`, { 
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Hide the request from the UI
                        const requestElement = document.querySelector(`[onclick="dismissRequest(${requestId})"]`).closest('.p-4');
                        requestElement.style.display = 'none';
                    } else {
                        alert('Failed to dismiss request. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to dismiss request. Please try again.');
                });
            }
        }
    </script>
</x-app-layout>


