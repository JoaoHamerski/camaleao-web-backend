@extends('layout')

@section('title', 'Caixa diário - Camaleão')

@section('content')

  <div class="card mt-4">
    <div class="card-header bg-success">
      <h5 class="font-weight-bold text-white mb-0">
        <i class="fas fa-cash-register fa-fw mr-1"></i>Caixa diário
      </h5>
    </div>

    <div class="card-body">
      <daily-cash-list
        :user-role="{{ Auth::user()->role_id }}"
      ></daily-cash-list>
    </div>
  </div>

@endsection