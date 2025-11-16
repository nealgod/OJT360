<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                {{ __('New Message') }}
            </h2>
            <a href="{{ route('messages.index') }}" class="text-ojt-primary hover:text-maroon-700">
                ← Back to Messages
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-ojt-dark mb-2">Send New Message</h1>
                <p class="text-gray-600">
                    @if(Auth::user()->isStudent())
                        Send a message to your coordinator or supervisor.
                    @elseif(Auth::user()->isCoordinator())
                        Send a message to students in your department.
                    @else
                        Send a message to any user.
                    @endif
                </p>
            </div>

            <!-- Message Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <form method="POST" action="{{ route('messages.store') }}" class="p-6">
                    @csrf
                    
                    <!-- Recipient Selection -->
                    <div class="mb-6">
                        <x-input-label for="recipient_id" :value="__('To')" />
                        
                        @if(isset($selectedRecipient) && $selectedRecipient)
                            {{-- Show selected recipient (for replies) --}}
                            <div class="mt-1 flex items-center space-x-3 p-3 bg-gray-50 border border-gray-300 rounded-md">
                                <x-user-avatar :user="$selectedRecipient" size="w-10 h-10" />
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $selectedRecipient->name }}</p>
                                    <p class="text-sm text-gray-500">
                                        @if($selectedRecipient->isCoordinator())
                                            Coordinator - {{ $selectedRecipient->coordinatorProfile?->department }}
                                        @elseif($selectedRecipient->isSupervisor())
                                            Supervisor
                                        @elseif($selectedRecipient->isStudent())
                                            Student - {{ $selectedRecipient->studentProfile?->course }}
                                        @endif
                                    </p>
                                </div>
                                <a href="{{ route('messages.create') }}" class="text-sm text-ojt-primary hover:text-maroon-700">
                                    Change
                                </a>
                            </div>
                            <input type="hidden" name="recipient_id" value="{{ $selectedRecipient->id }}">
                        @else
                            {{-- Show dropdown for new messages --}}
                            <select id="recipient_id" name="recipient_id" 
                                    class="mt-1 block w-full border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary rounded-md shadow-sm"
                                    required>
                                <option value="">Select recipient...</option>
                                @foreach($recipients as $recipient)
                                    <option value="{{ $recipient->id }}" {{ old('recipient_id') == $recipient->id ? 'selected' : '' }}>
                                        {{ $recipient->name }} 
                                        @if($recipient->isCoordinator())
                                            (Coordinator - {{ $recipient->coordinatorProfile?->department }})
                                        @elseif($recipient->isSupervisor())
                                            (Supervisor)
                                        @elseif($recipient->isStudent())
                                            (Student - {{ $recipient->studentProfile?->course }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        <x-input-error :messages="$errors->get('recipient_id')" class="mt-2" />
                    </div>

                    <!-- Subject -->
                    <div class="mb-6">
                        <x-input-label for="subject" :value="__('Subject')" />
                        <x-text-input id="subject" name="subject" type="text" 
                                      class="mt-1 block w-full" 
                                      :value="old('subject', $prefilledSubject ?? '')" 
                                      placeholder="e.g., Document Submission, General Inquiry, Reports, etc."
                                      maxlength="255"
                                      required />
                        <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                    </div>

                    <!-- Message Content -->
                    <div class="mb-6">
                        <x-input-label for="message" :value="__('Message')" />
                        <textarea id="message" name="message" rows="8" 
                                  class="mt-1 block w-full border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary rounded-md shadow-sm"
                                  placeholder="Type your message here..."
                                  maxlength="2000"
                                  required>{{ old('message') }}</textarea>
                        <x-input-error :messages="$errors->get('message')" class="mt-2" />
                        <div class="mt-1 flex justify-between text-sm text-gray-500">
                            <span>Maximum 2000 characters</span>
                            <span id="char-count">0 / 2000</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('messages.index') }}" 
                           class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-ojt-primary text-white font-medium rounded-lg hover:bg-maroon-700 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Send Message
                        </button>
                    </div>
                </form>
            </div>

            <!-- Help Section -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="text-lg font-medium text-blue-900 mb-2">Message Guidelines</h3>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• Be clear and concise in your message</li>
                            <li>• Use appropriate subject lines for easy reference</li>
                            <li>• Include relevant details about your OJT or concerns</li>
                            <li>• Be respectful and professional in your communication</li>
                            
                            @if(Auth::user()->isStudent())
                                <li>• Message your coordinator for document submissions, requirements, and general OJT guidance</li>
                                <li>• Message your supervisor for acceptance letters, daily work concerns, and workplace questions</li>
                                <li>• Include your student ID and course when discussing academic matters</li>
                            @elseif(Auth::user()->isCoordinator())
                                <li>• Message students for document reviews, requirement clarifications, and OJT guidance</li>
                                <li>• Provide clear feedback on document submissions and requirements</li>
                                <li>• Use messages to track student progress and address concerns promptly</li>
                            @elseif(Auth::user()->isSupervisor())
                                <li>• Message your supervised students for work-related updates and feedback</li>
                                <li>• Message coordinators for student-related concerns or documentation needs</li>
                                <li>• Provide timely responses to acceptance letter requests and student inquiries</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Character counter for message textarea
        const messageTextarea = document.getElementById('message');
        const charCount = document.getElementById('char-count');
        
        function updateCharCount() {
            const length = messageTextarea.value.length;
            charCount.textContent = `${length} / 2000`;
            
            // Change color when approaching limit
            if (length > 1900) {
                charCount.classList.add('text-red-600', 'font-medium');
            } else if (length > 1700) {
                charCount.classList.add('text-yellow-600', 'font-medium');
            } else {
                charCount.classList.remove('text-red-600', 'text-yellow-600', 'font-medium');
            }
        }
        
        // Update on input
        messageTextarea.addEventListener('input', updateCharCount);
        
        // Initial count
        updateCharCount();
    </script>
</x-app-layout>
