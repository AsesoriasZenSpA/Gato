<x-mail::message>
# Introduction

The body of your message.

@php
$url = url("/rooms/{$room->uuid}/invited?token={$room->token}");
@endphp

<x-mail::button :url="$url">
Accept
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
