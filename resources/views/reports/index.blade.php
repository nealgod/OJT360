<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Daily Reports</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-ojt-dark">Your Reports</h1>
                <div class="flex gap-3">
                    <a href="{{ route('reports.weekly') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        📄 Generate Weekly Report
                    </a>
                    <a href="{{ route('reports.create') }}" class="bg-ojt-primary text-white px-4 py-2 rounded-lg hover:bg-maroon-700">Submit Report</a>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" id="searchInput" placeholder="Search reports by content..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-ojt-primary focus:border-ojt-primary">
                    </div>
                    <div class="flex gap-3">
                        <select id="monthFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-ojt-primary focus:border-ojt-primary">
                            <option value="all">All Months</option>
                            @for($i = 0; $i < 12; $i++)
                                @php $date = now()->subMonths($i); @endphp
                                <option value="{{ $date->format('Y-m') }}">{{ $date->format('F Y') }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>


            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="divide-y">
                    <div class="p-4 sm:p-6 bg-ojt-accent/5 border-b border-ojt-accent/20 flex items-center justify-between">
                        <div class="text-sm text-ojt-dark">Attachments show previews for images; PDFs/docs open in a new tab.</div>
                    </div>
                    @forelse($reports as $report)
                        <div class="p-4 sm:p-6 report-card" 
                             data-status="{{ $report->status }}" 
                             data-month="{{ $report->work_date->format('Y-m') }}"
                             data-content="{{ strtolower($report->summary) }}">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-ojt-dark font-semibold">{{ $report->work_date->format('M d, Y') }}</h3>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $report->status === 'approved' ? 'bg-green-100 text-green-800' : ($report->status === 'returned' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ $report->status === 'approved' ? '✓ Approved' : ($report->status === 'returned' ? '↩ Returned' : '⏳ Pending') }}
                                        </span>
                                        @if($report->attachment_path)
                                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                📎 Attachment
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 line-clamp-2 mb-3">{{ $report->summary }}</p>
                                    <div class="flex items-center gap-4 text-xs text-gray-500">
                                        <span>Submitted: {{ $report->created_at->format('M d, Y g:i A') }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 ml-4">
                                    <a href="{{ route('reports.show', $report) }}" class="px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded hover:bg-blue-200 transition-colors">
                                        View
                                    </a>
                                    @if($report->status === 'submitted')
                                        <form method="POST" action="{{ route('reports.destroy', $report) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this report? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-red-100 text-red-700 text-xs rounded hover:bg-red-200 transition-colors">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">No reports yet.</div>
                    @endforelse
                    
                    <!-- Empty state for filtered results -->
                    <div class="empty-state p-8 text-center text-gray-500" style="display: none;">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No reports found</h3>
                        <p class="text-gray-500">Try adjusting your search or filter criteria.</p>
                    </div>
                </div>
            </div>
            <div class="mt-6">{{ $reports->links() }}</div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const monthFilter = document.getElementById('monthFilter');
            const reportCards = document.querySelectorAll('.report-card');

            function filterReports() {
                const searchTerm = searchInput.value.toLowerCase();
                const monthValue = monthFilter.value;

                let visibleCount = 0;

                reportCards.forEach(card => {
                    const cardContent = card.getAttribute('data-content');
                    const cardMonth = card.getAttribute('data-month');

                    const matchesSearch = searchTerm === '' || cardContent.includes(searchTerm);
                    const matchesMonth = monthValue === 'all' || cardMonth === monthValue;

                    if (matchesSearch && matchesMonth) {
                        card.style.display = 'block';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Show/hide empty state if needed
                const emptyState = document.querySelector('.empty-state');
                if (emptyState) {
                    emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
                }
            }

            searchInput.addEventListener('input', filterReports);
            monthFilter.addEventListener('change', filterReports);
        });
    </script>
</x-app-layout>


