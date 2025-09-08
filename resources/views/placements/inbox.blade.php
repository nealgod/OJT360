<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Placement Requests Inbox</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="divide-y divide-gray-100">
                    @forelse($requests as $req)
                        <div class="p-4 sm:p-6">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-ojt-dark font-semibold">{{ $req->student->name }}</p>
                                    <p class="text-sm text-gray-600">Company: {{ $req->company->name ?? ($req->external_company_name ?? 'External Company') }}</p>
                                    @if($req->note)
                                        <p class="mt-2 text-sm text-gray-700">{{ $req->note }}</p>
                                    @endif
                                    @if($req->proof_path)
                                        <p class="mt-2 text-sm"><a href="{{ Storage::url($req->proof_path) }}" class="text-ojt-primary underline" target="_blank">View proof</a></p>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500">{{ $req->created_at->diffForHumans() }}</div>
                            </div>

                            <div class="mt-4 flex items-center gap-3">
                                <form method="POST" action="{{ route('coord.placements.approve', $req) }}" class="flex items-center gap-2">
                                    @csrf
                                    <input type="date" name="start_date" class="border border-gray-300 rounded-md px-3 py-2 text-sm" required />
                                    <button class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('coord.placements.decline', $req) }}" class="flex items-center gap-2">
                                    @csrf
                                    <input type="text" name="reason" placeholder="Reason" class="border border-gray-300 rounded-md px-3 py-2 text-sm" required />
                                    <button class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700">Decline</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">No pending requests.</div>
                    @endforelse
                </div>
            </div>
            <div class="mt-6">{{ $requests->links() }}</div>
        </div>
    </div>
</x-app-layout>


