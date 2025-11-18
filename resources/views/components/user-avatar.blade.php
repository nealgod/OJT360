@props(['user', 'size' => 'w-10 h-10'])

@php
    $profileImage = $user->profile_image;
    $initials = strtoupper(substr($user->name, 0, 1));
    $avatarColor = $user->getAvatarColor();
@endphp

@if($profileImage)
    <img src="{{ $profileImage }}" alt="{{ $user->name }}" class="{{ $size }} rounded-full object-cover border-2 border-white shadow-sm">
@else
    <div class="{{ $size }} {{ $avatarColor }} rounded-full flex items-center justify-center text-white font-bold shadow-sm">
        {{ $initials }}
    </div>
@endif
