!function(e){var t={};function n(o){if(t[o])return t[o].exports;var r=t[o]={i:o,l:!1,exports:{}};return e[o].call(r.exports,r,r.exports,n),r.l=!0,r.exports}n.m=e,n.c=t,n.d=function(e,t,o){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:o})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var o=Object.create(null);if(n.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var r in e)n.d(o,r,function(t){return e[t]}.bind(null,r));return o},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="/",n(n.s=5)}({5:function(e,t,n){e.exports=n("8fxR")},"8fxR":function(e,t){$("#btnUpdateUser").on("click",(function(e){e.preventDefault(),$btn=$(this),loadingBtn($btn,!0),axios.patch(getLocationURL(),{name:$("[name=name]").val(),email:$("[name=email]").val(),password:$("[name=password]").val(),password_confirmation:$("[name=password_confirmation]").val()}).then((function(e){console.log(e.data),window.location=e.data.redirect})).catch((function(e){console.log(e.response),loadingBtn($btn,!1),dispatchErrorMessages(e.response.data.errors)}))})),$("#btnDeleteAccount").on("click",(function(e){e.preventDefault(),Swal.fire({icon:"error",iconHtml:'<i class="fas fa-trash-alt"></i>',title:"Tem certeza?",html:'<div class="text-center">Sua conta não poderá ser recuperada</div>',showCancelButton:!0,confirmButtonText:"Tenho",cancelButtonText:"Cancelar"}).then((function(e){e.isConfirmed&&axios.delete(getLocationURL()+"/deletar").then((function(e){window.location=e.data.redirect})).catch((function(e){console.log(e.response)}))}))}))}});