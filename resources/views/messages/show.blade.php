<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                {{ __('Message Details') }}
            </h2>
            <a href="{{ route('messages.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Messages
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Message Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <!-- Message Header -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold text-ojt-dark mb-2">{{ $message->subject }}</h1>
                            
                            <div class="flex items-center space-x-6 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    @if($message->sender_id === Auth::id())
                                        To: {{ $message->recipient->name }}
                                    @else
                                        From: {{ $message->sender->name }}
                                    @endif
                                </div>
                                
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $message->created_at->format('M d, Y \a\t g:i A') }}
                                </div>
                                
                                @if($message->is_read && $message->read_at)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Read {{ $message->read_at->format('M d, Y \a\t g:i A') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                @if($message->sender_id === Auth::id()) bg-green-100 text-green-800
                                @else bg-blue-100 text-blue-800
                                @endif">
                                @if($message->sender_id === Auth::id()) Sent
                                @else Received
                                @endif
                            </span>
                            
                            @if(!$message->is_read && $message->recipient_id === Auth::id())
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    Unread
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Message Content -->
                <div class="p-6">
                    <div class="prose max-w-none">
                        <div class="whitespace-pre-wrap text-gray-700 leading-relaxed">{{ $message->message }}</div>
                    </div>
                </div>

                <!-- Message Actions -->
                <div class="p-6 border-t border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            @if($message->recipient_id === Auth::id())
                                @if(!$message->is_read)
                                    <form method="POST" action="{{ route('messages.read', $message) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Mark as Read
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('messages.unread', $message) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Mark as Unread
                                        </button>
                                    </form>
                                @endif
                            @endif
                            
                            <a href="{{ route('messages.create') }}?reply_to={{ $message->sender_id }}" 
                               class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white font-medium rounded-lg hover:bg-maroon-700 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                </svg>
                                Reply
                            </a>
                        </div>
                        
                        <form method="POST" action="{{ route('messages.destroy', $message) }}" class="inline" 
                              onsubmit="return confirm('Are you sure you want to delete this message?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- User Information -->
            <div class="mt-8 bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Message Participants</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Sender Info -->
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-ojt-primary rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr($message->sender->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $message->sender->name }}</h4>
                            <p class="text-sm text-gray-600 capitalize">{{ $message->sender->role }}</p>
                            @if($message->sender->isCoordinator())
                                <p class="text-xs text-gray-500">{{ $message->sender->coordinatorProfile?->department }}</p>
                            @elseif($message->sender->isStudent())
                                <p class="text-xs text-gray-500">{{ $message->sender->studentProfile?->course }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Recipient Info -->
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gray-600 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr($message->recipient->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $message->recipient->name }}</h4>
                            <p class="text-sm text-gray-600 capitalize">{{ $message->recipient->role }}</p>
                            @if($message->recipient->isCoordinator())
                                <p class="text-xs text-gray-500">{{ $message->recipient->coordinatorProfile?->department }}</p>
                            @elseif($message->recipient->isStudent())
                                <p class="text-xs text-gray-500">{{ $message->recipient->studentProfile?->course }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
