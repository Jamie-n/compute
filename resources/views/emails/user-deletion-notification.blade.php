@component('mail::message')
Dear {{$user->name}},

As part of our requirement to comply with GDPR, we are required to delete your data after 2 years of inactivity.

You last accessed your account on: {{$user->last_login_at->format('d/m/Y')}}

Your account will be deactivated in 1 month, to prevent this, please login to your account.

If you wish for your data to be deleted, please disregard this message.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
