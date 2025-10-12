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

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="start_date" :value="__('Start Date')" />
                            <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('start_date')" />
                        </div>
                        <div>
                            <x-input-label for="contact_person" :value="__('Contact Person')" />
                            <x-text-input id="contact_person" name="contact_person" type="text" class="mt-1 block w-full" :value="old('contact_person')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('contact_person')" />
                        </div>
                    </div>


                    <div>
                        <x-input-label for="note" :value="__('Notes (optional)')" />
                        <textarea id="note" name="note" rows="4" class="mt-1 block w-full border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary rounded-md shadow-sm">{{ old('note') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('note')" />
                    </div>

                    <div>
                        <x-input-label for="proof" :value="__('Upload Proof (optional: jpg, png, pdf)')" />
                        <input id="proof" name="proof" type="file" class="mt-1 block w-full border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary rounded-md shadow-sm" />
                        <x-input-error class="mt-2" :messages="$errors->get('proof')" />
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


