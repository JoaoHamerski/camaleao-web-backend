/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!***********************************************!*\
  !*** ./resources/js/partials/orders/index.js ***!
  \***********************************************/
applyCleave($('[name*=data]'), cleaveDate);
$('#formGenerateReport button[type=submit]').on('click', function (e) {
  e.preventDefault();
  var $btn = $(this);
  loadingBtn($btn, true);
  axios.get(getLocationURL() + '/relatorio', {
    params: {
      cidade: $('[name=cidade]').val(),
      status: $('[name=status]').val(),
      data_de_fechamento: $('[name=data_de_fechamento]').val()
    }
  }).then(function (response) {
    $('#formGenerateReport').submit();
  })["catch"](function (error) {
    dispatchErrorMessages(error.response.data.errors);
  }).then(function () {
    loadingBtn($btn, false);
  });
});
$('#formGenerateReportProduction button[type=submit]').on('click', function (e) {
  e.preventDefault();
  var $btn = $(this);
  loadingBtn($btn, true);
  axios.get(getLocationURL() + '/relatorio-data-producao', {
    params: {
      data_de_producao: $('[name=data_de_producao]').val()
    }
  }).then(function (response) {
    $('#formGenerateReportProduction').submit();
  })["catch"](function (error) {
    dispatchErrorMessages(error.response.data.errors);
  }).then(function () {
    loadingBtn($btn, false);
  });
});
$(document).on('click blur type focus', '[name=data_de_fechamento]', function (e) {
  if ($(this).val() == '') {
    $('[name="em_aberto"]').removeAttr('disabled');
    $('[name="em_aberto"]')[0].checked = true;
  } else {
    $('[name="em_aberto"]').prop('checked', false);
    $('[name="em_aberto"]').prop('disabled', true);
  }
});
/******/ })()
;