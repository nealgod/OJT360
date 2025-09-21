<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Submit Daily Report</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div>
                        <x-input-label for="work_date" :value="__('Date')" />
                        <x-text-input id="work_date" name="work_date" type="date" class="mt-1 block w-full" required />
                        <x-input-error class="mt-2" :messages="$errors->get('work_date')" />
                    </div>
                    <div>
                        <x-input-label for="summary" :value="__('What did you do today?')" />
                        <textarea id="summary" name="summary" rows="6" class="mt-1 block w-full border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary rounded-md" required></textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('summary')" />
                    </div>
                    <div>
                        <x-input-label for="attachment" :value="__('Attachment (optional)')" />
                        <input id="attachment" name="attachment" type="file" class="mt-1 block w-full border-gray-300 rounded-md" />
                        <x-input-error class="mt-2" :messages="$errors->get('attachment')" />
                    </div>
                    <div class="flex items-center gap-4">
                        <x-primary-button>Submit</x-primary-button>
                        <a href="{{ route('reports.index') }}" class="text-gray-600 hover:text-ojt-primary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>


