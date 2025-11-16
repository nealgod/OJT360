<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
            {{ __('Request OJT Acceptance Letter') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('documents.show', \App\Models\DocumentRequirement::where('name', 'Letter of Acceptance')->first()) }}" 
                   class="text-ojt-primary hover:text-maroon-700 text-sm font-medium">
                    ← Back to Letter of Acceptance
                </a>
            </div>

            @if($pendingRequest)
                <!-- Pending Request Notice -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-yellow-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-yellow-900 mb-2">Pending Request</h3>
                            <p class="text-sm text-yellow-800 mb-3">
                                You have a pending acceptance letter request sent to <strong>{{ $pendingRequest->supervisor_name }}</strong> 
                                ({{ $pendingRequest->supervisor_email }}) at {{ $pendingRequest->company_name }}.
                            </p>
                            <div class="text-xs text-yellow-700 space-y-1">
                                <p>• Sent: {{ $pendingRequest->created_at->format('M d, Y g:i A') }}</p>
                                <p>• Expires: {{ $pendingRequest->expires_at->format('M d, Y g:i A') }}</p>
                                <p>• Status: Waiting for supervisor response</p>
                                <p>• Supervisor Email: <strong>{{ $pendingRequest->supervisor_email }}</strong></p>
                            </div>
                            <div class="mt-3 text-xs text-yellow-700">
                                <p class="italic">If your supervisor hasn't received the email, please ask them to check their spam folder or you can cancel this request and create a new one.</p>
                            </div>
                            <form method="POST" action="{{ route('acceptance.request.cancel', $pendingRequest) }}" class="mt-4">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium"
                                        onclick="return confirm('Cancel this request? You can create a new one after cancelling.')">
                                    Cancel Request
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <!-- Request Form -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-ojt-dark mb-4">Request Acceptance Letter</h3>
                    
                    <!-- Instructions -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <h4 class="text-sm font-medium text-blue-900 mb-2">How it works:</h4>
                        <ol class="text-sm text-blue-800 space-y-1 list-decimal list-inside">
                            <li>Fill out the form below with your supervisor's information</li>
                            <li>Your supervisor will receive an email with a secure link</li>
                            <li>They will create an account (one-time setup)</li>
                            <li>They will fill out the acceptance letter details</li>
                            <li>The letter will be automatically generated and sent to your documents</li>
                        </ol>
                    </div>

                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                            <ul class="text-sm text-red-800 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('acceptance.request.store') }}" class="space-y-6">
                        @csrf

                        <!-- Company Name -->
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Company Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="company_name" 
                                   name="company_name" 
                                   value="{{ old('company_name') }}"
                                   required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-ojt-primary focus:border-ojt-primary"
                                   placeholder="e.g., TechStart Inc">
                        </div>

                        <!-- Supervisor Name -->
                        <div>
                            <label for="supervisor_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Supervisor Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="supervisor_name" 
                                   name="supervisor_name" 
                                   value="{{ old('supervisor_name') }}"
                                   required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-ojt-primary focus:border-ojt-primary"
                                   placeholder="Lastname, Firstname Middlename">
                            <p class="mt-1 text-xs text-gray-500">The person who accepted you for the internship</p>
                        </div>

                        <!-- Supervisor Email -->
                        <div>
                            <label for="supervisor_email" class="block text-sm font-medium text-gray-700 mb-2">
                                Supervisor Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="supervisor_email" 
                                   name="supervisor_email" 
                                   value="{{ old('supervisor_email') }}"
                                   required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-ojt-primary focus:border-ojt-primary"
                                   placeholder="e.g., john.doe@techstart.com">
                            <p class="mt-1 text-xs text-gray-500">They will receive the request at this email</p>
                        </div>

                        <!-- Position -->
                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 mb-2">
                                Position/Role <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="position" 
                                   name="position" 
                                   value="{{ old('position') }}"
                                   required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-ojt-primary focus:border-ojt-primary"
                                   placeholder="e.g., Web Developer Intern">
                            <p class="mt-1 text-xs text-gray-500">The position you were accepted for</p>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end space-x-3 pt-4">
                            <button type="submit" 
                                    class="px-6 py-2 text-sm font-medium text-white bg-ojt-primary rounded-lg hover:bg-maroon-700">
                                Send Request to Supervisor
                            </button>
                        </div>
                    </form>
                </div>


            @endif

        </div>
    </div>
</x-app-layout>
