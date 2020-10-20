<form 
	@isset($action) action="{{ $action }}" @endisset 
	@isset($method) @if($method == 'GET') method="GET" @else method="POST" @endif @endisset >

	@isset($method)
		@method($method)
	@endisset
	
	<div class="form-group">
		<label class="font-weight-bold" for="name">Nome</label>
		<input class="form-control" 
			type="text" 
			name="name" 
			id="name"
			@if ($method == 'PATCH')
			value="{{ $user->name }}" 
			@endif>
	</div>

	<div class="form-group">
		<label class="font-weight-bold" for="email">Email</label>
		<input class="form-control" 
			type="text" 
			name="email" 
			id="email"
			@if ($method == 'PATCH')
			value="{{ $user->email }}" 
			@endif>
	</div>

	<div class="form-row d-flex">
		<div class="form-group col">
			@if ($method == 'PATCH')
				<label class="font-weight-bold" for="password">Nova senha</label>
			@else 
				<label class="font-weight-bold" for="password">Senha</label>
			@endif
			<input autocomplete="new-password" class="form-control" type="password" name="password" id="password">

			@if ($method == 'PATCH')
				<small class="text-secondary">Deixe a senha em branco caso não queria alterar</small>
			@endif
		</div>

		<div class="form-group col">
			<label class="font-weight-bold" for="password_confirmation">Confirme a senha</label>
			<input class="form-control" type="password" name="password_confirmation" id="password_confirmation">
		</div>
	</div>

	@if ($method == 'POST')
		<div class="form-group">
			<label class="font-weight-bold" for="role_id">Nível de autenticação</label>
			<select class="custom-select" name="role_id" id="role_id">
				<option selected="selected" value="">Selecione o nível do usuário</option>
				@foreach($roles as $role)
					<option value="{{ $role->id }}">{{ $role->name }}</option>
				@endforeach
			</select>
		</div>	
	@endif

	<div class="mt-4">
		@if ($method == 'POST')
			<button id="btnRegisterUser" class="btn btn-success">
				<i class="fas fa-check fa-fw mr-1"></i>Cadastrar
			</button>
		@else
			<button id="btnUpdateUser" class="btn btn-success">
				<i class="fas fa-check fa-fw mr-1"></i>Atualizar
			</button>
		@endif

		<button data-dismiss="modal" class="btn btn-light">Cancelar</button>
	</div>	
</form>
