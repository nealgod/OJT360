<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
            {{ __('Notifications') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-ojt-dark mb-2">
                    @if(Auth::user()->isStudent())
                        My Notifications
                    @elseif(Auth::user()->isCoordinator())
                        System Notifications
                    @else
                        All Notifications
                    @endif
                </h1>
                <p class="text-gray-600">
                    @if(Auth::user()->isStudent())
                        View your document reviews, placement updates, and system notifications.
                    @elseif(Auth::user()->isCoordinator())
                        View system notifications and updates.
                    @else
                        View all system notifications.
                    @endif
                </p>
            </div>

            <!-- Messages List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                @forelse($notifications as $notification)
                    <div class="p-6 border-b border-gray-200 last:border-b-0 {{ !$notification->read ? 'bg-blue-50' : '' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h3 class="text-lg font-semibold text-ojt-dark">
                                        {{ $notification->title }}
                                    </h3>
                                    @if(!$notification->read)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            New
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                        @if($notification->type === 'ojt_acceptance') bg-green-100 text-green-800
                                        @elseif($notification->type === 'ojt_concern') bg-red-100 text-red-800
                                        @elseif($notification->type === 'document_reviewed') bg-blue-100 text-blue-800
                                        @elseif($notification->type === 'document_submitted') bg-orange-100 text-orange-800
                                        @elseif($notification->type === 'pre_placement_complete') bg-green-100 text-green-800
                                        @elseif($notification->type === 'placement_request') bg-purple-100 text-purple-800
                                        @elseif($notification->type === 'placement_decision') bg-green-100 text-green-800
                                        @elseif($notification->type === 'acceptance_letter_request') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        @if($notification->type === 'ojt_acceptance') OJT Acceptance
                                        @elseif($notification->type === 'ojt_concern') OJT Concern
                                        @elseif($notification->type === 'document_reviewed') Document Review
                                        @elseif($notification->type === 'document_submitted') Document Submission
                                        @elseif($notification->type === 'pre_placement_complete') Pre-Placement Complete
                                        @elseif($notification->type === 'placement_request') Placement Request
                                        @elseif($notification->type === 'placement_decision') Placement Decision
                                        @elseif($notification->type === 'acceptance_letter_request') Acceptance Letter
                                        @else General Inquiry
                                        @endif
                                    </span>
                                </div>
                                
                                <p class="text-gray-600 mb-3">{{ $notification->message }}</p>
                                
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $notification->created_at->format('M d, Y \a\t g:i A') }}
                                </div>
                            </div>
                            
                            <div class="flex flex-col items-end space-y-2 ml-4">
                                <div class="flex flex-wrap gap-2 justify-end">
                                    @if(!$notification->read)
                                        <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-ojt-primary rounded-full hover:bg-maroon-700 transition-colors">
                                                Mark as read
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('notifications.unread', $notification) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-700 border border-gray-300 rounded-full hover:bg-gray-50 transition-colors">
                                                Mark as unread
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('notifications.destroy', $notification) }}" onsubmit="return confirm('Delete this notification?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 border border-red-200 rounded-full hover:bg-red-50 transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                                @if($notification->type === 'acceptance_letter_request')
                                    <a href="{{ route('supervisor.acceptance.index') }}" class="text-xs text-ojt-primary hover:text-maroon-700 font-medium">
                                        View request
                                    </a>
                                @endif
                                
                                @if(Auth::user()->isCoordinator() && $notification->type === 'ojt_acceptance')
                                    <a href="{{ route('coord.students.index') }}" class="text-xs text-ojt-primary hover:text-maroon-700 font-medium">
                                        Review student
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Notifications</h3>
                        <p class="text-gray-500">
                            @if(Auth::user()->isStudent())
                                You don't have any notifications yet. You'll receive notifications when your documents are reviewed or when there are important updates.
                            @elseif(Auth::user()->isCoordinator())
                                No notifications yet.
                            @else
                                No notifications in the system.
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
