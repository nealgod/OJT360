<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Submit Daily Report</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6">
                @php
                    $todayAttendance = Auth::user()->attendanceLogs()->where('work_date', today())->first();
                @endphp
                @if($todayAttendance)
                    <div class="mb-4 rounded-lg border border-ojt-accent/30 bg-ojt-accent/5 p-3">
                        <div class="text-sm text-ojt-dark">
                            <span class="font-medium">Today's attendance:</span>
                            Time In {{ $todayAttendance->time_in_formatted }}
                            @if($todayAttendance->time_out)
                                — Time Out {{ $todayAttendance->time_out_formatted }}
                            @else
                                — Time Out: not recorded yet
                            @endif
                            — Worked {{ $todayAttendance->hours_worked_formatted }}h
                        </div>
                    </div>
                @endif
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
                        <input id="attachment" name="attachment" type="file" accept="image/*,.pdf,.doc,.docx" class="mt-1 block w-full border-gray-300 rounded-md" />
                        <div id="attachmentPreview" class="mt-3 hidden">
                            <div class="flex items-center gap-3">
                                <img id="attachmentThumb" class="hidden w-20 h-20 object-cover rounded border" />
                                <div class="text-sm text-gray-600" id="attachmentInfo"></div>
                            </div>
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('attachment')" />
                    </div>
                    <div class="flex justify-between">
                        <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            Back to Reports
                        </a>
                        <div class="flex items-center gap-3">
                            <button type="button" id="saveDraftBtn" class="text-gray-600 hover:text-ojt-primary text-sm">Save Draft</button>
                            <button type="button" id="clearDraftBtn" class="text-gray-400 hover:text-red-600 text-sm">Clear Draft</button>
                            <x-primary-button>Submit</x-primary-button>
                        </div>
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
    const saveDraftBtn = document.getElementById('saveDraftBtn');
    const clearDraftBtn = document.getElementById('clearDraftBtn');
    const storageKey = 'ojt360_report_draft_' + (document.getElementById('work_date')?.value || '{{ today()->format('Y-m-d') }}');
    const templateFlagKey = storageKey + '_template_inserted';
    
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

        // Clear draft after successful submit attempt
        try { localStorage.removeItem(storageKey); } catch (e) {}
    });

    // Load draft if present
    let loadedDraft = false;
    try {
        const draft = localStorage.getItem(storageKey);
        if (draft && !summaryTextarea.value) {
            summaryTextarea.value = draft;
            const count = draft.length;
            charCount.textContent = count;
            charCount.className = count < 50 ? 'text-red-500 font-medium' : 'text-green-600 font-medium';
            loadedDraft = true;
        }
    } catch (e) {}

    // Autosave draft
    let autosaveTimer = null;
    function queueAutosave() {
        if (autosaveTimer) clearTimeout(autosaveTimer);
        autosaveTimer = setTimeout(() => {
            try { localStorage.setItem(storageKey, summaryTextarea.value); } catch (e) {}
        }, 1000);
    }
    summaryTextarea.addEventListener('input', queueAutosave);

    // Manual save draft
    if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', function() {
            try { localStorage.setItem(storageKey, summaryTextarea.value); alert('Draft saved.'); } catch (e) { alert('Unable to save draft.'); }
        });
    }

    // Auto-insert attendance-based template if no draft/content yet (idempotent)
    (function autoInsertTemplate() {
        if (loadedDraft || summaryTextarea.value) return;
        try { if (localStorage.getItem(templateFlagKey) === '1') return; } catch (e) {}
        const base = `Today I attended my OJT from {{ $todayAttendance?->time_in_formatted ?? '—' }}{{ $todayAttendance && $todayAttendance->time_out ? ' to ' . $todayAttendance->time_out_formatted : '' }} ({{ $todayAttendance?->hours_worked_formatted ?? '0.00' }} hours).\n\nKey tasks accomplished:\n- \n- \n- \n\nLearnings/notes:\n- \n- \n`;
        @if($todayAttendance)
            // Skip if template-like content already present (defensive)
            if (!summaryTextarea.value.includes('Key tasks accomplished:') && !summaryTextarea.value.includes('Learnings/notes:')) {
                summaryTextarea.value = base;
                summaryTextarea.dispatchEvent(new Event('input'));
                try { localStorage.setItem(templateFlagKey, '1'); } catch (e) {}
            }
        @endif
    })();

    // Clear draft
    if (clearDraftBtn) {
        clearDraftBtn.addEventListener('click', function() {
            if (confirm('Clear saved draft for this date?')) {
                try { localStorage.removeItem(storageKey); localStorage.removeItem(templateFlagKey); } catch (e) {}
                summaryTextarea.value = '';
                summaryTextarea.dispatchEvent(new Event('input'));
            }
        });
    }
});
</script>
