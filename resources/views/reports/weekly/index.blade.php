<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                    Weekly Reports
                </h2>
                <p class="text-sm text-gray-500">Track and download your weekly accomplishment reports.</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="document.getElementById('weekPickerModal').classList.remove('hidden')"
                   class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white rounded-lg shadow hover:bg-maroon-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Weekly Report
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 border border-green-100 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('info'))
                <div class="mb-4 rounded-md bg-blue-50 border border-blue-100 px-4 py-3 text-blue-800">
                    {{ session('info') }}
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6">
                @if ($reports->isEmpty())
                    <p class="text-gray-500">No weekly reports created yet.</p>
                @else
                    @php
                        $draftCount = $reports->where('status', 'draft')->count();
                    @endphp
                    @if($draftCount > 0)
                        <div class="mb-4 rounded-md bg-yellow-50 border border-yellow-100 px-4 py-3 text-yellow-800">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p class="text-sm">You have {{ $draftCount }} draft {{ $draftCount === 1 ? 'report' : 'reports' }}. Don't forget to submit them to your coordinator!</p>
                            </div>
                        </div>
                    @endif
                    <div class="space-y-4">
                        @foreach ($reports as $report)
                            <div class="border rounded-lg p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4 hover:border-ojt-primary/50 transition">
                                <div>
                                    <p class="text-sm text-gray-500">Week {{ $report->week_number }}</p>
                                    <h3 class="text-lg font-semibold text-ojt-dark">
                                        {{ $report->week_start_date->format('M d') }} - {{ $report->week_end_date->format('M d, Y') }}
                                    </h3>
                                    <div class="mt-2 flex flex-wrap gap-4 text-sm text-gray-600">
                                        <span>Present: <strong>{{ $report->days_present }}</strong></span>
                                        <span>Hours: <strong>{{ number_format($report->total_hours, 2) }}</strong></span>
                                        <span>Status: 
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                                                @if($report->status === 'reviewed') bg-green-100 text-green-800
                                                @elseif($report->status === 'submitted') bg-blue-100 text-blue-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ ucfirst($report->status) }}
                                            </span>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('reports.weekly.show', $report) }}"
                                       class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        View Report
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $reports->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Date Range Picker Modal -->
    <div id="weekPickerModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Select Date Range</h3>
                <form action="{{ route('reports.weekly.create') }}" method="GET" id="dateRangeForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="week_start_date" id="startDate" required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-ojt-primary focus:ring-ojt-primary"
                               value="{{ now()->toDateString() }}"
                               onchange="updateEndDateMin()">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" name="week_end_date" id="endDate" required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-ojt-primary focus:ring-ojt-primary"
                               value="{{ now()->toDateString() }}"
                               onchange="validateDateRange()">
                        <p class="text-xs text-gray-500 mt-1">Maximum 7 days range</p>
                        <p id="dateRangeError" class="text-xs text-red-500 mt-1 hidden"></p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="submit" id="submitBtn" class="flex-1 px-4 py-2 bg-ojt-primary text-white rounded-lg hover:bg-maroon-700">
                            Continue
                        </button>
                        <button type="button" onclick="closeModal()"
                                class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateEndDateMin() {
            const startDate = document.getElementById('startDate').value;
            const endDateInput = document.getElementById('endDate');
            endDateInput.min = startDate;
            
            // Auto-set end date to start date if it's before start date
            if (endDateInput.value < startDate) {
                endDateInput.value = startDate;
            }
            validateDateRange();
        }

        function validateDateRange() {
            const startDate = new Date(document.getElementById('startDate').value);
            const endDate = new Date(document.getElementById('endDate').value);
            const errorMsg = document.getElementById('dateRangeError');
            const submitBtn = document.getElementById('submitBtn');
            
            if (endDate < startDate) {
                errorMsg.textContent = 'End date must be after or equal to start date';
                errorMsg.classList.remove('hidden');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                return false;
            }
            
            const diffTime = Math.abs(endDate - startDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays > 6) {
                errorMsg.textContent = 'Date range cannot exceed 7 days';
                errorMsg.classList.remove('hidden');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                return false;
            }
            
            errorMsg.classList.add('hidden');
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            return true;
        }

        function closeModal() {
            document.getElementById('weekPickerModal').classList.add('hidden');
            // Reset form
            document.getElementById('dateRangeForm').reset();
            document.getElementById('dateRangeError').classList.add('hidden');
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateEndDateMin();
        });
    </script>
</x-app-layout>



