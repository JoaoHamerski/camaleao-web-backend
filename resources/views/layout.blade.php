<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title')</title>
  <link rel="stylesheet" href="{{ mix('css/app.css') }}">
  <link rel="icon" type="image/png" href="/favicon.ico"/>
  <link rel="manifest" href="/manifest.webmanifest">
  <meta name="theme-color" content="#ff4e00">
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
        <div class="d-flex flex-column justify-content-between">
          @include('sidebar')
        </div>
      @endauth
      
      <div id="content">
        <div class="container">
          @yield('content')
        </div>
      </div>
    </div>
  </div>

  <script src="{{ mix('js/app.js') }}"></script>
  @stack('script')
</body>
</html>