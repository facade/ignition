<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>
</head>
<body>
àààààààà
{{ throw new Exception }}
àààààààà
<ul>
    @foreach ([1, 2, 3] as $i => $test)
        <li>{{ $test }}</li>
    @endforeach
</ul>
</body>
</html>
