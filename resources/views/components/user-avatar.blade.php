@props(['user', 'size' => 'w-10 h-10'])

@php
    $profileImage = $user->profile_image;
    $initials = strtoupper(substr($user->name, 0, 1));
    $colors = ['bg-red-500', 'bg-blue-500', 'bg-green-500', 'bg-yellow-500', 'bg-purple-500', 'bg-pink-500', 'bg-indigo-500'];
    $colorIndex = ord($initials) % count($colors);
    $avatarColor = $colors[$colorIndex];
@endphp

@if($profileImage)
    <img src="{{ $profileImage }}" alt="{{ $user->name }}" class="{{ $size }} rounded-full object-cover border-2 border-white shadow-sm">
@else
    <div class="{{ $size }} {{ $avatarColor }} rounded-full flex items-center justify-center text-white font-bold shadow-sm">
        {{ $initials }}
    </div>
@endif
