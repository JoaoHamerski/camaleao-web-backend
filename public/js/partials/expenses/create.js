/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!**************************************************!*\
  !*** ./resources/js/partials/expenses/create.js ***!
  \**************************************************/
/*
	Aplica as máscaras nos inputs iniciais da página
*/
applyCleave($('[name*=value]'), cleaveValueBRL);
applyCleave($('[name*=date]'), cleaveDate);
applyCleave($('[name=all_date]'), cleaveDate);
/*
	Pega a soma do valor total dos inputs de valor
*/

function getTotalValue() {
  var total = 0;
  $('[name*=value]').each(function () {
    total += +sanitizeMoney($(this).val());
  });
  return total;
}
/*
	Toda vez que houver alteração, ou seja, toda vez que um novo
	formulário em linha for adicionado, é aplicado as máscaras de input
	nesse formulário.
*/


var target = document.querySelector('#formExpenses');
var observer = new MutationObserver(function (mutations) {
  applyCleave($('[name*=value]').last(), cleaveValueBRL);
  applyCleave($('[name*=date]').last(), cleaveDate);

  if ($('[name=all_date]').val().trim() !== '') {
    $('[name*=date]').last().val($('[name=all_date]').val());
  }
});
observer.observe(target, {
  attributes: true,
  childList: true,
  characterData: true
});
/*
	Adiciona um novo formulário em linha quando clicado no botão de mais
*/

$('#btnNewExpense').on('click', function (e) {
  e.preventDefault();
  $btn = $(this);
  loadingBtn($(this), true);
  var index = $('.form-inline-wrapper').last().attr('data-index');
  index = isNaN(index) ? 0 : index;
  axios.get(getLocationURL() + '/get-inline-form', {
    params: {
      index: +index + 1
    }
  }).then(function (response) {
    $('#btnNewExpense').parent().before(response.data.view);
  })["catch"](function (error) {}).then(function () {
    loadingBtn($btn, false);
  });
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
    $(this).next('.custom-file-label').html('Comprovante');
  }
});
/*
	Atualiza o valor total de despesas ao alterar qualquer input value
*/

$('#formExpenses').on('input', '[name*=value]', function (e) {
  var value = sanitizeMoney($(this).val());
  var totalValue = getTotalValue();
  var formatter = new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  });
  $('#totalValue').text(formatter.format(getTotalValue()));
});
$('[name=all_date]').on('input focus', function () {
  $('[name*=date]').val($(this).val());
});
/*
	Deleta o elemento responsável pelo formulário em linha
*/

$('#formExpenses').on('click', '.btn-delete', function (e) {
  e.preventDefault();
  $(this).closest('.form-inline-wrapper').remove();
});
/*
	Envia os dados para o servidor para verificar se há erros,
	se não houver, redireciona para a página retornada pelo servidor.
*/

$('#formExpenses button[type="submit"]').on('click', function (e) {
  e.preventDefault();
  var formData = new FormData(document.querySelector('#formExpenses'));
  var $btn = $(this);
  loadingBtn($btn, true);
  axios.post(getLocationURL(), formData).then(function (response) {
    window.location = response.data.redirect;
  })["catch"](function (error) {
    console.log(error.response);
    loadingBtn($btn, false);
    dispatchErrorMessages(error.response.data.errors);
  });
});
$(document).on('change', '[name*=expense_type_id]', function (e) {
  var dataIndex = $(this).parents('.form-inline-wrapper').attr('data-index');
  var formGroup = "\n\t\t<div class=\"form-row\">\n\t\t\t<div class=\"form-group col col-md-4\">\n\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"employee_name[".concat(dataIndex, "]\" placeholder=\"Nome do funcion\xE1rio...\">\n\t\t\t</div>\n\t\t</div>\n\t");
  var text = $("[name=\"expense_type_id[".concat(dataIndex, "]\"] option:selected")).text().trim();

  if (text.toUpperCase() == 'mão de obra'.toUpperCase()) {
    $(this).parents('.form-inline-wrapper').find('.form-row:nth-child(2)').after($(formGroup));
  } else {
    $(this).parents('.form-inline-wrapper').find('.form-row:nth-child(3)').remove();
  }
});
/******/ })()
;