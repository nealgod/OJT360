<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Notify Acceptance</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-8">
                <h1 class="text-2xl font-bold text-ojt-dark mb-6">I’ve been accepted</h1>

                <form method="POST" action="{{ route('placements.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="company_id" :value="__('Company (choose listed or leave blank)')" />
                        <select id="company_id" name="company_id" class="mt-1 block w-full border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary rounded-md shadow-sm" onchange="handleCompanySelection()">
                            <option value="">Not listed here</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('company_id')" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6" id="external-company-fields">
                        <div>
                            <x-input-label for="external_company_name" :value="__('External Company Name (if not listed)')" />
                            <x-text-input id="external_company_name" name="external_company_name" type="text" class="mt-1 block w-full" :value="old('external_company_name')" />
                            <x-input-error class="mt-2" :messages="$errors->get('external_company_name')" />
                        </div>
                        <div>
                            <x-input-label for="external_company_address" :value="__('External Company Address')" />
                            <x-text-input id="external_company_address" name="external_company_address" type="text" class="mt-1 block w-full" :value="old('external_company_address')" />
                            <x-input-error class="mt-2" :messages="$errors->get('external_company_address')" />
                        </div>
                    </div>

                    <!-- Position/Role (always visible) -->
                    <div class="sm:col-span-2">
                        <x-input-label for="position_title" :value="__('Position / Role (optional)')" />
                        <x-text-input id="position_title" name="position_title" type="text" class="mt-1 block w-full" :value="old('position_title')" placeholder="Enter position or leave blank" />
                        <x-input-error class="mt-2" :messages="$errors->get('position_title')" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="start_date" :value="__('Start Date')" />
                            <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('start_date')" />
                        </div>
                        <div>
                            <x-input-label for="break_minutes" :value="__('Break Time (minutes)')" />
                            <x-text-input id="break_minutes" name="break_minutes" type="number" min="0" max="240" class="mt-1 block w-full" :value="old('break_minutes', 60)" />
                            <x-input-error class="mt-2" :messages="$errors->get('break_minutes')" />
                        </div>
                    </div>

                    

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="contact_person" :value="__('Company Contact Person')" />
                            <x-text-input id="contact_person" name="contact_person" type="text" class="mt-1 block w-full" :value="old('contact_person')" required />
                            <p class="mt-1 text-xs text-gray-500">Name of the person at the company who accepted your internship (HR, manager, etc.)</p>
                            <x-input-error class="mt-2" :messages="$errors->get('contact_person')" />
                        </div>
                    </div>

                    <!-- Optional Schedule Fields -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="shift_start" :value="__('Shift Start (optional)')" />
                            <x-text-input id="shift_start" name="shift_start" type="time" class="mt-1 block w-full" :value="old('shift_start')" />
                            <x-input-error class="mt-2" :messages="$errors->get('shift_start')" />
                        </div>
                        <div>
                            <x-input-label for="shift_end" :value="__('Shift End (optional)')" />
                            <x-text-input id="shift_end" name="shift_end" type="time" class="mt-1 block w-full" :value="old('shift_end')" />
                            <x-input-error class="mt-2" :messages="$errors->get('shift_end')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label :value="__('Work Days (optional)')" />
                        <div class="mt-2 grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <label class="inline-flex items-center space-x-2">
                                <input type="checkbox" name="working_days[]" value="mon" class="rounded border-gray-300 text-ojt-primary focus:ring-ojt-primary" {{ in_array('mon', old('working_days', [])) ? 'checked' : '' }}>
                                <span>Mon</span>
                            </label>
                            <label class="inline-flex items-center space-x-2">
                                <input type="checkbox" name="working_days[]" value="tue" class="rounded border-gray-300 text-ojt-primary focus:ring-ojt-primary" {{ in_array('tue', old('working_days', [])) ? 'checked' : '' }}>
                                <span>Tue</span>
                            </label>
                            <label class="inline-flex items-center space-x-2">
                                <input type="checkbox" name="working_days[]" value="wed" class="rounded border-gray-300 text-ojt-primary focus:ring-ojt-primary" {{ in_array('wed', old('working_days', [])) ? 'checked' : '' }}>
                                <span>Wed</span>
                            </label>
                            <label class="inline-flex items-center space-x-2">
                                <input type="checkbox" name="working_days[]" value="thu" class="rounded border-gray-300 text-ojt-primary focus:ring-ojt-primary" {{ in_array('thu', old('working_days', [])) ? 'checked' : '' }}>
                                <span>Thu</span>
                            </label>
                            <label class="inline-flex items-center space-x-2">
                                <input type="checkbox" name="working_days[]" value="fri" class="rounded border-gray-300 text-ojt-primary focus:ring-ojt-primary" {{ in_array('fri', old('working_days', [])) ? 'checked' : '' }}>
                                <span>Fri</span>
                            </label>
                            <label class="inline-flex items-center space-x-2">
                                <input type="checkbox" name="working_days[]" value="sat" class="rounded border-gray-300 text-ojt-primary focus:ring-ojt-primary" {{ in_array('sat', old('working_days', [])) ? 'checked' : '' }}>
                                <span>Sat</span>
                            </label>
                            <label class="inline-flex items-center space-x-2">
                                <input type="checkbox" name="working_days[]" value="sun" class="rounded border-gray-300 text-ojt-primary focus:ring-ojt-primary" {{ in_array('sun', old('working_days', [])) ? 'checked' : '' }}>
                                <span>Sun</span>
                            </label>
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('working_days')" />
                    </div>

                    <!-- Supervisor Information (Optional) -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Supervisor Information (Optional)</h3>
                        <p class="text-sm text-gray-600 mb-4">If you know your supervisor's details, please provide them. If not, leave blank and your coordinator will contact you later for this information.</p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="supervisor_name" :value="__('Supervisor Name (if known)')" />
                                <x-text-input id="supervisor_name" name="supervisor_name" type="text" class="mt-1 block w-full" :value="old('supervisor_name')" />
                                <x-input-error class="mt-2" :messages="$errors->get('supervisor_name')" />
                            </div>
                            <div>
                                <x-input-label for="supervisor_email" :value="__('Supervisor Email (if known)')" />
                                <x-text-input id="supervisor_email" name="supervisor_email" type="email" class="mt-1 block w-full" :value="old('supervisor_email')" />
                                <x-input-error class="mt-2" :messages="$errors->get('supervisor_email')" />
                            </div>
                        </div>
                    </div>


                    <div>
                        <x-input-label for="note" :value="__('Notes (optional)')" />
                        <textarea id="note" name="note" rows="4" class="mt-1 block w-full border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary rounded-md shadow-sm">{{ old('note') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('note')" />
                    </div>

                    <div>
                        <x-input-label for="proof" :value="__('Upload Proof of Acceptance')" />
                        <div class="mt-1">
                            <input id="proof" name="proof" type="file" 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-ojt-primary file:text-white hover:file:bg-maroon-700 file:cursor-pointer border border-gray-300 rounded-md cursor-pointer" 
                                   accept=".jpg,.jpeg,.png,.pdf" />
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('proof')" />
                        <p class="mt-1 text-xs text-gray-500">
                            Upload acceptance letter, email screenshot, or any proof of your internship acceptance. (JPG, PNG, PDF - Max 4MB)
                        </p>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button onclick="validateForm()">Submit</x-primary-button>
                        <a href="{{ route('placements.index') }}" class="text-gray-600 hover:text-ojt-primary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Company data for auto-filling
        const companies = @json($companies->keyBy('id'));
        
        function handleCompanySelection() {
            const companySelect = document.getElementById('company_id');
            const externalFields = document.getElementById('external-company-fields');
            const externalNameField = document.getElementById('external_company_name');
            const externalAddressField = document.getElementById('external_company_address');
            
            if (companySelect.value) {
                // Hide external company fields when a listed company is selected
                externalFields.style.display = 'none';
                externalNameField.value = '';
                externalAddressField.value = '';
                externalNameField.required = false;
                externalAddressField.required = false;
                
                // Auto-fill company details (if available in the companies data)
                const selectedCompany = companies[companySelect.value];
                if (selectedCompany) {
                    // You can auto-fill other fields here if needed
                    // For example, if you want to pre-fill contact person or other details
                }
            } else {
                // Show external company fields when no company is selected
                externalFields.style.display = 'block';
                externalNameField.required = true;
                externalAddressField.required = true;
            }
        }
        
        function validateForm() {
            const companySelect = document.getElementById('company_id');
            const externalNameField = document.getElementById('external_company_name');
            const externalFields = document.getElementById('external-company-fields');
            
            // If no company is selected, external company name is required
            if (!companySelect.value && !externalNameField.value.trim()) {
                alert('Please either select a company from the list or enter an external company name.');
                externalFields.style.display = 'block';
                externalNameField.focus();
                return false;
            }
            
            // If company is selected, ensure external fields are hidden and empty
            if (companySelect.value) {
                externalFields.style.display = 'none';
                externalNameField.value = '';
                document.getElementById('external_company_address').value = '';
            }
            
            return true;
        }
        
        // Initialize form validation on page load
        document.addEventListener('DOMContentLoaded', function() {
            handleCompanySelection();
        });
    </script>
</x-app-layout>


