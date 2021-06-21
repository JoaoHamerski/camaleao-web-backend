@extends('layout')

@section('title', 'Cidades - Gerenciamento')

@section('content')

  <div class="col-md-12 mx-auto mt-5"> 
    <div class="card">
      <div class="card-header bg-primary text-white font-weight-bold">
        <i class="fas fa-city fa-fw mr-1 text-white"></i>
        Gerenciamento de cidades
      </div>

      <div class="card-body">
        <cities-list />
      </div>
    </div>
  </div>

@endsection