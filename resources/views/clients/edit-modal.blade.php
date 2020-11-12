@include('components.modal', [
	'id' => 'clientEditModal',
	'title' => 'Atualizar dados do cliente',
	'headerClass' => 'text-white font-weight-bold bg-success',
	'modalDialogClass' => 'modal-dialog-centered',
	'icon' => 'fas fa-user',
	'view' => 'clients._form',
	'viewAttrs' => [
		'method' => 'PATCH'
	]
])