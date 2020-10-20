@extends('layout')

@section('title', 'Usuários')

@section('content')
	
	<div class="mt-5">
		<button data-toggle="modal" data-target="#createUserModal" class="btn btn-primary" href="">
			<i class="fas fa-user-plus fa-fw mr-1"></i>Novo usuário
		</button>
	</div>

	<div class="card mt-2">
		<div class="card-header bg-dark text-white font-weight-bold">
			<i class="fas fa-users fa-fw mr-1"></i> Lista de usuários do sistema
		</div>

		<div class="card-body px-0">
			<div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th>Nome</th>
							<th>E-mail</th>
							<th>Nível de usuário</th>
							<th class="text-center">Alterar nível</th>
							<th class="text-center">Deletar</th>
						</tr>
					</thead>

					<tbody>
						@foreach($users as $user)
						<tr data-id="{{ $user->id }}">
							<td>{{ $user->name }}</td>
							<td>{{ $user->email }}</td>
							<td>{{ $user->role->name }}</td>
							<td class="text-center">
								@if ($user->id == Auth::user()->id)
									<button style="pointer-events: none;" class="btn btn-outline-primary" disabled="disabled">
										<i class="fas fa-user-edit"></i>
									</button>
								@else
									<button data-toggle="modal" data-target="#changeRoleModal" class="btn btn-outline-primary btn-change-role">
										<i class="fas fa-user-edit"></i>
									</button>
								@endif
							</td>

							<td class="text-center">
								@if ($user->id == Auth::user()->id)
									<button class="btn btn-outline-danger" disabled="disabled">
										<i class="fas fa-trash-alt"></i>
									</button>
								@else
									<button class="btn btn-outline-danger btn-delete-user">
										<i class="fas fa-trash-alt"></i>
									</button>
								@endif
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="mt-2">
		{{ $users->links() }}
	</div>

	@include('users._create-user-modal')
	@include('users._change-role-modal')
@endsection

@push('script')
	<script src="{{ mix('js/partials/users.js') }}"></script>
@endpush