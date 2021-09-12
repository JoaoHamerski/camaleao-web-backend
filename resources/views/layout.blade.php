<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <link rel="icon" type="image/png" href="/favicon.ico" />
    <link rel="manifest" href="/manifest.webmanifest">
    @stack('css')
</head>

<body class="@yield('body-class')">
    <div id="app">
        @auth
        <div id="btnSidebar"
            class="hamburger hamburger-squeeze js-hamburger {{ Cookie::get('sidebar_active') ? 'is-active' : '' }}">
            <div class="hamburger-box">
                <div class="hamburger-inner"></div>
            </div>
        </div>
        @endauth

        <div class="wrapper-app">
            @auth
            @include('sidebar')
            @endauth

            <div id="content">
                <div class="container">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</body>

</html>
