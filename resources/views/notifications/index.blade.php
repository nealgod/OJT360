<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
            {{ __('Messages') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-ojt-dark mb-2">
                    @if(Auth::user()->isStudent())
                        Messages to Coordinator
                    @elseif(Auth::user()->isCoordinator())
                        Student Messages
                    @else
                        All Messages
                    @endif
                </h1>
                <p class="text-gray-600">
                    @if(Auth::user()->isStudent())
                        View messages you've sent to your coordinator.
                    @elseif(Auth::user()->isCoordinator())
                        View messages from students in your department.
                    @else
                        View all system messages.
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
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        @if($notification->type === 'ojt_acceptance') OJT Acceptance
                                        @elseif($notification->type === 'ojt_concern') OJT Concern
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
                            
                            <div class="flex items-center space-x-2 ml-4">
                                @if(!$notification->read)
                                    <form method="POST" action="{{ route('notifications.read', $notification) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                            Mark as Read
                                        </button>
                                    </form>
                                @endif
                                
                                @if(Auth::user()->isCoordinator() && $notification->type === 'ojt_acceptance')
                                    <button class="text-sm text-ojt-primary hover:text-maroon-700 font-medium">
                                        Approve OJT
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Messages</h3>
                        <p class="text-gray-500 mb-4">
                            @if(Auth::user()->isStudent())
                                You haven't sent any messages to your coordinator yet.
                            @elseif(Auth::user()->isCoordinator())
                                No messages from students yet.
                            @else
                                No messages in the system.
                            @endif
                        </p>
                        @if(Auth::user()->isStudent())
                            <a href="{{ route('notifications.create') }}" 
                               class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white font-medium rounded-lg hover:bg-maroon-700 transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Send Message
                            </a>
                        @endif
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
