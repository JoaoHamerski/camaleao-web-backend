!function(e){var t={};function n(o){if(t[o])return t[o].exports;var a=t[o]={i:o,l:!1,exports:{}};return e[o].call(a.exports,a,a.exports,n),a.l=!0,a.exports}n.m=e,n.c=t,n.d=function(e,t,o){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:o})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var o=Object.create(null);if(n.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var a in e)n.d(o,a,function(t){return e[t]}.bind(null,a));return o},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="/",n(n.s=3)}({3:function(e,t,n){e.exports=n("JaHu")},JaHu:function(e,t){$("a[data-option]").on("click",(function(e){e.preventDefault();var t=$(this).attr("data-option");axios.post(window.location.href+"/file-view",{option:t}).then((function(e){$("#fileViewerModal").find(".modal-body").html(e.data.view),$("#fileViewerModal").modal("show"),console.log(e)})).catch((function(e){console.log(e.response)}))})),$("#btnDeleteOrder").on("click",(function(e){e.preventDefault(),Swal.fire({icon:"error",iconHtml:'<i class="fas fa-trash-alt"></i>',title:"Tem certeza?",html:'<div class="text-center">Todos os anexos e pagamentos serão excluídos também</div>',showCancelButton:!0,confirmButtonText:"Tenho",cancelButtonText:"Cancelar"}).then((function(e){e.isConfirmed&&($("#content").prepend($('<div class="loading-page"><div class="spinner-border text-primary"></div></div>')),axios.delete(window.location.href).then((function(e){window.location=e.data.redirect})))}))})),$("#btnAddNote").on("click",(function(e){e.preventDefault();var t=$(this);loadingBtn(t,!0),axios.post(window.location.href+"/new-note",{order_note:$("[name=order_note]").val()}).then((function(e){$("#listGroupNotes").append(e.data.noteListItem),$('button[data-target="#notesModal"]').html("Anotações ("+e.data.countNotes+")"),$("[name=order_note").val("")})).catch((function(e){console.log(e.response),dispatchErrorMessages(e.response.data.errors)})).then((function(){loadingBtn(t,!1)}))})),$(document).on("click",".btn-delete-item",(function(e){e.preventDefault();var t=$(this).parents("[data-id]"),n=t.attr("data-id");axios.delete(window.location.href+"/delete-note/"+n).then((function(e){t.remove(),$('button[data-target="#notesModal"]').html("Anotações ("+e.data.countNotes+")")})).catch((function(e){}))})),applyCleave($("[name=value]"),cleaveValueBRL),applyCleave($("[name=date]"),cleaveDate),$("#today").on("click",(function(e){e.preventDefault();var t=new Date,n="",o=t.getMonth()+1;n+=t.getDate()+"/",n+=o<10?"0"+o:o,n+="/",n+=t.getFullYear(),$("[name=date]").val(n).focus()})),$("#btnAddPayment").on("click",(function(e){e.preventDefault(),$btn=$(this);$(this).attr("data-id");loadingBtn($btn,!0),axios.post(window.location.href+"/new-payment",{value:$("[name=value]").val(),date:$("[name=date]").val(),note:$("[name=note]").val()}).then((function(e){window.location=e.data.redirect})).catch((function(e){console.log(e.response),dispatchErrorMessages(e.response.data.errors)})).then((function(){loadingBtn($btn,!1)}))}))}});