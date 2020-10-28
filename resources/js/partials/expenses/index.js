applyCleave($('[name=value]'), cleaveValueBRL);
applyCleave($('[name*=date]'), cleaveDate);
applyCleave($('[name*=dia]'), cleaveDate);


/*
  Aplica as máscaras nos inputs caso haja alguma alteração no modal
  de formulário.
*/
let target = document.querySelector('#editFormModal .modal-body');

let observer = new MutationObserver(mutations => {
  applyCleave($('[name=value]'),  cleaveValueBRL);
  applyCleave($('[name=date]'), cleaveDate);
});

observer.observe(target, {attributes: true, childList: true, characterData: true});

/*
  Adiciona um novo tipo de despesa ao clicar no botão de adicionar tipo de despesa
*/
$('#btnAddExpenseType').on('click', function(e) {
  e.preventDefault();

  $btn = $(this);

  loadingBtn($btn, true);

  axios.post(getLocationURL() + '/tipo-de-despesa', {
    expense_type: $('[name=expense_type]').val()
  })
  .then(response => {
    $('.modal-body .list-group').append(response.data.view);
    $('[name=expense_type]').val('');
  })
  .catch(error => {
    dispatchErrorMessages(error.response.data.errors);
  })
  .then(function() {
    loadingBtn($btn, false);
  });
});

/*
  Deleta o tipo de despesa
*/
$(document).on('click', '.btn-delete-expense-type', function(e) {
  e.preventDefault();

  Swal.fire({
    icon: 'error',
    iconHtml: '<i class="fas fa-exclamation-triangle"></i>',
    title: 'Cuidado',
    html: `
    <div class="text-center">
      Ao deletar esse tipo de despesa você terá que alterar todas as despesas que estavam cadastradas nesse tipo para um tipo existente
      </div>
      <div class="font-weight-bold text-center mt-3">Você tem certeza?</div> 
    `,
    showCancelButton: true,
    confirmButtonText: 'Tenho',
    cancelButtonText: 'Cancelar'
  })
  .then(result => {
    if (result.isConfirmed) {
      let id = $(this).parents('[data-id]').attr('data-id');
      let spinner = `
        <div class="loading-page">
            <div class="spinner-border text-primary"></div>
        </div>
      `;
          
      $('.modal-body').prepend($(spinner));

      axios.delete(getLocationURL() + '/tipo-de-despesa/' + id + '/deletar')
        .then(response => {
          $('[data-id=' + id + ']').remove();
        })
        .catch(error => {})
        .then(response => {
          $('.loading-page').remove();
        });

    }
  });
}); 

/*
  Edita o tipo de despesa
*/

$(document).on('click' ,'.btn-edit-expense-type', function(e) {
  e.preventDefault();

  let id = $(this).parents('[data-id]').attr('data-id');
  let expenseTypeName = $(this).parents('[data-id]').find('.expense-type-name').text().trim();

  let input = $(`
    <div class="col px-0">
      <form>
        <div class="input-group">
          <input class="form-control" name="expense_type_updated" value="${expenseTypeName}">
          <div class="input-group-append">
            <button class="btn btn-outline-primary btn-update-expense-type">Concluído</button>
          </div>
        </div>
      </form>
    </div>
  `);

  $(this).parents('[data-id=' + id + ']').html(input);
  $('[name=expense_type_updated]').focus();
});

/*
  Atualiza a despesa que foi editada ao clicar no botão "concluído"
*/
$(document).on('click', '.btn-update-expense-type', function(e) {
  e.preventDefault();

  let $btn = $(this);
  let id = $(this).parents('[data-id]').attr('data-id');

  loadingBtn($btn, true);

  axios.patch(getLocationURL() + '/tipo-de-despesa/' + id, {
    'expense_type_updated': $('[name=expense_type_updated]').val()
  })
  .then(response => {
    $('[data-id=' + id + ']').html($(response.data.view).children());
  })
  .catch(error => {
    dispatchErrorMessages(error.response.data.errors);
  })
  .then(function() {
    loadingBtn($btn, false);
  })
}); 


/*
  Deleta uma despesa
*/

$('.btn-delete').on('click', function(e) {
  e.preventDefault();

  let id = $(this).parents('[data-id]').attr('data-id');
  let $btn = $(this);


  Swal.fire({
    icon: 'error',
    iconHtml: '<i class="fas fa-trash-alt"></i>',
    title: 'Tem certeza?',
    html: `
    <div class="text-center">
      Isso irá deletar a despesa
      </div>
    `,
    showCancelButton: true,
    confirmButtonText: 'Tenho',
    cancelButtonText: 'Cancelar'
  })
  .then(result => {
    if (result.isConfirmed) {
      loadingBtn($btn, true);
      axios.delete(getLocationURL() + '/' + id + '/deletar')
        .then(response => {
          window.location = response.data.redirect;

          loadingBtn($btn, false);
        });
    };
  });
});

/*
  Cria uma única despesa
*/

$('#btnCreateUniqueExpense').on('click', function(e) {
  e.preventDefault();

  $btn = $(this);

  loadingBtn($btn, true);

  axios.post(getLocationURL() + '/cadastro', {
    description: $('#createFormModal [name=description]').val(),
    expense_type_id: $('#createFormModal [name=expense_type_id]').val(),
    value: $('#createFormModal [name=value]').val(),
    date: $('#createFormModal [name=date]').val()
  })
  .then(response => {
    window.location = response.data.redirect;
  })
  .catch(error => {
    dispatchErrorMessages(error.response.data.errors, '#createFormModal');
    loadingBtn($btn, false);
  });   
});

/*
  Atualiza a despesa caso clique no botão de atualizar despesa
*/
$(document).on('click', '#btnUpdateExpense', function(e) {

  e.preventDefault();

  let id = $(this).attr('data-id');
  let $btn = $(this);

  loadingBtn($btn, true);

  axios.patch(getLocationURL() + '/' + id, {
    description: $('#editFormModal [name=description]').val(),
    expense_type_id: $('#editFormModal [name=expense_type_id]').val(),
    value: $('#editFormModal [name=value]').val(),
    date: $('#editFormModal [name=date]').val()
  })
    .then(response => {
      window.location = response.data.redirect;
    })
    .catch(error => {
      dispatchErrorMessages(error.response.data.errors, '#editFormModal');
      loadingBtn($btn);
    });
});

/*
  Abre o modal de atualizar despesas ao clicar no botão de editar despesa
*/
$('.btn-edit').on('click', function() {
  let id = $(this).parents('[data-id]').attr('data-id');

  axios.get(getLocationURL() + '/' + id + '/get-edit-form')
    .then(response => {
      $('#editFormModal .modal-body').html(response.data.view);
    });
});

/*
  Valida e dá submit no relatório das despesas caso validado.
*/
$('#btnGenerateReport').on('click', function(e) {
  e.preventDefault();

  axios.get(getLocationURL() + '/relatorio', {
    params: {
      dia_inicial: $('[name=dia_inicial]').val(),
      dia_final: $('[name=dia_final]').val()
    }
  })
  .then(response => {
    $('#reportForm').submit();
  })
  .catch(error => {
    dispatchErrorMessages(error.response.data.errors);
  });
});