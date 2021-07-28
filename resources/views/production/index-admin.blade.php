@extends('layout')

@section('title', 'Setor de produção - Listagem')

@section('content')
<x-card class="mt-4" header-color="primary" icon="fas fa-box">
  <x-slot name="header">
    Pedidos confirmados pela produção
  </x-slot>

  <x-slot name="body">
    <nav class="nav nav-pills flex-column flex-sm-row mb-4">
      <a @class([
          'flex-sm-fill text-sm-center nav-link',
          'active font-weight-bold' => request('filtro') !== 'pendentes'
        ]) 
        href="{{ route('production.indexAdmin') }}"
      >Confirmados</a>

      <a @class([
          'flex-sm-fill text-sm-center nav-link',
          'active font-weight-bold' => request('filtro') === 'pendentes'
        ]) 
        href="{{ route('production.indexAdmin', ['filtro' => 'pendentes']) }}"
      >Pendentes</a>
    </nav>

    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>USUÁRIO</th>
            <th>PEDIDO</th>
            <th>COMISSÃO</th>
            <th class="text-center">CONFIRMAÇÃO</th>
          </tr>
        </thead>
        <tbody>
          @foreach($commissions as $commission)
            <tr>
              <td>
                {{ $commission->user->name }} 
                <small class="text-secondary">({{ $commission->user->role->name }})</small>
              </td>
              <td>
                <a class="font-weight-bold" 
                  target="_blank" 
                  href="{{ $commission->commission->order->path() }}"
                >
                  {{ $commission->commission->order->code }}
                </a>
              </td>
              <td>{{ Mask::money($commission->commission_value)  }}</td>
              <td class="text-center">
                @if ($commission->confirmed_at)
                  <div class="text-success font-weight-bold">
                    {{ Helper::date($commission->confirmed_at, '%d/%m %H:%M') }}
                  </div>
                @else
                  <i class="fas fa-minus fa-fw text-warning"></i>
                @endif
              </td>
            </tr> 
          @endforeach
        </tbody>
      </table>
    </div>
  </x-slot>
</x-card>

{{ $commissions->links() }}
@endsection