<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Add Supervisor</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-ojt-dark">Create New Supervisor</h3>
                    <p class="text-sm text-gray-600 mt-1">Add a company supervisor to manage students during their OJT.</p>
                </div>

                <form method="POST" action="{{ route('coord.supervisors.store') }}" class="p-6 space-y-6">
                    @csrf

                    <!-- Personal Information -->
                    <div class="space-y-4">
                        <h4 class="text-md font-medium text-gray-900">Personal Information</h4>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Full Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>
                            <div>
                                <x-input-label for="email" :value="__('Email Address')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>
                        </div>
                    </div>

                    <!-- Company Assignment -->
                    <div class="space-y-4">
                        <h4 class="text-md font-medium text-gray-900">Company Assignment</h4>
                        
                        <div>
                            <x-input-label for="company_id" :value="__('Company')" />
                            <select id="company_id" name="company_id" class="mt-1 block w-full border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary rounded-md shadow-sm" required>
                                <option value="">Select a company</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('company_id')" />
                        </div>
                    </div>

                    <!-- Professional Information -->
                    <div class="space-y-4">
                        <h4 class="text-md font-medium text-gray-900">Professional Information</h4>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="employee_id" :value="__('Employee ID')" />
                                <x-text-input id="employee_id" name="employee_id" type="text" class="mt-1 block w-full" :value="old('employee_id')" />
                                <p class="text-xs text-gray-500 mt-1">Leave blank to auto-generate</p>
                                <x-input-error class="mt-2" :messages="$errors->get('employee_id')" />
                            </div>
                            <div>
                                <x-input-label for="position" :value="__('Position/Title')" />
                                <x-text-input id="position" name="position" type="text" class="mt-1 block w-full" :value="old('position')" />
                                <x-input-error class="mt-2" :messages="$errors->get('position')" />
                            </div>
                        </div>
                        
                        <div>
                            <x-input-label for="phone" :value="__('Phone Number')" />
                            <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" :value="old('phone')" />
                            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                        </div>
                    </div>

                    <!-- Information Notice -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Account Creation</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>A temporary password will be generated. The supervisor will be notified via email and required to change their password on first login.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('coord.supervisors.index') }}" 
                           class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-ojt-primary text-white rounded-lg hover:bg-maroon-700 transition-colors duration-200">
                            Create Supervisor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
