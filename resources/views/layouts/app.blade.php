<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title','App')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="antialiased">
@yield('content')
</body>
</html>
