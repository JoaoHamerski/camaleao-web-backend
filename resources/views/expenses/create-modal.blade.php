@modal([
  'id' => 'expensesCreateModal',
  'title' => 'Cadastrar nova despesa',
  'headerClass' => 'text-white font-weight-bold bg-primary',
  'modalDialogClass' => 'modal-dialog-centered',
  'icon' => 'fas fa-plus',
  'view' => 'expenses._form',
  'viewAttrs' => [
    'method' => 'POST'
  ]
])