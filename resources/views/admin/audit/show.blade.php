<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.audit.index') }}" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Audit Log Details</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Audit Log Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <!-- Header -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Audit Log #{{ $audit->id }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $audit->created_at->format('F d, Y \a\t g:i A') }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            @if($audit->action === 'created') bg-green-100 text-green-800
                            @elseif($audit->action === 'updated') bg-blue-100 text-blue-800
                            @elseif($audit->action === 'deleted') bg-red-100 text-red-800
                            @elseif($audit->action === 'login' || $audit->action === 'logout') bg-purple-100 text-purple-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($audit->action) }}
                        </span>
                    </div>
                </div>

                <!-- Content -->
                <div class="px-6 py-6 space-y-6">
                    <!-- User Information -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">User Information</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">User</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $audit->user?->name ?? 'System' }}</p>
                                </div>
                                @if($audit->user)
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Role</p>
                                        <p class="text-sm font-medium text-gray-900">{{ ucfirst($audit->user->role) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Email</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $audit->user->email }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Action Details -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Action Details</h4>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Description</p>
                                <p class="text-sm text-gray-900">{{ $audit->description }}</p>
                            </div>
                            @if($audit->model_type)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Model Type</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $audit->model_type }}</p>
                                    </div>
                                    @if($audit->model_id)
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Model ID</p>
                                            <p class="text-sm font-medium text-gray-900">{{ $audit->model_id }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Changes (if updated) -->
                    @if($audit->action === 'updated' && ($audit->old_values || $audit->new_values))
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Changes</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($audit->old_values)
                                    <div>
                                        <p class="text-xs text-gray-500 mb-2 font-medium">Old Values</p>
                                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                            <pre class="text-xs text-gray-800 whitespace-pre-wrap">{{ json_encode($audit->old_values, JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                    </div>
                                @endif
                                @if($audit->new_values)
                                    <div>
                                        <p class="text-xs text-gray-500 mb-2 font-medium">New Values</p>
                                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                            <pre class="text-xs text-gray-800 whitespace-pre-wrap">{{ json_encode($audit->new_values, JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Technical Details -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Technical Details</h4>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">IP Address</p>
                                    <code class="text-sm bg-white px-2 py-1 rounded border border-gray-200">{{ $audit->ip_address ?? 'N/A' }}</code>
                                </div>
                                @if($audit->user_agent)
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">User Agent</p>
                                        <p class="text-xs text-gray-700 break-all">{{ $audit->user_agent }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Related Logs (if applicable) -->
                    @if($relatedLogs->isNotEmpty())
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Related Activity</h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-xs text-gray-500 mb-3">Other changes to this {{ class_basename($audit->model_type) }}:</p>
                                <div class="space-y-2">
                                    @foreach($relatedLogs as $relatedLog)
                                        <div class="bg-white rounded border border-gray-200 p-3 hover:border-ojt-primary transition-colors cursor-pointer" onclick="window.location='{{ route('admin.audit.show', $relatedLog) }}'">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="px-2 py-0.5 rounded text-xs font-medium
                                                            @if($relatedLog->action === 'created') bg-green-100 text-green-800
                                                            @elseif($relatedLog->action === 'updated') bg-blue-100 text-blue-800
                                                            @elseif($relatedLog->action === 'deleted') bg-red-100 text-red-800
                                                            @else bg-gray-100 text-gray-800
                                                            @endif">
                                                            {{ ucfirst($relatedLog->action) }}
                                                        </span>
                                                        <span class="text-xs text-gray-500">{{ $relatedLog->created_at->format('M d, Y H:i') }}</span>
                                                    </div>
                                                    <p class="text-sm text-gray-700">{{ \Illuminate\Support\Str::limit($relatedLog->description, 80) }}</p>
                                                </div>
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('admin.audit.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            ← Back to Audit Logs
                        </a>
                        <span class="text-xs text-gray-500">Log ID: {{ $audit->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

