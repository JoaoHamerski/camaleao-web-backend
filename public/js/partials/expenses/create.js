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
/******/ 	return __webpack_require__(__webpack_require__.s = 9);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/partials/expenses/create.js":
/*!**************************************************!*\
  !*** ./resources/js/partials/expenses/create.js ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

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
  axios.get(getLocationURL() + '/get-inline-form').then(function (response) {
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
    loadingBtn($btn, false);
    dispatchErrorMessages(error.response.data.errors);
  });
});

/***/ }),

/***/ 9:
/*!********************************************************!*\
  !*** multi ./resources/js/partials/expenses/create.js ***!
  \********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /home/joao/Desktop/wellington/site/resources/js/partials/expenses/create.js */"./resources/js/partials/expenses/create.js");


/***/ })

/******/ });