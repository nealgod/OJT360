<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                Acceptance Letters
            </h2>
            <a href="{{ route('dashboard') }}" class="text-ojt-primary hover:text-maroon-700 text-sm font-medium">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Pending Requests</p>
                            <p class="text-2xl font-bold text-ojt-dark">{{ $pendingRequests->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Generated Letters</p>
                            <p class="text-2xl font-bold text-ojt-dark">{{ $generatedLetters->total() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">This Month</p>
                            <p class="text-2xl font-bold text-ojt-dark">{{ \App\Models\AcceptanceLetter::where('supervisor_user_id', Auth::id())->whereMonth('created_at', now()->month)->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Requests -->
            @if($pendingRequests->count() > 0)
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-ojt-dark">Pending Requests</h3>
                            <p class="text-sm text-gray-600">Students waiting for acceptance letters</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            {{ $pendingRequests->count() }} Pending
                        </span>
                    </div>

                    <div class="space-y-3">
                        @foreach($pendingRequests as $req)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-ojt-primary transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-ojt-primary rounded-full flex items-center justify-center text-white font-bold">
                                                {{ substr($req->student->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ $req->student->name }}</h4>
                                                <p class="text-sm text-gray-600">{{ $req->student->studentProfile->course ?? 'Student' }} • {{ $req->position }}</p>
                                            </div>
                                        </div>
                                        <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500">
                                            <span>Requested: {{ $req->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('supervisor.acceptance.create', $req->token) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white text-sm font-medium rounded-lg hover:bg-maroon-700 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Generate Letter
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-8 mb-8 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Pending Requests</h3>
                    <p class="text-gray-600">You don't have any pending acceptance letter requests at the moment.</p>
                </div>
            @endif

            <!-- Expired Requests -->
            @if($expiredRequests->count() > 0)
                <div class="bg-white rounded-lg border border-red-200 shadow-sm p-6 mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-red-800">Expired Requests</h3>
                            <p class="text-sm text-gray-600">These requests have expired and need to be resent</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            {{ $expiredRequests->count() }} Expired
                        </span>
                    </div>

                    <div class="space-y-3">
                        @foreach($expiredRequests as $req)
                            <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-red-200 rounded-full flex items-center justify-center text-red-800 font-bold">
                                                {{ substr($req->student->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ $req->student->name }}</h4>
                                                <p class="text-sm text-gray-600">{{ $req->student->studentProfile->course ?? 'Student' }} • {{ $req->position }}</p>
                                            </div>
                                        </div>
                                        <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500">
                                            <span>Requested: {{ $req->created_at->diffForHumans() }}</span>
                                            <span class="text-red-600 font-medium">Expired: {{ $req->expires_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('supervisor.acceptance.resend', $req->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            Resend Link
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Generated Letters -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-ojt-dark">Generated Letters</h3>
                        <p class="text-sm text-gray-600">All acceptance letters you've created</p>
                    </div>
                </div>

                @if($generatedLetters->count() > 0)
                    <div class="space-y-3">
                        @foreach($generatedLetters as $letter)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ $letter->student->name }}</h4>
                                                <p class="text-sm text-gray-600">{{ $letter->job_title }} • {{ $letter->company->name }}</p>
                                            </div>
                                        </div>
                                        <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500">
                                            <span>Generated: {{ $letter->created_at->format('M d, Y g:i A') }}</span>
                                            <span>Document ID: {{ $letter->document_id }}</span>
                                            <span>Effective: {{ $letter->start_date->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('acceptance-letters.download', $letter) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Download
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $generatedLetters->links() }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Letters Generated Yet</h3>
                        <p class="text-gray-600">You haven't generated any acceptance letters yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
