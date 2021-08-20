/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./resources/js/helpers.js":
/*!*********************************!*\
  !*** ./resources/js/helpers.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "setCookie": () => (/* binding */ setCookie),
/* harmony export */   "destroyCookie": () => (/* binding */ destroyCookie),
/* harmony export */   "sanitizeMoney": () => (/* binding */ sanitizeMoney),
/* harmony export */   "dispatchErrorMessages": () => (/* binding */ dispatchErrorMessages),
/* harmony export */   "loadingBtn": () => (/* binding */ loadingBtn),
/* harmony export */   "getLocationURL": () => (/* binding */ getLocationURL),
/* harmony export */   "scrollToElement": () => (/* binding */ scrollToElement),
/* harmony export */   "openInNewTab": () => (/* binding */ openInNewTab)
/* harmony export */ });
/*
	Seta um cookie, em que "data"
	é informado o "name" e "value" do cookie.
*/
var setCookie = function setCookie(data) {
  axios.post('/set-cookie', data);
};
/*
	Deleta um cookie com o nome especificado.
*/

var destroyCookie = function destroyCookie(name) {
  axios["delete"]('/destroy-cookie', {
    name: name
  });
};
/*
	Sanitiza o valor em dinheiro
	Ex.: R$ 123,45 => 123.45
*/

var sanitizeMoney = function sanitizeMoney(str) {
  str = str.replace(/\./g, '');
  str = str.replace(',', '.');
  str = str.replace('R$', '');
  return str.trim();
};
/*
	Exibe todas as mensagens de erro nos inputs automaticamente,
	que foram recebidas do servidor.
*/

var dispatchErrorMessages = function dispatchErrorMessages(errors) {
  var wrapper = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  scrollToElement($('[name*=' + Object.entries(errors)[0][0].split('.')[0] + ']'));
  Object.entries(errors).forEach(function (el) {
    var $errorMessage = $('<div class="text-danger small">' + el[1][0] + '</div>');
    var name = el[0],
        child,
        selector = '[name=' + name + ']';

    if (name.includes('.')) {
      child = name.split('.')[1];
      name = name.split('.')[0];
      selector = '[name^=' + name + ']';
    }

    if (wrapper !== '') {
      selector = wrapper + ' ' + selector;
    }

    if (name == 'password') {
      $('[name=password]').val('');
      $('[name=password_confirmation]').val('');
    }

    if ($(selector).parent().hasClass('input-group')) {
      if (el[0].includes('.')) {
        selector = $(selector).get(child);
      }

      $(selector).parent().next('.text-danger').remove();
      $(selector).removeClass('is-invalid').addClass('is-invalid');
      $(selector).parent().after($errorMessage);
    } else {
      if (el[0].includes('.')) {
        selector = $(selector).get(child);
      }

      $(selector).next('.text-danger').remove();
      $(selector).removeClass('is-invalid').addClass('is-invalid').after($errorMessage);
    }
  });
};
/*
	Altera o estado de um botão passado,
	adicionando ou removendo o ícone de loading
*/

var loadingBtn = function loadingBtn(btn, add) {
  if (add) {
    btn.attr('disabled', 'disabled');
    btn.find('i').hide();
    btn.prepend('<span class="spinner-border spinner-border-sm mr-1"></span>');
  } else {
    btn.removeAttr('disabled');
    btn.find('.spinner-border').remove();
    btn.find('i').show();
  }
};
/*
	Retorna a URL atual sem parâmetros.
*/

var getLocationURL = function getLocationURL() {
  return window.location.protocol + '//' + window.location.host + window.location.pathname;
};
/*
	Scrolla para o elemento informado.
*/

var scrollToElement = function scrollToElement(element) {
  var duration = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 400;
  $('html, body').animate({
    scrollTop: element.offset().top - 100
  }, duration);
};
/*
	Abre a URL passada em uma nova guia.
*/

var openInNewTab = function openInNewTab(href) {
  Object.assign(document.createElement('a'), {
    target: '_blank',
    href: href
  }).click();
};

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!****************************************!*\
  !*** ./resources/js/partials/users.js ***!
  \****************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @/helpers */ "./resources/js/helpers.js");

$('#btnRegisterUser').on('click', function (e) {
  e.preventDefault();
  var $btn = $(this);
  (0,_helpers__WEBPACK_IMPORTED_MODULE_0__.loadingBtn)($btn, true);
  axios.post((0,_helpers__WEBPACK_IMPORTED_MODULE_0__.getLocationURL)(), {
    name: $('[name=name]').val(),
    email: $('[name=email]').val(),
    password: $('[name=password').val(),
    password_confirmation: $('[name=password_confirmation]').val(),
    role_id: $('[name=role_id]').val()
  }).then(function (response) {
    window.location = response.data.redirect;
  })["catch"](function (error) {
    (0,_helpers__WEBPACK_IMPORTED_MODULE_0__.dispatchErrorMessages)(error.response.data.errors);
    (0,_helpers__WEBPACK_IMPORTED_MODULE_0__.loadingBtn)($btn, false);
  });
});
$('.btn-delete-user').on('click', function (e) {
  e.preventDefault();
  var id = $(this).parents('[data-id]').attr('data-id');
  Swal.fire({
    icon: 'error',
    iconHtml: '<i class="fas fa-trash-alt"></i>',
    title: 'Tem certeza?',
    html: '<div class="text-center">O usuário será deletado</div>',
    showCancelButton: true,
    confirmButtonText: 'Tenho',
    cancelButtonText: 'Cancelar'
  }).then(function (result) {
    if (result.isConfirmed) {
      axios["delete"](window.location.href + '/' + id + '/deletar').then(function (response) {
        window.location = response.data.redirect;
      });
    }
  });
});
$('.btn-change-role').on('click', function (e) {
  e.preventDefault();
  var id = $(this).parents('[data-id]').attr('data-id');
  axios.get(window.location.href + '/' + id + '/get-change-role-form').then(function (response) {
    $('#changeRoleModal .modal-body').html(response.data.view);
  });
});
$(document).on('click', '#btnSaveChangedRole', function (e) {
  e.preventDefault();
  var id = $(this).attr('data-id');
  var $btn = $(this);
  (0,_helpers__WEBPACK_IMPORTED_MODULE_0__.loadingBtn)($btn, true);
  axios.post(window.location.href + '/' + id + '/change-role', {
    role_id: $('[name=role_id_change]').val()
  }).then(function (response) {
    window.location = response.data.redirect;
  })["catch"](function (error) {
    (0,_helpers__WEBPACK_IMPORTED_MODULE_0__.dispatchErrorMessages)(error.response.data.errors);
    (0,_helpers__WEBPACK_IMPORTED_MODULE_0__.loadingBtn)($btn, false);
  });
});
})();

/******/ })()
;