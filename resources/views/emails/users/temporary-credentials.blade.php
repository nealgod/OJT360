@component('mail::message')
# Welcome to OJT360

Hi {{ $user->name }},

An administrator created an account for you. Please verify your email address and sign in using the temporary password below. You can change your password after logging in.

@component('mail::panel')
Email: {{ $user->email }}  
Temporary Password: {{ $temporaryPassword }}
@endcomponent

@component('mail::button', ['url' => route('verification.notice')])
Verify Your Email
@endcomponent

If the button doesn't work, log in at {{ config('app.url') }} and follow the verification prompt.

Thanks,
{{ config('app.name') }}
@endcomponent


