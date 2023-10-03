<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>MYAPI GATEWAY</title>
    {{-- vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

</head>

<body>
    <div>
        <p>
            Welcome to MYAPI GATEWAY
        </p>
        <p>by mujabdev</p>
        <p>Featur v.03.10.2023</p>
        <ul>
            <li>
                <a href="http://myapigateway.test/api/login">/login</a>
            </li>
            <li>
                <a href="http://myapigateway.test/api/register">/register</a>

            </li>
            <li>
                <a href="http://myapigateway.test/api/blogs">/blogs</a>

            </li>
            <li>
                <a href="http://myapigateway.test/api/logout">/logout</a>

            </li>
        </ul>
    </div>
</body>

</html>
