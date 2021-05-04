@component('mail::layout')
  @slot('header')

  @component('mail::header', ['url' => 'https://interno.clicadrih.com/'])
  <img src="{{ url('images/icon-256.png') }}" alt="Logo da Camaleao Camisas">
  @endcomponent
@endslot

# Aviso semanal de Backup

Este é um e-mail automático para avisar que backups diários estão sendo feitos no **Sistema Interno - Camaleão Camisas** às **19h30min**. 

Você sempre pode baixar o último backup feito no botão abaixo em formato **.zip**.

@component('mail::button', ['url' => $url, 'color' => 'primary'])
BAIXAR AGORA
@endcomponent

**Obs.:** O backup baixado é sempre o mais recente, independente da data do e-mail, este e-mail tem como unico propósito de lembrete.

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