@component('mail::message')
    # Activation email

    {{__('api.email.welcomeText')}}

    {{__('api.email.activationText',['password'=>$password])}}

    Thanks,
    {{ config('app.name') }}
@endcomponent
