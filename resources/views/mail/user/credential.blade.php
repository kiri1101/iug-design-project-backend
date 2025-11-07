<x-mail::message>
# User Credentials

Hi {{ $user->first_name }} {{ $user->last_name }},

Your default account credentials are as shown below:

pseudo: {{ $user->email }}<br>
password: {{ $defaultPwd }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
