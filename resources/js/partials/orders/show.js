import { applyCleave, cleaveValueBRL, cleaveDate } from '@/cleave'
import { getLocationURL, loadingBtn, dispatchErrorMessages } from '@/helpers'

applyCleave($('[name=value]'), cleaveValueBRL)
applyCleave($('[name=date]'), cleaveDate)

$('a[data-attach]').on('click', function(e) {
  e.preventDefault()

  const option = $(this).attr('data-attach')

  axios.post(getLocationURL() + '/file-view', {
    option: option
  })
    .then(response => {
      $('#fileViewerModal').find('.modal-body').html(response.data.view)
      $('#fileViewerModal').modal('show')
    })
})

$('#btnDeleteOrder').on('click', function(e) {
  e.preventDefault()

  Swal.fire({
    icon: 'error',
    iconHtml: '<i class="fas fa-trash-alt"></i>',
    title: 'Tem certeza?',
    html: '<div class="text-center">Todos os anexos e pagamentos serão excluídos também</div>',
    showCancelButton: true,
    confirmButtonText: 'Tenho',
    cancelButtonText: 'Cancelar'
  })
    .then(result => {
      if (result.isConfirmed) {

        $('#content').prepend($(`
        <div class="loading-page">
          <div class="spinner-border text-primary"></div>
        </div>
        `))

        axios.delete(getLocationURL() + '/deletar')
          .then(response => {
            window.location = response.data.redirect
          })
      }
    })
})

$('#btnAddNote').on('click', function(e) {
  e.preventDefault()
  const $btn = $(this)

  loadingBtn($btn, true)

  axios.post(getLocationURL() + '/new-note', {
    order_note: $('[name=order_note]').val()
  })
    .then(response => {
      $('#listGroupNotes').append(response.data.noteListItem)
      $('button[data-target="#notesModal"]').html('Anotações (' + response.data.countNotes  + ')')

      $('[name=order_note').val('').focus()
    })
    .catch(error => {
      dispatchErrorMessages(error.response.data.errors)
    })
    .then(function() {
      loadingBtn($btn, false)
    })
})

$(document).on('click', '.btn-delete-item', function(e) {
  e.preventDefault()
  const $itemWrapper = $(this).parents('[data-id]')
  const id = $itemWrapper.attr('data-id')

  axios.delete(getLocationURL() + '/delete-note/' + id)
    .then(response => {
      $itemWrapper.remove()
      $('button[data-target="#notesModal"]').html('Anotações (' + response.data.countNotes  + ')')
    })
})

$('#btnAddPayment').on('click', function(e) {
  e.preventDefault()

  const $btn = $(this),
    formData = new FormData($btn.parents('form').get(0))

  loadingBtn($btn, true)

  axios.post(getLocationURL() + '/new-payment', formData)
    .then(response => {
      window.location = response.data.redirect
    })
    .catch(error => {
      dispatchErrorMessages(error.response.data.errors)
    })
    .then(function() {
      loadingBtn($btn, false)
    })
})
