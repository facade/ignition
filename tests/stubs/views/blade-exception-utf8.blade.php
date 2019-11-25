<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>
</head>
<body>
Wäre es nicht schön, wenn die Tür schließt?
@php
    throw new Exception
@endphp

<ul>
    @foreach ([1, 2, 3] as $i => $test)
        <li>{{ $test }}</li>
    @endforeach
</ul>
</body>
</html>
