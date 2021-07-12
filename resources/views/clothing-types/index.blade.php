@extends('layout')

@section('title', 'Gerenciamento - Tipos de roupa')

@section('content')
  <div class="card mt-5">
    <div class="card-header bg-primary">
      <h5 class="font-weight-bold text-white mb-0">
        Gerencie os tipos de roupas
      </h5>
    </div>

    <div class="card-body">
      <clothing-types-list></clothing-types-list>
    </div>
  </div>
@endsection