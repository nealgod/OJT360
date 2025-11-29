<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <a href="{{ route('messages.index') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div class="flex items-center space-x-3">
                    <x-user-avatar :user="$user" size="w-10 h-10" />
                    <div>
                        <h2 class="font-semibold text-lg text-ojt-dark leading-tight">
                            {{ $user->name }}
                        </h2>
                        <p class="text-xs text-gray-500 capitalize">{{ $user->role }}</p>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="flex flex-col h-[calc(100vh-10rem)] min-h-[500px]">
        <!-- Messages Area -->
        <div class="flex-1 overflow-y-auto bg-gradient-to-b from-gray-50 to-white" id="messagesContainer">
            <div class="max-w-5xl mx-auto p-4 sm:p-6 space-y-4">
                @forelse($messages as $message)
                    @php
                        $isMe = $message->sender_id === Auth::id();
                    @endphp
                    <div class="flex w-full {{ $isMe ? 'justify-end' : 'justify-start' }} animate-fade-in">
                        <div class="flex max-w-[85%] sm:max-w-[75%] {{ $isMe ? 'flex-row-reverse space-x-reverse' : 'flex-row' }} space-x-2 sm:space-x-3">
                            <!-- Avatar (only for other user) -->
                            @if(!$isMe)
                                <div class="flex-shrink-0 self-end mb-1">
                                    <x-user-avatar :user="$message->sender" size="w-8 h-8 sm:w-10 sm:h-10" />
                                </div>
                            @endif

                            <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }} space-y-1">
                                <!-- Message Bubble -->
                                <div class="group relative px-4 py-3 rounded-2xl shadow-sm transition-all duration-200 {{ $isMe ? 'bg-ojt-primary text-white rounded-br-md hover:shadow-md' : 'bg-white text-gray-800 border border-gray-200 rounded-bl-md hover:shadow-md hover:border-gray-300' }}">
                                    <p class="text-sm sm:text-base leading-relaxed whitespace-pre-wrap break-words">{{ $message->message }}</p>
                                </div>
                                
                                <!-- Timestamp and Status -->
                                <div class="flex items-center space-x-2 px-2">
                                    <span class="text-xs text-gray-500">
                                        {{ $message->created_at->format('g:i A') }}
                                    </span>
                                    @if($isMe && $message->is_read)
                                        <span class="text-xs text-green-600 font-medium flex items-center space-x-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>Seen</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center text-gray-500 space-y-3">
                            <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <p class="text-lg font-medium">No messages yet.</p>
                            <p class="text-sm">Start the conversation!</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Input Area -->
        <div class="bg-white border-t border-gray-200 shadow-lg">
            <div class="max-w-5xl mx-auto p-4 sm:p-6">
                <form id="chatForm" action="{{ route('messages.store') }}" method="POST" class="flex items-end space-x-3 sm:space-x-4">
                    @csrf
                    <input type="hidden" name="recipient_id" value="{{ $user->id }}">
                    <input type="hidden" name="subject" value="Message">
                    
                    <div class="flex-1">
                        <textarea name="message" rows="1" 
                                  class="w-full border-2 border-gray-300 focus:border-ojt-primary focus:ring-2 focus:ring-ojt-primary/20 rounded-2xl shadow-sm resize-none py-3 px-4 text-sm sm:text-base transition-all duration-200"
                                  placeholder="Type your message..."
                                  required
                                  oninput="this.style.height = ''; this.style.height = Math.min(this.scrollHeight, 120) + 'px'"></textarea>
                    </div>
                    
                    <button type="submit" id="sendBtn"
                            class="inline-flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 bg-ojt-primary text-white font-medium rounded-2xl hover:bg-maroon-700 active:scale-95 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const container = document.getElementById('messagesContainer');
        const form = document.getElementById('chatForm');
        const textarea = form.querySelector('textarea');
        const sendBtn = document.getElementById('sendBtn');

        // Auto-scroll to bottom on load
        function scrollToBottom() {
            container.scrollTop = container.scrollHeight;
        }
        scrollToBottom();
        
        // Focus textarea
        textarea.focus();

        // Handle form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const messageContent = textarea.value.trim();
            if (!messageContent) return;

            // Create FormData BEFORE disabling (disabled fields are excluded from FormData)
            const formData = new FormData(form);

            // Disable UI AFTER creating FormData
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
                    // Append new message
                    appendMessage(data.message);
                    
                    // Reset form
                    form.reset();
                    textarea.style.height = 'auto';
                    scrollToBottom();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Failed to send message. Please try again.');
            })
            .finally(() => {
                // Re-enable UI
                textarea.disabled = false;
                sendBtn.disabled = false;
                textarea.focus();
            });
        });

        function appendMessage(message) {
            const time = new Date(message.created_at).toLocaleTimeString([], {hour: 'numeric', minute:'2-digit'});
            
            const html = `
                <div class="flex w-full justify-end animate-fade-in">
                    <div class="flex max-w-[85%] sm:max-w-[75%] flex-row-reverse space-x-reverse space-x-2 sm:space-x-3">
                        <div class="flex flex-col items-end space-y-1">
                            <div class="group relative px-4 py-3 rounded-2xl shadow-sm transition-all duration-200 bg-ojt-primary text-white rounded-br-md hover:shadow-md">
                                <p class="text-sm sm:text-base leading-relaxed whitespace-pre-wrap break-words">${escapeHtml(message.message)}</p>
                            </div>
                            <div class="flex items-center space-x-2 px-2">
                                <span class="text-xs text-gray-500">
                                    ${time}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove "No messages" placeholder if it exists
            const emptyState = container.querySelector('.text-center.text-gray-500');
            if (emptyState) {
                emptyState.parentElement.remove();
            }

            container.querySelector('.max-w-5xl').insertAdjacentHTML('beforeend', html);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Submit on Enter (without Shift)
        textarea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                form.dispatchEvent(new Event('submit'));
            }
        });
    </script>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
</x-app-layout>
