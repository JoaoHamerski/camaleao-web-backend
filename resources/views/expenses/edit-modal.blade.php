@modal([
  'id' => 'expensesEditModal',
  'title' => 'Editar despesa',
  'headerClass' => 'text-white font-weight-bold bg-primary',
  'modalDialogClass' => 'modal-dialog-centered',
  'icon' => 'fas fa-list',
  'view' => 'expenses._form',
  'viewAttrs' => [
    'method' => 'PATCH'
  ]
])