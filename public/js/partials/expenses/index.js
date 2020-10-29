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
/******/ 	return __webpack_require__(__webpack_require__.s = 8);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/partials/expenses/index.js":
/*!*************************************************!*\
  !*** ./resources/js/partials/expenses/index.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

applyCleave($('[name=value]'), cleaveValueBRL);
applyCleave($('[name*=date]'), cleaveDate);
applyCleave($('[name*=dia]'), cleaveDate);
/*
  Aplica as máscaras nos inputs caso haja alguma alteração no modal
  de formulário.
*/

var target = document.querySelector('#editFormModal .modal-body');
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
    'expense_type_updated': $('[name=expense_type_updated]').val()
  }).then(function (response) {
    $('[data-id=' + id + ']').html($(response.data.view).children());
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
    $('#editFormModal .modal-body').html(response.data.view);
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
    console.log(response.data);
    $btn.parents('[data-id]').remove();
  })["catch"](function (error) {
    console.log(error.response);
    ;
  });
});
$('.btn-view-receipt').on('click', function (e) {
  e.preventDefault();
  var id = $(this).parents('[data-id]').attr('data-id');
  axios.get(getLocationURL() + '/' + id + '/get-view-receipt').then(function (response) {
    $('#viewReceiptModal .modal-body').html(response.data.view);
  })["catch"](function (error) {
    console.log(error.response);
  });
});

/***/ }),

/***/ 8:
/*!*******************************************************!*\
  !*** multi ./resources/js/partials/expenses/index.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /home/joao/Desktop/wellington/site/resources/js/partials/expenses/index.js */"./resources/js/partials/expenses/index.js");


/***/ })

/******/ });