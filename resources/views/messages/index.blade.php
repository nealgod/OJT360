<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                {{ __('Messages') }}
            </h2>
            <a href="{{ route('messages.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white font-medium rounded-lg hover:bg-maroon-700 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                New Message
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-ojt-dark mb-2">
                    Conversations
                </h1>
                <p class="text-gray-600">
                    View your recent conversations.
                </p>
            </div>

            <!-- Conversations List -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                @if($conversations->count() > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($conversations as $conversation)
                            @php
                                $user = $conversation['user'];
                                $lastMessage = $conversation['last_message'];
                                $unreadCount = $conversation['unread_count'];
                            @endphp
                            <a href="{{ route('messages.chat', $user->id) }}" 
                               class="block hover:bg-gradient-to-r hover:from-gray-50 hover:to-white transition-all duration-200 {{ $unreadCount > 0 ? 'bg-blue-50/30' : '' }}">
                                <div class="p-4 sm:p-6 flex items-center space-x-4">
                                    <!-- Avatar -->
                                    <div class="flex-shrink-0 relative">
                                        <div class="ring-2 {{ $unreadCount > 0 ? 'ring-ojt-primary ring-offset-2' : 'ring-gray-200' }} rounded-full transition-all duration-200">
                                            <x-user-avatar :user="$user" size="w-14 h-14 sm:w-16 sm:h-16" />
                                        </div>
                                        @if($unreadCount > 0)
                                            <span class="absolute -top-1 -right-1 flex items-center justify-center min-w-[24px] h-6 bg-red-500 text-white text-xs font-bold px-2 rounded-full border-2 border-white shadow-lg animate-pulse">
                                                {{ $unreadCount }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 truncate {{ $unreadCount > 0 ? 'text-ojt-primary' : '' }}">
                                                {{ $user->name }}
                                            </h3>
                                            <span class="text-xs sm:text-sm text-gray-500 flex-shrink-0 ml-2">
                                                {{ $lastMessage->created_at->diffForHumans(null, true) }}
                                            </span>
                                        </div>
                                        
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm sm:text-base text-gray-600 truncate pr-4 {{ $unreadCount > 0 ? 'font-semibold text-gray-900' : '' }}">
                                                @if($lastMessage->sender_id === Auth::id())
                                                    <span class="text-gray-500 mr-1">You:</span>
                                                @endif
                                                {{ Str::limit($lastMessage->message, 60) }}
                                            </p>
                                            
                                            <div class="flex items-center space-x-2 flex-shrink-0">
                                                <span class="text-xs bg-gray-100 text-gray-700 px-3 py-1 rounded-full font-medium capitalize">
                                                    {{ $user->role === 'intern' ? 'Student' : $user->role }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex-shrink-0 text-gray-400 {{ $unreadCount > 0 ? 'text-ojt-primary' : '' }}">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Conversations Yet</h3>
                        <p class="text-gray-500 mb-4">
                            Start a new conversation to communicate with your coordinator or supervisor.
                        </p>
                        <a href="{{ route('messages.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white font-medium rounded-lg hover:bg-maroon-700 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Start Conversation
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
