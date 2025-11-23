@props(['evaluation'])

@php
    $status = 'pending';
    $label = 'Pending Review';
    $colorClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
    $icon = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>';
    
    if ($evaluation->reviewed_at) {
        $status = 'reviewed';
        $label = 'Reviewed';
        $colorClass = 'bg-green-100 text-green-800 border-green-200';
        $icon = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>';
    }
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm font-medium border {$colorClass}"]) }}>
    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
        {!! $icon !!}
    </svg>
    {{ $label }}
</span>
