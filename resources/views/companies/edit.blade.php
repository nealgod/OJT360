<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Edit Company</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                <form method="POST" action="{{ route('coord.companies.update', $company) }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Company Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $company->name)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label :value="__('Department (fixed)')" />
                        <div class="mt-1 block w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 text-gray-700">{{ $department ?? '—' }}</div>
                    </div>

                    <div>
                        <x-input-label for="address" :value="__('Address')" />
                        <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $company->address)" />
                        <x-input-error class="mt-2" :messages="$errors->get('address')" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="contact_person" :value="__('Contact Person')" />
                            <x-text-input id="contact_person" name="contact_person" type="text" class="mt-1 block w-full" :value="old('contact_person', $company->contact_person)" />
                            <x-input-error class="mt-2" :messages="$errors->get('contact_person')" />
                        </div>
                        <div>
                            <x-input-label for="contact_email" :value="__('Contact Email')" />
                            <x-text-input id="contact_email" name="contact_email" type="email" class="mt-1 block w-full" :value="old('contact_email', $company->contact_email)" />
                            <x-input-error class="mt-2" :messages="$errors->get('contact_email')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="contact_phone" :value="__('Contact Phone')" />
                        <x-text-input id="contact_phone" name="contact_phone" type="text" class="mt-1 block w-full" :value="old('contact_phone', $company->contact_phone)" />
                        <x-input-error class="mt-2" :messages="$errors->get('contact_phone')" />
                    </div>

                    <div>
                        <x-input-label for="status" :value="__('Status')" />
                        <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary rounded-md shadow-sm">
                            <option value="active" {{ old('status', $company->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $company->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('status')" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>Update</x-primary-button>
                        <a href="{{ route('companies.index') }}" class="text-gray-600 hover:text-ojt-primary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>


