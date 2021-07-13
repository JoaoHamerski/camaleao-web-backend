/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!**********************************************!*\
  !*** ./resources/js/partials/orders/show.js ***!
  \**********************************************/
applyCleave($('[name=value]'), cleaveValueBRL);
applyCleave($('[name=date]'), cleaveDate);
$('a[data-attach]').on('click', function (e) {
  e.preventDefault();
  var option = $(this).attr('data-attach');
  axios.post(getLocationURL() + '/file-view', {
    option: option
  }).then(function (response) {
    $('#fileViewerModal').find('.modal-body').html(response.data.view);
    $('#fileViewerModal').modal('show');
  });
});
$('#btnDeleteOrder').on('click', function (e) {
  e.preventDefault();
  Swal.fire({
    icon: 'error',
    iconHtml: '<i class="fas fa-trash-alt"></i>',
    title: 'Tem certeza?',
    html: '<div class="text-center">Todos os anexos e pagamentos serão excluídos também</div>',
    showCancelButton: true,
    confirmButtonText: 'Tenho',
    cancelButtonText: 'Cancelar'
  }).then(function (result) {
    if (result.isConfirmed) {
      $('#content').prepend($("\n        <div class=\"loading-page\">\n          <div class=\"spinner-border text-primary\"></div>\n        </div>\n        "));
      axios["delete"](getLocationURL() + '/deletar').then(function (response) {
        window.location = response.data.redirect;
      });
    }
  });
});
$('#btnAddNote').on('click', function (e) {
  e.preventDefault();
  var $btn = $(this);
  loadingBtn($btn, true);
  axios.post(getLocationURL() + '/new-note', {
    'order_note': $('[name=order_note]').val()
  }).then(function (response) {
    $('#listGroupNotes').append(response.data.noteListItem);
    $('button[data-target="#notesModal"]').html('Anotações (' + response.data.countNotes + ')');
    $('[name=order_note').val('').focus();
  })["catch"](function (error) {
    dispatchErrorMessages(error.response.data.errors);
  }).then(function () {
    loadingBtn($btn, false);
  });
});
$(document).on('click', '.btn-delete-item', function (e) {
  e.preventDefault();
  var $itemWrapper = $(this).parents('[data-id]');
  var id = $itemWrapper.attr('data-id');
  axios["delete"](getLocationURL() + '/delete-note/' + id).then(function (response) {
    $itemWrapper.remove();
    $('button[data-target="#notesModal"]').html('Anotações (' + response.data.countNotes + ')');
  });
});
$('#btnAddPayment').on('click', function (e) {
  e.preventDefault();
  var $btn = $(this),
      id = $(this).attr('data-id'),
      formData = new FormData($btn.parents('form').get(0));
  loadingBtn($btn, true);
  axios.post(getLocationURL() + '/new-payment', formData).then(function (response) {
    window.location = response.data.redirect;
  })["catch"](function (error) {
    dispatchErrorMessages(error.response.data.errors);
  }).then(function () {
    loadingBtn($btn, false);
  });
});
/******/ })()
;