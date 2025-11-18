<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                {{ __('Edit Application Letter') }}
            </h2>
            <a href="{{ route('student-documents.index') }}" class="text-ojt-primary hover:text-maroon-700">
                ← Back to Documents
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('student-documents.application-letter.update', $letter) }}" class="space-y-6">
                @csrf
                @method('PATCH')

                <!-- Student Information (Read-only) -->
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-ojt-dark mb-4">Your Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <p class="text-gray-900 font-medium">{{ $user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <p class="text-gray-900">{{ $user->email }}</p>
                        </div>
                        @if($studentProfile && $studentProfile->phone)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <p class="text-gray-900">{{ $studentProfile->phone }}</p>
                        </div>
                        @endif
                        @if($studentProfile && $studentProfile->department)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                            <p class="text-gray-900">{{ $studentProfile->department }}</p>
                        </div>
                        @endif
                        @if($studentProfile && $studentProfile->course)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Program</label>
                            <p class="text-gray-900">{{ $studentProfile->course }}</p>
                        </div>
                        @endif
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Created</label>
                            <p class="text-gray-900">{{ $letter->created_at->format('F d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Letter Content -->
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-ojt-dark mb-4">Letter Content</h3>
                    
                    <div class="mb-4">
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                            Edit your application letter *
                        </label>
                        <textarea 
                            id="content" 
                            name="content" 
                            rows="15" 
                            required
                            class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary"
                        >{{ old('content', $letter->content) }}</textarea>
                        @error('content')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-2">
                            Minimum 50 characters. The letter will automatically include "Sincerely yours," followed by your name at the end.
                        </p>
                    </div>

                    <!-- Preview Section -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mt-6">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Letter Preview</h4>
                        <div class="text-sm text-gray-600 space-y-2">
                            <p class="text-right">{{ $letter->created_at->format('F d, Y') }}</p>
                            <div class="mt-4">
                                <p>{{ $user->name }}</p>
                                <p>{{ $user->email }}</p>
                                @if($studentProfile && $studentProfile->phone)
                                <p>{{ $studentProfile->phone }}</p>
                                @endif
                                @if($studentProfile && $studentProfile->department)
                                <p>{{ $studentProfile->department }}</p>
                                @endif
                                @if($studentProfile && $studentProfile->course)
                                <p>{{ $studentProfile->course }}</p>
                                @endif
                            </div>
                            <div class="mt-6 mb-6">
                                <p class="font-semibold text-center text-gray-900">APPLICATION LETTER</p>
                            </div>
                            <p class="whitespace-pre-wrap">{{ $letter->content }}</p>
                            <div class="mt-6">
                                <p>Sincerely yours,</p>
                                <p class="mt-4 font-semibold">{{ $user->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('student-documents.index') }}" class="text-gray-600 hover:text-gray-900">
                            Cancel
                        </a>
                        <button type="submit" class="bg-ojt-primary text-white px-6 py-2 rounded-lg hover:bg-maroon-700 transition-colors">
                            Update Application Letter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Live preview for application letter
        document.addEventListener('DOMContentLoaded', function() {
            const contentTextarea = document.getElementById('content');
            const previewContent = document.querySelector('.whitespace-pre-wrap');
            
            if (contentTextarea && previewContent) {
                contentTextarea.addEventListener('input', function() {
                    const text = this.value.trim();
                    if (text) {
                        previewContent.textContent = text;
                    }
                });
            }
        });
    </script>
</x-app-layout>
