applyCleave($("[name=phone]"),cleavePhone),$("#btnCreateClient").click((function(e){e.preventDefault(),$btn=$(this),loadingBtn($btn,!0),axios.post("/novo-cliente",{name:$("[name=name]").val(),phone:$("[name=phone]").val(),city:$("[name=city]").val()}).then((function(e){$("#clientCreateModal").modal("hide"),window.location=e.data.redirect})).catch((function(e){dispatchErrorMessages(e.response.data.errors),loadingBtn($btn,!1)}))})),$("#btnEditClient").click((function(e){e.preventDefault(),$btn=$(this),loadingBtn($btn,!0),axios.patch(getLocationURL(),{name:$("[name=name]").val(),phone:$("[name=phone]").val(),city:$("[name=city]").val()}).then((function(e){$("#clientEditModal").modal("hide"),window.location=e.data.redirect})).catch((function(e){dispatchErrorMessages(e.response.data.errors),loadingBtn($btn,!1)}))})),$("#btnDeleteClient").click((function(e){e.preventDefault(),Swal.fire({icon:"error",iconHtml:'<i class="fas fa-trash-alt"></i>',title:"Tem certeza?",html:'<div class="text-center">Todos os pedidos, pagamentos e anexos do cliente serão deletados também</div>',showCancelButton:!0,confirmButtonText:"Tenho",cancelButtonText:"Cancelar"}).then((function(e){e.isConfirmed&&($("#content").prepend($('<div class="loading-page"><div class="spinner-border text-primary"></div></div>')),axios.delete(getLocationURL()).then((function(e){window.location=e.data.redirect})).catch((function(e){console.log(e.response)})))}))}));