import { applyCleave, cleavePhone } from '@/cleave'
import { loadingBtn, dispatchErrorMessages, getLocationURL } from '@/helpers'

applyCleave($('[name=phone]'), cleavePhone)

$('#btnCreateClient').click(function(e) {
  e.preventDefault()
  const $btn = $(this)

  loadingBtn($btn, true)

  axios.post('/novo-cliente', {
    name: $('[name=name]').val(),
    phone: $('[name=phone]').val(),
    city: $('[name=city]').val()
  })
    .then(response => {
      $('#clientCreateModal').modal('hide')
      window.location = response.data.redirect
    })
    .catch(error => {
      dispatchErrorMessages(error.response.data.errors)
      loadingBtn($btn, false)
    })
})

$('#btnEditClient').click(function(e) {
  e.preventDefault()

  const $btn = $(this)

  loadingBtn($btn, true)

  axios.patch(getLocationURL(), {
    name: $('[name=name]').val(),
    phone: $('[name=phone]').val(),
    city: $('[name=city]').val()
  })
    .then(response => {
      $('#clientEditModal').modal('hide')
      window.location = response.data.redirect
    })
    .catch(error => {
      dispatchErrorMessages(error.response.data.errors)
      loadingBtn($btn, false)
    })
})

$('#btnDeleteClient').click(e => {
  e.preventDefault()

  Swal.fire({
    icon: 'error',
    iconHtml: '<i class="fas fa-trash-alt"></i>',
    title: 'Tem certeza?',
    html: '<div class="text-center">Todos os pedidos, pagamentos e anexos do cliente serão deletados também</div>',
    showCancelButton: true,
    confirmButtonText: 'Tenho',
    cancelButtonText: 'Cancelar'
  })
    .then(result => {
      if (result.isConfirmed) {
        $('#content').prepend($('<div class="loading-page"><div class="spinner-border text-primary"></div></div>'))

        axios.delete(getLocationURL())
          .then(response => {
            window.location = response.data.redirect
          })
      }
    })
})
