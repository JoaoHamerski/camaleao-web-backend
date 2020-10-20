/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 3);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/partials/show-order.js":
/*!*********************************************!*\
  !*** ./resources/js/partials/show-order.js ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$('a[data-option]').on('click', function (e) {
  e.preventDefault();
  var option = $(this).attr('data-option');
  axios.post(window.location.href + '/file-view', {
    option: option
  }).then(function (response) {
    $('#fileViewerModal').find('.modal-body').html(response.data.view);
    $('#fileViewerModal').modal('show');
    console.log(response);
  })["catch"](function (error) {
    console.log(error.response);
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
      $('#content').prepend($('<div class="loading-page"><div class="spinner-border text-primary"></div></div>'));
      axios["delete"](window.location.href).then(function (response) {
        window.location = response.data.redirect;
      });
    }
  });
});
$('#btnAddNote').on('click', function (e) {
  e.preventDefault();
  var $btn = $(this);
  loadingBtn($btn, true);
  axios.post(window.location.href + '/new-note', {
    'order_note': $('[name=order_note]').val()
  }).then(function (response) {
    $('#listGroupNotes').append(response.data.noteListItem);
    $('button[data-target="#notesModal"]').html('Anotações (' + response.data.countNotes + ')');
    $('[name=order_note').val('');
  })["catch"](function (error) {
    console.log(error.response);
    dispatchErrorMessages(error.response.data.errors);
  }).then(function () {
    loadingBtn($btn, false);
  });
});
$(document).on('click', '.btn-delete-item', function (e) {
  e.preventDefault();
  var $itemWrapper = $(this).parents('[data-id]');
  var id = $itemWrapper.attr('data-id');
  axios["delete"](window.location.href + '/delete-note/' + id).then(function (response) {
    $itemWrapper.remove();
    $('button[data-target="#notesModal"]').html('Anotações (' + response.data.countNotes + ')');
  })["catch"](function (error) {});
});
applyCleave($('[name=value]'), cleaveValueBRL);
applyCleave($('[name=date]'), cleaveDate);
$('#today').on('click', function (e) {
  e.preventDefault();
  var date = new Date();
  var today = '';
  var month = date.getMonth() + 1;
  today += date.getDate() + '/';
  today += month < 10 ? '0' + month : month;
  today += '/';
  today += date.getFullYear();
  $('[name=date]').val(today).focus();
});
$('#btnAddPayment').on('click', function (e) {
  e.preventDefault();
  $btn = $(this);
  var id = $(this).attr('data-id');
  loadingBtn($btn, true);
  axios.post(window.location.href + '/new-payment', {
    value: $('[name=value]').val(),
    date: $('[name=date]').val(),
    note: $('[name=note]').val()
  }).then(function (response) {
    window.location = response.data.redirect;
  })["catch"](function (error) {
    console.log(error.response);
    dispatchErrorMessages(error.response.data.errors);
  }).then(function () {
    loadingBtn($btn, false);
  });
});

/***/ }),

/***/ 3:
/*!***************************************************!*\
  !*** multi ./resources/js/partials/show-order.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /home/joao/Desktop/wellington/site/resources/js/partials/show-order.js */"./resources/js/partials/show-order.js");


/***/ })

/******/ });