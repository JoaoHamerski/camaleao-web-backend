@extends('layout')

@section('title', 'Cidade - ' . $city->name)

@section('content')

  <div class="col-md-10 mx-auto mt-5">
    <a href="{{ route('cities.index') }}" class="btn btn-outline-primary mb-2">
      <i class="fas fa-arrow-alt-circle-left mr-1"></i>Voltar
    </a>
    <div class="card">
      <div class="card-header bg-primary text-white font-weight-bold">
        <i class="fas fa-city fa-fw mr-1 text-white"></i>
          {{ $city->name }} {{ $city->state ? ' - ' . $city->state->name : '' }}
      </div>

      <div class="card-body px-0">
        <h5 class="text-center mx-3 mb-3">
          Clientes residentes em <strong>{{ $city->name}} {{ $city->state ? ' - ' . $city->state->name : '' }}</strong>
        </h5>
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Nome</th>
              <th>Telefone</th>
            </tr>
          </thead>

          <tbody>
            @foreach($clients as $client)
              <tr data-url="{{ $client->path() }}" class="clickable-link">
                <td>{{ $client->name }}</td>
                <td>{{ Mask::phone($client->phone) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection