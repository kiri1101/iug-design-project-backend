<x-mail::message>
# Password Reset Request

Click on the link below to reset you password!

<x-mail::button :url="$url">
Link
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
