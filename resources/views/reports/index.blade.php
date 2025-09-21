<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Daily Reports</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-ojt-dark">Your Reports</h1>
                <a href="{{ route('reports.create') }}" class="bg-ojt-primary text-white px-4 py-2 rounded-lg hover:bg-maroon-700">Submit Report</a>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="divide-y">
                    @forelse($reports as $report)
                        <div class="p-4 sm:p-6 flex items-center justify-between">
                            <div>
                                <p class="text-ojt-dark font-medium">{{ $report->work_date->format('M d, Y') }}</p>
                                <p class="text-sm text-gray-500 line-clamp-2">{{ $report->summary }}</p>
                            </div>
                            <div class="text-xs flex items-center gap-3">
                                <span class="px-2 py-1 rounded-full {{ $report->status === 'approved' ? 'bg-green-100 text-green-800' : ($report->status === 'returned' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">{{ ucfirst($report->status) }}</span>
                                @if($report->attachment_path)
                                    <a href="{{ Storage::url($report->attachment_path) }}" target="_blank" class="text-ojt-primary underline">Attachment</a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">No reports yet.</div>
                    @endforelse
                </div>
            </div>
            <div class="mt-6">{{ $reports->links() }}</div>
        </div>
    </div>
</x-app-layout>


