/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!**********************************************!*\
  !*** ./resources/js/partials/client-form.js ***!
  \**********************************************/
applyCleave($('[name=phone]'), cleavePhone);
$('#btnCreateClient').click(function (e) {
  e.preventDefault();
  $btn = $(this);
  loadingBtn($btn, true);
  axios.post('/novo-cliente', {
    name: $('[name=name]').val(),
    phone: $('[name=phone]').val(),
    city: $('[name=city]').val()
  }).then(function (response) {
    $('#clientCreateModal').modal('hide');
    window.location = response.data.redirect;
  })["catch"](function (error) {
    dispatchErrorMessages(error.response.data.errors);
    loadingBtn($btn, false);
  });
});
$('#btnEditClient').click(function (e) {
  e.preventDefault();
  $btn = $(this);
  loadingBtn($btn, true);
  axios.patch(getLocationURL(), {
    name: $('[name=name]').val(),
    phone: $('[name=phone]').val(),
    city: $('[name=city]').val()
  }).then(function (response) {
    $('#clientEditModal').modal('hide');
    window.location = response.data.redirect;
  })["catch"](function (error) {
    dispatchErrorMessages(error.response.data.errors);
    loadingBtn($btn, false);
  });
});
$('#btnDeleteClient').click(function (e) {
  e.preventDefault();
  Swal.fire({
    icon: 'error',
    iconHtml: '<i class="fas fa-trash-alt"></i>',
    title: 'Tem certeza?',
    html: '<div class="text-center">Todos os pedidos, pagamentos e anexos do cliente serão deletados também</div>',
    showCancelButton: true,
    confirmButtonText: 'Tenho',
    cancelButtonText: 'Cancelar'
  }).then(function (result) {
    if (result.isConfirmed) {
      $('#content').prepend($('<div class="loading-page"><div class="spinner-border text-primary"></div></div>'));
      axios["delete"](getLocationURL()).then(function (response) {
        window.location = response.data.redirect;
      })["catch"](function (error) {
        console.log(error.response);
      });
    }
  });
});
/******/ })()
;