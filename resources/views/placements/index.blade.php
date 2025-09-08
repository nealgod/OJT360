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
                        <div class="p-4 sm:p-6 flex items-center justify-between">
                            <div>
                                <p class="text-ojt-dark font-medium">{{ $req->company->name ?? ($req->external_company_name ?? 'External Company') }}</p>
                                <p class="text-sm text-gray-500">Status: <span class="font-medium capitalize">{{ $req->status }}</span> @if($req->start_date) • Start: {{ $req->start_date->format('M d, Y') }} @endif</p>
                            </div>
                            <div class="text-sm text-gray-500">{{ $req->created_at->diffForHumans() }}</div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">No placement requests yet.</div>
                    @endforelse
                </div>
            </div>

            <div class="mt-6">{{ $requests->links() }}</div>
        </div>
    </div>
</x-app-layout>


