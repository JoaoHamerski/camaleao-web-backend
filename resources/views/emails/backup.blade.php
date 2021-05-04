@component('mail::layout')
  @slot('header')

  @component('mail::header', ['url' => 'https://interno.clicadrih.com/'])
  <img src="{{ url('images/icon-256.png') }}" alt="Logo da Camaleao Camisas">
  @endcomponent
@endslot

# Aviso semanal de Backup feito

Olá, este é um e-mail automático para avisar que o **Sistema Interno - Camaleão Camisas** está fazendo os backups diários às 00:00. Você pode baixar o último backup feito no botão abaixo em formato *.zip*.

@component('mail::button', ['url' => $url, 'color' => 'primary'])
BAIXAR AGORA
@endcomponent

@component('mail::subcopy')
Caso tenha algum problema ao clicar no botão de confirmação, acesse o link: <span class="break-all"><a
    href="{{ $url }}">{{ $url }}</a></span>
@endcomponent

@slot('footer')
@component('mail::footer')
Camaleão Camisas &bull; {{ Helper::date(\Carbon\Carbon::now(), '%d de %B de %Y') }}
@endcomponent
@endslot
@endcomponent