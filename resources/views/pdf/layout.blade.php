<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>@yield('title') - Camaleão Web</title>
  @include('pdf.styles')

  @stack('styles')
</head>
<body>
  {{-- Footer aqui por compatibilidade na renderização do PDF --}}
  @include('pdf.footer')

  @include('pdf.header', [
    'title' => $title ?? '',
    'subtitle' => $subtitle ?? ''
  ])

  <div class="content">
    @yield('content')
  </div>
</body>
</html>
