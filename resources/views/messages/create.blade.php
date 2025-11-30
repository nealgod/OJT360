<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                {{ __('New Conversation') }}
            </h2>
            <a href="{{ route('messages.index') }}" class="text-ojt-primary hover:text-maroon-700">
                ← Back to Messages
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-ojt-dark mb-2">Start a Conversation</h1>
                <p class="text-gray-600">
                    Select a person to start chatting with.
                </p>
            </div>

            <!-- Contacts List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                @if($recipients->count() > 0)
                    <div class="divide-y divide-gray-200">
                        @foreach($recipients as $recipient)
                            <a href="{{ route('messages.chat', $recipient->id) }}" class="block hover:bg-gray-50 transition-colors duration-150">
                                <div class="p-4 sm:p-6 flex items-center space-x-4">
                                    <x-user-avatar :user="$recipient" size="w-12 h-12" />
                                    
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ $recipient->name }}
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            @if($recipient->isCoordinator())
                                                Coordinator - {{ $recipient->coordinatorProfile?->department }}
                                            @elseif($recipient->isSupervisor())
                                                Supervisor
                                            @elseif($recipient->isStudent())
                                                Student - {{ $recipient->studentProfile?->course }}
                                            @endif
                                        </p>
                                    </div>
                                    
                                    <div class="flex-shrink-0 text-ojt-primary">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Contacts Available</h3>
                        <p class="text-gray-500 mb-4">
                            You don't have any assigned contacts to message yet.
                        </p>
                        
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-left max-w-lg mx-auto text-sm text-yellow-800">
                            <p class="font-bold mb-2">Why is this list empty?</p>
                            <ul class="list-disc list-inside space-y-1">
                                @if(Auth::user()->isStudent())
                                    <li><strong>Coordinator:</strong> No coordinator found in your department ({{ $userDepartment ?? 'No Department Assigned' }}). Please check if your department matches your coordinator's department exactly.</li>
                                    <li><strong>Supervisor:</strong> You have not been assigned a supervisor yet, or your supervisor has not generated your Acceptance Letter.</li>
                                @elseif(Auth::user()->isCoordinator())
                                    <li><strong>Students:</strong> No students found in your department ({{ $userDepartment ?? 'No Department Assigned' }}).</li>
                                    <li><strong>Supervisors:</strong> No supervisors are currently handling your students.</li>
                                @elseif(Auth::user()->isSupervisor())
                                    <li><strong>Students:</strong> You have not generated an Acceptance Letter for any student yet. Please go to "My Students", search for a student, and generate their letter to link them.</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
