@component('mail::message')
    # Forget password email

    {{__('api.email.welcomeText')}}

    {{__('api.email.passwordText',['password'=>$password])}}

    Thanks,
    {{ config('app.name') }}
@endcomponent
