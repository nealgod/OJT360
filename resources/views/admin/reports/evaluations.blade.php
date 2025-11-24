<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header with Back Button -->
            <div class="flex items-center gap-4 mb-6">
                <a href="{{ route('admin.reports.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-ojt-dark">Evaluations</h1>
                    <p class="text-gray-600 mt-1">View all monthly and final evaluations</p>
                </div>
            </div>

            <!-- Monthly Evaluations -->
            <div class="bg-white rounded-lg border shadow-sm p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4">Monthly Evaluations</h2>
                <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Month</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Intern</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supervisor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($monthlyEvals as $eval)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm">{{ \Carbon\Carbon::parse($eval->evaluation_month)->format('F Y') }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if($eval->student->studentProfile?->profile_image)
                                                <img src="{{ Storage::url($eval->student->studentProfile->profile_image) }}" alt="{{ $eval->student->name }}" class="w-8 h-8 rounded-full object-cover mr-2">
                                            @else
                                                <div class="w-8 h-8 {{ $eval->student->getAvatarColor() }} rounded-full flex items-center justify-center text-white text-xs font-bold mr-2">
                                                    {{ substr($eval->student->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <span class="text-sm font-medium">{{ $eval->student->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm">{{ $eval->supervisor->name }}</td>
                                    <td class="px-6 py-4">
                                        @if($eval->reviewed_at)
                                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">✓ Reviewed</span>
                                        @else
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">⏳ Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">No monthly evaluations</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Final Evaluations -->
            <div class="bg-white rounded-lg border shadow-sm p-6">
                <h2 class="text-xl font-semibold mb-4">Final Evaluations</h2>
                <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Intern</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supervisor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($finalEvals as $eval)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if($eval->student->studentProfile?->profile_image)
                                                <img src="{{ Storage::url($eval->student->studentProfile->profile_image) }}" alt="{{ $eval->student->name }}" class="w-8 h-8 rounded-full object-cover mr-2">
                                            @else
                                                <div class="w-8 h-8 {{ $eval->student->getAvatarColor() }} rounded-full flex items-center justify-center text-white text-xs font-bold mr-2">
                                                    {{ substr($eval->student->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <span class="text-sm font-medium">{{ $eval->student->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm">{{ $eval->supervisor->name }}</td>
                                    <td class="px-6 py-4">
                                        @if($eval->reviewed_at)
                                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">✓ Reviewed</span>
                                        @else
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">⏳ Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $eval->created_at->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">No final evaluations</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
