@extends('layout')

@section('title', 'Camaleão camisas - Setor de produção')

@section('content')
  <x-card :is-collapsed="true"
    class="mt-4"
    collapsed-id="totalComission"
    header-color="success"
  >
    <x-slot name="header">
      Comissão mensal
    </x-slot>

    <x-slot name="body">
      <month-commission></month-commission>
    </x-slot>
  </x-card>
  <x-card class="mt-4"
    header-color="primary"
    icon="fas fa-box"
  >
    <x-slot name="header">
      Pedidos em produção
    </x-slot>

    <x-slot name="body">
      <commissions-list user-role="{{Auth::user()->role->name}}"></commissions-list>
    </x-slot>
  </x-card>
@endsection