<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
            {{ __('Notify Coordinator') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-ojt-dark mb-2">Contact Your OJT Coordinator</h1>
                    <p class="text-gray-600">Send a message to your coordinator about your OJT status or any concerns.</p>
                </div>

                <form method="POST" action="{{ route('notifications.store') }}" class="space-y-6">
                    @csrf

                    <!-- Notification Type -->
                    <div>
                        <x-input-label for="type" :value="__('Message Type')" />
                        <select id="type" name="type" class="mt-1 block w-full border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary rounded-md shadow-sm" required>
                            <option value="">Select message type</option>
                            <option value="ojt_acceptance">OJT Acceptance & Start Date</option>
                            <option value="ojt_concern">OJT Concern/Issue</option>
                            <option value="general">General Inquiry</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('type')" />
                    </div>

                    <!-- Title -->
                    <div>
                        <x-input-label for="title" :value="__('Subject')" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" 
                            :value="old('title')" required autofocus placeholder="Brief subject of your message" />
                        <x-input-error class="mt-2" :messages="$errors->get('title')" />
                    </div>

                    <!-- Message -->
                    <div>
                        <x-input-label for="message" :value="__('Message')" />
                        <textarea id="message" name="message" rows="6" class="mt-1 block w-full border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary rounded-md shadow-sm" 
                            required placeholder="Describe your OJT status, concerns, or any information your coordinator should know...">{{ old('message') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('message')" />
                    </div>

                    <!-- Additional Data (Hidden) -->
                    <input type="hidden" name="data" value="{}">

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('dashboard') }}" 
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                            Cancel
                        </a>
                        <x-primary-button class="bg-ojt-primary hover:bg-maroon-700">
                            {{ __('Send Message') }}
                        </x-primary-button>
                    </div>
                </form>

                <!-- Help Text -->
                <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                    <h3 class="text-sm font-medium text-blue-900 mb-2">Message Types:</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li><strong>OJT Acceptance & Start Date:</strong> Notify when you've been accepted and your start date</li>
                        <li><strong>OJT Concern/Issue:</strong> Report any problems during OJT</li>
                        <li><strong>General Inquiry:</strong> Ask questions or request assistance</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
