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
                            <input type="text" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" name="email">

                            @error('email')
                                <small class="text-danger"> {{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bolder" for="password">Senha: </label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password">

                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                                <label class="custom-control-label" for="remember">Lembre-se de mim</label>
                            </div>
                        </div>      

                        <button type="submit" id="btnLogin" class="btn btn-block btn-primary">Entrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $('#btnLogin').on('click', function(e) {
            let $btn = $(this);

            loadingBtn($btn, true);

            $('form').submit();
        });
    </script>
@endpush