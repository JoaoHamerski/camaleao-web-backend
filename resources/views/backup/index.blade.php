@extends('layout')

@section('title', 'Backup do sistema')

@section('content')
  <div class="col col-md-7 mx-auto mt-5">
    <div class="card">
      <div class="card-header bg-primary">
        <h5 class="font-weight-bold text-white mb-0">
          Backup do sistema
        </h5>
      </div>

      <div class="card-body">
        <div class="text-center">
          <h5 class="font-weight-bold text-secondary mt-3">
            <div><i class="fas fa-download fa-fw fa-2x mr-1 text-primary mb-2"></i></div>
            Faça download do último backup do sistema abaixo
          </h5>

          <div class="small text-secondary mb-4">
            Contém o banco de dados e todas as imagens armazenadas no sistema
          </div>

          <div class="small text-secondary mb-2 ">
            Feito em <strong>{{ Helper::date($lastModified, '%d/%m/%Y às %Hh%Mm') }}</strong>
          </div>

          <a href="{{ route('backup.download') }}" class="btn btn-lg btn-primary font-weight-bold px-5">
            Baixar <small>({{ $size}})</small>
          </a>
        </div>
      </div>
    </div>
  </div>
@endsection