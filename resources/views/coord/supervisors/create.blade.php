<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Add Supervisor</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-ojt-dark">Invite Supervisor</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Send a registration link to supervisors from
                        {{ $programName ? $programName . ' ' : '' }}companies so they can create their own account.
                    </p>
                </div>

                <form method="POST" action="{{ route('coord.supervisors.store') }}" class="p-6 space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="email" :value="__('Supervisor Email Address')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start space-x-3">
                                <svg class="h-5 w-5 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="text-sm text-blue-800 space-y-2">
                                    <p>We’ll email a registration link that expires in 24 hours. Supervisors will confirm their email, set a password, and enter their company details.</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-700">
                            <p class="font-medium text-ojt-dark mb-1">Need to resend?</p>
                            <p>If the supervisor can’t find the invite, just submit the same email again and we’ll refresh the link.</p>
                        </div>
                    </div>

                    @if(session('error'))
                        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('coord.supervisors.index') }}" 
                           class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-ojt-primary text-white rounded-lg hover:bg-maroon-700 transition-colors duration-200">
                            Send Invitation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
