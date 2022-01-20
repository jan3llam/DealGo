@component('mail::message')
    # Invitation email

    {{__('api.email.welcomeText')}}
    <br>
    {{__('api.email.passwordText',['password'=>$password])}}

    @component('mail::button', ['url' => 'https://spcard4u.com/admin'])
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
