/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*********************************************!*\
  !*** ./resources/js/partials/my-account.js ***!
  \*********************************************/
$('#btnUpdateUser').on('click', function (e) {
  e.preventDefault();
  $btn = $(this);
  loadingBtn($btn, true);
  axios.patch(getLocationURL(), {
    name: $('[name=name]').val(),
    email: $('[name=email]').val(),
    password: $('[name=password]').val(),
    password_confirmation: $('[name=password_confirmation]').val()
  }).then(function (response) {
    window.location = response.data.redirect;
  })["catch"](function (error) {
    loadingBtn($btn, false);
    dispatchErrorMessages(error.response.data.errors);
  });
});
$('#btnDeleteAccount').on('click', function (e) {
  e.preventDefault();
  Swal.fire({
    icon: 'error',
    iconHtml: '<i class="fas fa-trash-alt"></i>',
    title: 'Tem certeza?',
    html: '<div class="text-center">Sua conta não poderá ser recuperada</div>',
    showCancelButton: true,
    confirmButtonText: 'Tenho',
    cancelButtonText: 'Cancelar'
  }).then(function (result) {
    if (result.isConfirmed) {
      axios["delete"](getLocationURL() + '/deletar').then(function (response) {
        window.location = response.data.redirect;
      });
    }
  });
});
/******/ })()
;