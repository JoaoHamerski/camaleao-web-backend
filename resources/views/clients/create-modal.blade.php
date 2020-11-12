@include('components.modal', [
	'id' => 'clientCreateModal',
	'title' => 'Cadastrar novo cliente',
	'headerClass' => 'text-white font-weight-bold bg-primary',
	'modalDialogClass' => 'modal-dialog-centered',
	'icon' => 'fas fa-user-plus',
	'view' => 'clients._form',
	'viewAttrs' => [
		'method' => 'POST'
	]
])