!function(e){var t={};function n(a){if(t[a])return t[a].exports;var o=t[a]={i:a,l:!1,exports:{}};return e[a].call(o.exports,o,o.exports,n),o.l=!0,o.exports}n.m=e,n.c=t,n.d=function(e,t,a){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:a})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var a=Object.create(null);if(n.r(a),Object.defineProperty(a,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var o in e)n.d(a,o,function(t){return e[t]}.bind(null,o));return a},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="/",n(n.s=9)}({9:function(e,t,n){e.exports=n("eQzh")},eQzh:function(e,t){function n(){var e=0;return $("[name*=value]").each((function(){e+=+sanitizeMoney($(this).val())})),e}applyCleave($("[name*=value]"),cleaveValueBRL),applyCleave($("[name*=date]"),cleaveDate),applyCleave($("[name=all_date]"),cleaveDate);var a=document.querySelector("#formExpenses");new MutationObserver((function(e){applyCleave($("[name*=value]").last(),cleaveValueBRL),applyCleave($("[name*=date]").last(),cleaveDate),""!==$("[name=all_date]").val().trim()&&$("[name*=date]").last().val($("[name=all_date]").val())})).observe(a,{attributes:!0,childList:!0,characterData:!0}),$("#btnNewExpense").on("click",(function(e){e.preventDefault(),$btn=$(this),loadingBtn($(this),!0),axios.get(getLocationURL()+"/get-inline-form").then((function(e){$("#btnNewExpense").parent().before(e.data.view)})).catch((function(e){})).then((function(){loadingBtn($btn,!1)}))})),$(document).on("input","input[type=file]",(function(){var e=$(this)[0].files||null,t=[];e.length>0?(Object.entries(e).forEach((function(e){t.push(e[1].name)})),$(this).next(".custom-file-label").html(t.reverse().join(", "))):$(this).next(".custom-file-label").html("Comprovante")})),$("#formExpenses").on("input","[name*=value]",(function(e){sanitizeMoney($(this).val()),n();var t=new Intl.NumberFormat("pt-BR",{style:"currency",currency:"BRL"});$("#totalValue").text(t.format(n()))})),$("[name=all_date]").on("input focus",(function(){$("[name*=date]").val($(this).val())})),$("#formExpenses").on("click",".btn-delete",(function(e){e.preventDefault(),$(this).closest(".form-inline-wrapper").remove()})),$('#formExpenses button[type="submit"]').on("click",(function(e){e.preventDefault();var t=new FormData(document.querySelector("#formExpenses")),n=$(this);loadingBtn(n,!0),axios.post(getLocationURL(),t).then((function(e){window.location=e.data.redirect})).catch((function(e){loadingBtn(n,!1),dispatchErrorMessages(e.response.data.errors)}))}))}});