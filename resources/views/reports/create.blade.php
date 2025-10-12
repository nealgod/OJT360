<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Submit Daily Report</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6">
                <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div>
                        <x-input-label for="work_date" :value="__('Date')" />
                        <x-text-input id="work_date" name="work_date" type="date" class="mt-1 block w-full" value="{{ old('work_date', today()->format('Y-m-d')) }}" max="{{ today()->format('Y-m-d') }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('work_date')" />
                        <p class="mt-1 text-sm text-gray-500">Select the date for your daily report (cannot be in the future)</p>
                    </div>
                    <div>
                        <x-input-label for="summary" :value="__('What did you do today?')" />
                        <textarea id="summary" name="summary" rows="6" class="mt-1 block w-full border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary rounded-md" placeholder="Describe your daily activities, tasks completed, skills learned, challenges faced, and any other relevant information about your OJT experience today..." required></textarea>
                        <div class="mt-1 flex justify-between text-sm text-gray-500">
                            <span>Minimum 50 characters required</span>
                            <span id="charCount">0</span>
                        </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const summaryTextarea = document.getElementById('summary');
    const charCount = document.getElementById('charCount');
    const submitButton = document.querySelector('button[type="submit"]');
    
    // Character counting
    summaryTextarea.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = count;
        
        // Visual feedback for character count
        if (count < 50) {
            charCount.className = 'text-red-500 font-medium';
        } else {
            charCount.className = 'text-green-600 font-medium';
        }
    });
    
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const summary = summaryTextarea.value.trim();
        
        if (summary.length < 50) {
            e.preventDefault();
            alert('Please provide at least 50 characters describing your daily activities.');
            summaryTextarea.focus();
            return false;
        }
        
        // Show loading state
        submitButton.disabled = true;
        submitButton.textContent = 'Submitting...';
    });
});
</script>
