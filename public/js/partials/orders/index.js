!function(e){var t={};function n(r){if(t[r])return t[r].exports;var o=t[r]={i:r,l:!1,exports:{}};return e[r].call(o.exports,o,o.exports,n),o.l=!0,o.exports}n.m=e,n.c=t,n.d=function(e,t,r){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(n.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var o in e)n.d(r,o,function(t){return e[t]}.bind(null,o));return r},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="/",n(n.s=5)}({5:function(e,t,n){e.exports=n("oTJk")},oTJk:function(e,t){applyCleave($("[name*=data]"),cleaveDate),$("#formGenerateReport button[type=submit]").on("click",(function(e){e.preventDefault();var t=$(this);loadingBtn(t,!0),axios.get(getLocationURL()+"/relatorio",{params:{cidade:$("[name=cidade]").val(),status:$("[name=status]").val()}}).then((function(e){$("#formGenerateReport").submit()})).catch((function(e){dispatchErrorMessages(e.response.data.errors)})).then((function(){loadingBtn(t,!1)}))})),$("#formGenerateReportProduction button[type=submit]").on("click",(function(e){e.preventDefault();var t=$(this);loadingBtn(t,!0),axios.get(getLocationURL()+"/relatorio-data-producao",{params:{data_de_producao:$("[name=data_de_producao]").val()}}).then((function(e){$("#formGenerateReportProduction").submit()})).catch((function(e){dispatchErrorMessages(e.response.data.errors)})).then((function(){loadingBtn(t,!1)}))}));var n=$('[name="data_de_fechamento"]');setInterval((function(){""==n.val()?($('[name="em_aberto"]').removeAttr("disabled"),$('[name="em_aberto"]')[0].checked=!0):($('[name="em_aberto"]').attr("disabled","disabled"),$('[name="em_aberto"').prop("checked",!1))}),100)}});