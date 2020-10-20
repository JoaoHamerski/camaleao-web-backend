@extends('layout')

@section('title', 'Minha conta - ' . $user->name)

@section('content')
	
	<div class="col col-md-6 mx-auto mt-5 px-0">
		<div class="card">
			<div class="card-header bg-primary text-white font-weight-bold">
				<i class="fas fa-user fa-fw mr-1"></i> Minha conta
			</div>

			<div class="card-body">
				<ul class="list-group list-group-flush">
					<li class="list-group-item">
						<strong>Nome: </strong> {{ $user->name }}
					</li>
					<li class="list-group-item">
						<strong>E-mail: </strong> {{ $user->email }}
					</li>
					<li class="list-group-item">
						<strong>Nível de usuário:</strong> {{ $user->role->name }}
					</li>
					<li class="list-group-item">
						<strong>Senha: </strong> *****
					</li>
					<li class="list-group-item">
						<strong>Criada em: </strong> {{ Helper::date($user->created_at, '%d/%m/%Y') }}
					</li>
				</ul>

				<div class="d-flex justify-content-between mx-3 mt-3">
					<a href="#editAccountModal" data-toggle="modal"><i class="fas fa-edit fa-fw mr-1"></i>Alterar dados</a>

					<a id="btnDeleteAccount" href="" class="text-danger"><i class="fas fa-trash-alt fa-fw mr-1"></i>Deletar conta</a>
				</div>
			</div>
		</div>
	</div>
	@include('users._edit-account-modal')
@endsection

@push('script')
	<script src="{{ mix('js/partials/my-account.js') }}"></script>
@endpush