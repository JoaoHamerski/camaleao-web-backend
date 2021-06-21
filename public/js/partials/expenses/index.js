/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*************************************************!*\
  !*** ./resources/js/partials/expenses/index.js ***!
  \*************************************************/
applyCleave($('[name=value]'), cleaveValueBRL);
applyCleave($('[name*=date]'), cleaveDate);
applyCleave($('[name*=dia]'), cleaveDate);
/*
  Aplica as máscaras nos inputs caso haja alguma alteração no modal
  de formulário.
*/

var target = document.querySelector('#expensesEditModal .modal-body');
var observer = new MutationObserver(function (mutations) {
  applyCleave($('[name=value]'), cleaveValueBRL);
  applyCleave($('[name=date]'), cleaveDate);
});
$(document).on('input', 'input[type=file]', function () {
  var files = $(this)[0].files || null;
  var names = [];

  if (files.length > 0) {
    Object.entries(files).forEach(function (el) {
      names.push(el[1].name);
    });
    $(this).next('.custom-file-label').html(names.reverse().join(', '));
  } else {
    $(this).next('.custom-file-label').html('Selecione o comprovante');
  }
});
observer.observe(target, {
  attributes: true,
  childList: true,
  characterData: true
});
/*
  Adiciona um novo tipo de despesa ao clicar no botão de adicionar tipo de despesa
*/

$('#btnAddExpenseType').on('click', function (e) {
  e.preventDefault();
  $btn = $(this);
  loadingBtn($btn, true);
  axios.post(getLocationURL() + '/tipo-de-despesa', {
    expense_type: $('[name=expense_type]').val()
  }).then(function (response) {
    $('.modal-body .list-group').append(response.data.view);
    $('[name=expense_type]').val('');
  })["catch"](function (error) {
    dispatchErrorMessages(error.response.data.errors);
  }).then(function () {
    loadingBtn($btn, false);
  });
});
/*
  Deleta o tipo de despesa
*/

$(document).on('click', '.btn-delete-expense-type', function (e) {
  var _this = this;

  e.preventDefault();
  Swal.fire({
    icon: 'error',
    iconHtml: '<i class="fas fa-exclamation-triangle"></i>',
    title: 'Cuidado',
    html: "\n    <div class=\"text-center\">\n      Ao deletar esse tipo de despesa voc\xEA ter\xE1 que alterar todas as despesas que estavam cadastradas nesse tipo para um tipo existente\n      </div>\n      <div class=\"font-weight-bold text-center mt-3\">Voc\xEA tem certeza?</div> \n    ",
    showCancelButton: true,
    confirmButtonText: 'Tenho',
    cancelButtonText: 'Cancelar'
  }).then(function (result) {
    if (result.isConfirmed) {
      var id = $(_this).parents('[data-id]').attr('data-id');
      var spinner = "\n        <div class=\"loading-page\">\n            <div class=\"spinner-border text-primary\"></div>\n        </div>\n      ";
      $('.modal-body').prepend($(spinner));
      axios["delete"](getLocationURL() + '/tipo-de-despesa/' + id + '/deletar').then(function (response) {
        $('[data-id=' + id + ']').remove();
      })["catch"](function (error) {}).then(function (response) {
        $('.loading-page').remove();
      });
    }
  });
});
/*
  Edita o tipo de despesa
*/

$(document).on('click', '.btn-edit-expense-type', function (e) {
  e.preventDefault();
  var id = $(this).parents('[data-id]').attr('data-id');
  var expenseTypeName = $(this).parents('[data-id]').find('.expense-type-name').text().trim();
  var input = $("\n    <div class=\"col px-0\">\n      <form>\n        <div class=\"input-group\">\n          <input class=\"form-control\" name=\"expense_type_updated\" value=\"".concat(expenseTypeName, "\">\n          <div class=\"input-group-append\">\n            <button class=\"btn btn-outline-primary btn-update-expense-type\">Conclu\xEDdo</button>\n          </div>\n        </div>\n      </form>\n    </div>\n  "));
  $(this).parents('[data-id=' + id + ']').html(input);
  $('[name=expense_type_updated]').focus();
});
/*
  Atualiza a despesa que foi editada ao clicar no botão "concluído"
*/

$(document).on('click', '.btn-update-expense-type', function (e) {
  e.preventDefault();
  var $btn = $(this);
  var id = $(this).parents('[data-id]').attr('data-id');
  loadingBtn($btn, true);
  axios.patch(getLocationURL() + '/tipo-de-despesa/' + id, {
    'expense_type_updated': $btn.parents('.input-group').find('input').val()
  }).then(function (response) {
    $('#expenseTypesModal [data-id=' + id + ']').html($(response.data.view).children());
  })["catch"](function (error) {
    dispatchErrorMessages(error.response.data.errors);
  }).then(function () {
    loadingBtn($btn, false);
  });
});
/*
  Deleta uma despesa
*/

