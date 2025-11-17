<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Daily Report Details</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-ojt-dark">{{ $report->work_date->format('l, M d, Y') }}</h1>
                    <p class="text-gray-600">Daily Report Details</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('reports.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                        Back to Reports
                    </a>
                    @if($report->status === 'submitted')
                        <form method="POST" action="{{ route('reports.destroy', $report) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this report? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                                Delete Report
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <!-- Report Header -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Report Status</h3>
                                <p class="text-sm text-gray-600">Submitted on {{ $report->created_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $report->status === 'approved' ? 'bg-green-100 text-green-800' : ($report->status === 'returned' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $report->status === 'approved' ? '✓ Approved' : ($report->status === 'returned' ? '↩ Returned' : '⏳ Pending') }}
                            </span>
                            @if($report->attachment_path)
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    📎 Has Attachment
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Report Content -->
                <div class="px-6 py-6">
                    <div class="space-y-6">
                        <!-- Work Date -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Work Date</h4>
                            <p class="text-lg text-gray-700">{{ $report->work_date->format('l, F d, Y') }}</p>
                        </div>

                        <!-- Attendance (if available) -->
                        @if($attendance)
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Attendance</h4>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <p class="text-sm text-gray-700">
                                        <span class="font-medium">In:</span> {{ $attendance->time_in_formatted }} • 
                                        <span class="font-medium">Out:</span> {{ $attendance->time_out_formatted ?? 'Not recorded' }} • 
                                        <span class="font-medium">{{ $attendance->hours_worked_formatted }} hrs</span>
                                    </p>
                                </div>
                            </div>
                        @endif

                        <!-- Report Summary -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Daily Activities Summary</h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $report->summary }}</p>
                            </div>
                        </div>

                        <!-- Attachment -->
                        @if($report->attachment_path)
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Attachment</h4>
                                <div class="border border-gray-200 rounded-lg p-4 space-y-4">
                                    @php
                                        $extension = strtolower(pathinfo($report->attachment_path, PATHINFO_EXTENSION));
                                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    @endphp

                                    <div class="flex flex-wrap gap-3">
                                        <a href="{{ Storage::url($report->attachment_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white text-sm rounded-lg hover:bg-maroon-700 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Open Attachment
                                        </a>
                                        <a href="{{ Storage::url($report->attachment_path) }}" download class="inline-flex items-center px-4 py-2 bg-white border border-ojt-primary text-ojt-primary text-sm rounded-lg hover:bg-ojt-primary hover:text-white transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v16h16V4M12 8v8m4-4H8" />
                                            </svg>
                                            Download File
                                        </a>
                                    </div>

                                    @if($isImage)
                                        <div>
                                            <p class="text-xs uppercase tracking-wide text-gray-500 mb-2">Inline Preview</p>
                                            <img src="{{ Storage::url($report->attachment_path) }}" alt="Report attachment" class="rounded-lg border border-gray-200 max-h-[500px] object-contain w-full bg-white">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Report Metadata -->
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Report Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-700">Submitted:</span>
                                    <span class="text-gray-600">{{ $report->created_at->format('M d, Y g:i A') }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Status:</span>
                                    <span class="text-gray-600">{{ ucfirst($report->status) }}</span>
                                </div>
                                @if($report->attachment_path)
                                    <div>
                                        <span class="font-medium text-gray-700">Attachment:</span>
                                        <span class="text-gray-600">{{ basename($report->attachment_path) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
