@extends('layout')

@section('title', 'Entre com sua conta')

@section('content')
    <div class="container">
        <div class="col-md-4 mx-auto mt-5">
            <div class="card">
                <div class="card-header bg-primary text-white font-weight-bold">
                    Entre com sua conta
                </div>

                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-user-circle fa-5x text-dark my-2"></i>
                    </div>
                    <form action="{{ route('auth.login') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label class="font-weight-bold" for="email">E-mail: </label>
                            <input type="text" class="form-control" name="email">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bolder" for="password">Senha: </label>
                            <input type="password" class="form-control" name="password">
                        </div>

                        <button class="btn btn-block btn-primary">Entrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection