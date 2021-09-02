@extends('layout')

@section('title', 'Filiais - Gerenciamento')

@section('content')
  <div class="col-md-12 mx-auto mt-5">
    <div class="card">
      <div class="card-header bg-primary text-white font-weight-bold">
        <i class="fas fa-building fa-fw mr-1 text-white"></i>
        Gerenciamento de filiais
      </div>

      <div class="card-body">

        <the-branches></the-branches>
      </div>
    </div>
  </div>
@endsection
