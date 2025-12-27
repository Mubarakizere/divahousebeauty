@component('mail::message')
# Hello!

{!! nl2br(e($body)) !!}

Thanks,  
{{ config('app.name') }}
@endcomponent
