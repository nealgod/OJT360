<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Add New Company</h2>
                <p class="text-sm text-gray-500 mt-1">Add a company to your department's approved list</p>
            </div>
            <a href="{{ route('companies.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Companies
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-ojt-dark">Company Information</h3>
                    <p class="text-sm text-gray-500 mt-1">Fill in the details below to add a new company</p>
                </div>

                <form method="POST" action="{{ route('coord.companies.store') }}" class="p-6 space-y-6">
                    @csrf

                    <!-- Company Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Company Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               placeholder="e.g., ABC Corporation, XYZ Industries"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary transition-colors @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Company Address
                        </label>
                        <input type="text" 
                               id="address" 
                               name="address" 
                               value="{{ old('address') }}"
                               placeholder="e.g., 123 Business St., City, Province"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary transition-colors @error('address') border-red-500 @enderror">
                        @error('address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contact Information Section -->
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Contact Information</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Contact Person -->
                            <div>
                                <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-2">
                                    Contact Person
                                </label>
                                <input type="text" 
                                       id="contact_person" 
                                       name="contact_person" 
                                       value="{{ old('contact_person') }}"
                                       placeholder="e.g., John Doe"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary transition-colors @error('contact_person') border-red-500 @enderror">
                                @error('contact_person')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Contact Phone -->
                            <div>
                                <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Contact Phone
                                </label>
                                <input type="text" 
                                       id="contact_phone" 
                                       name="contact_phone" 
                                       value="{{ old('contact_phone') }}"
                                       placeholder="e.g., +63 912 345 6789"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary transition-colors @error('contact_phone') border-red-500 @enderror">
                                @error('contact_phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Contact Email -->
                        <div class="mt-6">
                            <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Email
                            </label>
                            <input type="email" 
                                   id="contact_email" 
                                   name="contact_email" 
                                   value="{{ old('contact_email') }}"
                                   placeholder="e.g., contact@company.com"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary transition-colors @error('contact_email') border-red-500 @enderror">
                            @error('contact_email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" 
                                name="status" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ojt-primary focus:border-ojt-primary transition-colors @error('status') border-red-500 @enderror">
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active - Available for students</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive - Not available</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">Active companies will be visible to students in your department</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('companies.index') }}" 
                           class="px-6 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-ojt-primary text-white rounded-lg text-sm font-medium hover:bg-maroon-700 transition-colors shadow-sm">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Company
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info Box -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">Note:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Companies are automatically assigned to your department ({{ $department ?? 'N/A' }})</li>
                            <li>Only active companies will be visible to students</li>
                            <li>You can edit or deactivate companies later</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
