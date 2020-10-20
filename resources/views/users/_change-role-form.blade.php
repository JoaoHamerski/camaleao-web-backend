<form>
	<div>
		<h5 class="font-weight-bold text-center">{{ $user->name }}</h5>
	</div>

	<div class="form-group">
		<label class="font-weight-bold" for="role_id_change"></label>
		<select class="custom-select" name="role_id_change" id="role_id_change">
			@foreach($roles as $role)
				<option @if ($role->id == $user->role_id) selected="selected" @endif 
						value="{{ $role->id }}">{{ $role->name }}</option>
			@endforeach
		</select>
	</div>

	<button class="btn btn-success" id="btnSaveChangedRole" data-id="{{ $user->id }}"><i class="fas fa-check fa-fw mr-1"></i>Salvar</button>
	<button class="btn btn-light" data-dismiss="modal">Cancelar</button>
</form>