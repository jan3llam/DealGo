@component('mail::message')
    # Password email

    {{__('api.email.welcomeText')}}
    <br>
    {{__('api.email.passwordText',['password'=>$password])}}

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
