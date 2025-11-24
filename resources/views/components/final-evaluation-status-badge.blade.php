@props(['evaluation'])

@php
    $statusColors = [
        'draft' => 'bg-yellow-100 text-yellow-800',
        'submitted' => 'bg-blue-100 text-blue-800',
        'reviewed' => 'bg-green-100 text-green-800',
    ];
    
    $color = $statusColors[$evaluation->status] ?? 'bg-gray-100 text-gray-800';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {$color}"]) }}>
    {{ ucfirst($evaluation->status) }}
</span>
