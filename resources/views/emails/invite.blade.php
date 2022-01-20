@component('mail::message')
    # Invitation email

    {{__('api.email.welcomeText',['name'=>$name])}}
    <br>
    {{__('api.email.inviteText',['password'=>$password])}}

    @component('mail::button', ['url' => 'https://spcard4u.com'])
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
