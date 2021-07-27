/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*************************************************!*\
  !*** ./resources/js/partials/payments/index.js ***!
  \*************************************************/
$('[data-target="#changePaymentModal"]').on('click', function (e) {
  e.preventDefault();
  var id = $(this).parents('[data-id]').attr('data-id');
  axios.get(getLocationURL() + '/pagamento/' + id + '/get-change-payment-view').then(function (response) {
    $('#changePaymentModal .modal-body').html(response.data.view);
  })["catch"](function (error) {
    console.log(error.response);
  });
});
$(document).on('click', '#btnChangePayment', function (e) {
  e.preventDefault();
  var id = $(this).attr('data-id'),
      $btn = $(this),
      formData = new FormData($(this).parents('form').get(0));
  loadingBtn($btn, true);
  axios.post(getLocationURL() + '/pagamento/' + id, formData).then(function (response) {
    window.location = response.data.redirect;
  })["catch"](function (error) {
    dispatchErrorMessages(error.response.data.errors);
    loadingBtn($btn, false);
  });
});
/******/ })()
;