$('.btn-delete').on('click', function (e) {
  e.preventDefault();
  var id = $(this).parents('[data-id]').attr('data-id');
  var $btn = $(this);
  Swal.fire({
    icon: 'error',
    iconHtml: '<i class="fas fa-trash-alt"></i>',
    title: 'Tem certeza?',
    html: "\n    <div class=\"text-center\">\n      Isso ir\xE1 deletar a despesa\n      </div>\n    ",
    showCancelButton: true,
    confirmButtonText: 'Tenho',
    cancelButtonText: 'Cancelar'
  }).then(function (result) {
    if (result.isConfirmed) {
      loadingBtn($btn, true);
      axios["delete"](getLocationURL() + '/' + id + '/deletar').then(function (response) {
        window.location = response.data.redirect;
        loadingBtn($btn, false);
      });
    }

    ;
  });
});
/*
  Cria uma única despesa
*/

$('#btnCreateUniqueExpense').on('click', function (e) {
  e.preventDefault();
  $btn = $(this);
  loadingBtn($btn, true);
  var formData = new FormData($(this).parents('form').get(0));
  axios.post(getLocationURL() + '/cadastro', formData).then(function (response) {
    window.location = response.data.redirect;
  })["catch"](function (error) {
    console.log(error.response);
    dispatchErrorMessages(error.response.data.errors, '#createFormModal');
    loadingBtn($btn, false);
  });
});
/*
  Atualiza a despesa caso clique no botão de atualizar despesa
*/

$(document).on('click', '#btnUpdateExpense', function (e) {
  e.preventDefault();
  var id = $(this).attr('data-id');
  var $btn = $(this);
  loadingBtn($btn, true);
  var formData = new FormData($(this).parents('form').get(0));
  axios.post(getLocationURL() + '/' + id, formData).then(function (response) {
    window.location = response.data.redirect;
  })["catch"](function (error) {
    dispatchErrorMessages(error.response.data.errors, '#editFormModal');
    loadingBtn($btn);
  });
});
/*
  Abre o modal de atualizar despesas ao clicar no botão de editar despesa
*/

$('.btn-edit').on('click', function () {
  var id = $(this).parents('[data-id]').attr('data-id');
  axios.get(getLocationURL() + '/' + id + '/get-edit-form').then(function (response) {
    $('#expensesEditModal .modal-body').html(response.data.view);
  });
});
/*
  Valida e dá submit no relatório das despesas caso validado.
*/

$('#btnGenerateReport').on('click', function (e) {
  e.preventDefault();
  $btn = $(this);
  loadingBtn($btn, true);
  axios.get(getLocationURL() + '/relatorio', {
    params: {
      dia_inicial: $('[name=dia_inicial]').val(),
      dia_final: $('[name=dia_final]').val()
    }
  }).then(function (response) {
    $('#reportForm').submit();
  })["catch"](function (error) {
    dispatchErrorMessages(error.response.data.errors);
  }).then(function () {
    loadingBtn($btn, false);
  });
});
$(document).on('click', '#deleteReceipt', function (e) {
  e.preventDefault();
  var id = $(this).parents('[data-id]').attr('data-id');
  var $btn = $(this);
  axios["delete"](getLocationURL() + '/' + id + '/delete-receipt').then(function (response) {
    $btn.parents('[data-id]').remove();
  });
});
$('.btn-view-receipt').on('click', function (e) {
  e.preventDefault();
  var id = $(this).parents('[data-id]').attr('data-id');
  axios.get(getLocationURL() + '/' + id + '/get-view-receipt').then(function (response) {
    $('#viewReceiptModal .modal-body').html(response.data.view);
  });
});
$(document).on('change', '[name=expense_type_id]', function () {
  var formGroup = "\n    <div class=\"form-group\">\n      <label for=\"employee_name\" id=\"employee_name\" class=\"font-weight-bold\">Nome do funcion\xE1rio</label>\n      <small class=\"text-secondary\">(opcional)</small>\n      <input type=\"text\" name=\"employee_name\" class=\"form-control\">\n    </div>\n  ";
  var text = $('[name=expense_type_id] option:selected').text().trim();

  if (text.toUpperCase() == 'mão de obra'.toUpperCase()) {
    $(this).parents('.form-group').after($(formGroup));
  } else {
    $('[name=employee_name]').parents('.form-group').remove();
  }
});
/******/ })()
;