<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('messages.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <x-user-avatar :user="$user" size="w-10 h-10" />
            <div>
                <h2 class="font-semibold text-lg text-ojt-dark">{{ $user->name }}</h2>
                <p class="text-xs text-gray-500 capitalize">{{ $user->role }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-2 sm:py-6 h-full">
        <div class="max-w-7xl mx-auto px-0 sm:px-6 lg:px-8 h-full">
            <!-- Chat Container -->
            <div class="bg-white sm:rounded-lg shadow overflow-hidden flex flex-col chat-height">
                
                <!-- Messages Area -->
                <div id="messagesContainer" class="flex-1 overflow-y-auto p-4 sm:p-6 bg-gray-50 space-y-4">
                    @forelse($messages as $message)
                        @php
                            $isMe = (int)$message->sender_id == (int)Auth::id();
                        @endphp
                        
                        <div class="flex w-full {{ $isMe ? 'justify-end' : 'justify-start' }}">
                            <div class="flex max-w-[85%] sm:max-w-[70%] gap-2 {{ $isMe ? 'flex-row-reverse' : 'flex-row' }}">
                                
                                @if(!$isMe)
                                    <div class="flex-shrink-0 self-end">
                                        <x-user-avatar :user="$message->sender" size="w-8 h-8" />
                                    </div>
                                @endif
                                
                                <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                                    <div class="px-4 py-2 rounded-2xl shadow-sm {{ $isMe ? 'bg-ojt-primary text-white' : 'bg-white text-gray-800 border border-gray-200' }}">
                                        <p class="text-sm sm:text-base break-words">{{ $message->message }}</p>
                                    </div>
                                    <span class="text-xs text-gray-500 mt-1 px-1">
                                        {{ $message->created_at->format('g:i A') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex items-center justify-center h-full text-gray-500">
                            <div class="text-center">
                                <p class="text-lg font-medium">No messages yet</p>
                                <p class="text-sm">Start the conversation!</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Input Area -->
                <div class="bg-white border-t border-gray-200 p-3 sm:p-4">
                    <form id="chatForm" action="{{ route('messages.store') }}" method="POST" class="flex gap-2 sm:gap-3 items-end">
                        @csrf
                        <input type="hidden" name="recipient_id" value="{{ $user->id }}">
                        <input type="hidden" name="subject" value="Message">
                        
                        <textarea 
                            name="message" 
                            id="messageInput"
                            rows="1"
                            data-gramm="false"
                            class="flex-1 rounded-xl border-gray-300 focus:border-ojt-primary focus:ring focus:ring-ojt-primary/20 resize-none py-3"
                            placeholder="Type a message..."
                            required></textarea>
                        
                        <button 
                            type="submit" 
                            id="sendBtn"
                            class="flex-shrink-0 bg-ojt-primary text-white p-3 rounded-xl hover:bg-maroon-700 transition-colors shadow-sm">
                            <svg class="w-6 h-6 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Mobile: Compact height with input always visible */
        .chat-height {
            height: calc(100dvh - 180px); /* More compact for mobile */
            min-height: 250px;
            max-height: calc(100dvh - 160px); /* Prevent too tall */
        }

        /* Desktop: More generous height */
        @media (min-width: 640px) {
            .chat-height {
                height: calc(100vh - 140px);
                max-height: none;
            }
        }
    </style>

    <script>
        const container = document.getElementById('messagesContainer');
        const form = document.getElementById('chatForm');
        const textarea = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendBtn');

        // Scroll to bottom on load
        container.scrollTop = container.scrollHeight;

        // Handle form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const messageContent = textarea.value.trim();
            if (!messageContent) return;

            const formData = new FormData(form);
            textarea.disabled = true;
            sendBtn.disabled = true;

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw new Error(err.message || 'Server Error'); });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Create message HTML
                    const messageHtml = `
                        <div class="flex w-full justify-end">
                            <div class="flex max-w-[85%] sm:max-w-[70%] gap-2 flex-row-reverse">
                                <div class="flex flex-col items-end">
                                    <div class="px-4 py-2 rounded-2xl shadow-sm bg-ojt-primary text-white">
                                        <p class="text-sm sm:text-base break-words">${escapeHtml(messageContent)}</p>
                                    </div>
                                    <span class="text-xs text-gray-500 mt-1 px-1">
                                        Just now
                                    </span>
                                </div>
                            </div>
                        </div>
                    `;

                    // Remove "No messages" state if exists
                    const emptyState = container.querySelector('.text-center');
                    if (emptyState) emptyState.closest('.flex').remove();

                    // Append message
                    container.insertAdjacentHTML('beforeend', messageHtml);
                    
                    // Clear input
                    textarea.value = '';
                    textarea.style.height = 'auto';
                    
                    // Scroll to bottom
                    container.scrollTop = container.scrollHeight;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Failed to send message');
            })
            .finally(() => {
                textarea.disabled = false;
                sendBtn.disabled = false;
                textarea.focus();
            });
        });

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Auto-resize textarea
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        // Submit on Enter
        textarea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                form.dispatchEvent(new Event('submit'));
            }
        });
    </script>
</x-app-layout>
