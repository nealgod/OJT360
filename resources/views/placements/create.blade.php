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
                        <select id="company_id" name="company_id" class="mt-1 block w-full border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary rounded-md shadow-sm">
                            <option value="">Not listed here</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('company_id')" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="external_company_name" :value="__('External Company Name (if not listed)')" />
                            <x-text-input id="external_company_name" name="external_company_name" type="text" class="mt-1 block w-full" :value="old('external_company_name')" />
                            <x-input-error class="mt-2" :messages="$errors->get('external_company_name')" />
                        </div>
                        <div>
                            <x-input-label for="external_company_address" :value="__('External Company Address (optional)')" />
                            <x-text-input id="external_company_address" name="external_company_address" type="text" class="mt-1 block w-full" :value="old('external_company_address')" />
                            <x-input-error class="mt-2" :messages="$errors->get('external_company_address')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="start_date" :value="__('Start Date (optional)')" />
                            <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date')" />
                            <x-input-error class="mt-2" :messages="$errors->get('start_date')" />
                        </div>
                        <div>
                            <x-input-label for="contact_person" :value="__('Contact Person (optional)')" />
                            <x-text-input id="contact_person" name="contact_person" type="text" class="mt-1 block w-full" :value="old('contact_person')" />
                            <x-input-error class="mt-2" :messages="$errors->get('contact_person')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="supervisor_name" :value="__('Supervisor Name (optional)')" />
                            <x-text-input id="supervisor_name" name="supervisor_name" type="text" class="mt-1 block w-full" :value="old('supervisor_name')" />
                            <x-input-error class="mt-2" :messages="$errors->get('supervisor_name')" />
                        </div>
                        <div>
                            <x-input-label for="supervisor_email" :value="__('Supervisor Email (optional)')" />
                            <x-text-input id="supervisor_email" name="supervisor_email" type="email" class="mt-1 block w-full" :value="old('supervisor_email')" />
                            <x-input-error class="mt-2" :messages="$errors->get('supervisor_email')" />
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
                        <x-primary-button>Submit</x-primary-button>
                        <a href="{{ route('placements.index') }}" class="text-gray-600 hover:text-ojt-primary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>


