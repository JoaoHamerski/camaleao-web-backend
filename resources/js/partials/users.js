import {
  getLocationURL,
  loadingBtn,
  dispatchErrorMessages
} from '@/helpers'

$('#btnRegisterUser').on('click', function(e) {
  e.preventDefault()

  const $btn = $(this)

  loadingBtn($btn, true)

  axios.post(getLocationURL(), {
    name: $('[name=name]').val(),
    email: $('[name=email]').val(),
    password: $('[name=password').val(),
    password_confirmation: $('[name=password_confirmation]').val(),
    role_id: $('[name=role_id]').val()
  })
    .then(response => {
      window.location = response.data.redirect
    })
    .catch(error => {
      dispatchErrorMessages(error.response.data.errors)
      loadingBtn($btn, false)
    })
})

$('.btn-delete-user').on('click', function(e) {
  e.preventDefault()

  const id = $(this).parents('[data-id]').attr('data-id')

  Swal.fire({
    icon: 'error',
    iconHtml: '<i class="fas fa-trash-alt"></i>',
    title: 'Tem certeza?',
    html: '<div class="text-center">O usuário será deletado</div>',
    showCancelButton: true,
    confirmButtonText: 'Tenho',
    cancelButtonText: 'Cancelar'
  })
    .then(result => {
      if (result.isConfirmed) {
        axios.delete(window.location.href + '/' + id + '/deletar')
          .then(response => {
            window.location = response.data.redirect
          })
      }
    })
})

$('.btn-change-role').on('click', function(e) {
  e.preventDefault()

  const id = $(this).parents('[data-id]').attr('data-id')

  axios.get(window.location.href + '/' + id + '/get-change-role-form')
    .then(response => {
      $('#changeRoleModal .modal-body').html(response.data.view)
    })
})

$(document).on('click', '#btnSaveChangedRole', function(e) {
  e.preventDefault()

  const id = $(this).attr('data-id')
  const $btn = $(this)

  loadingBtn($btn, true)

  axios.post(window.location.href + '/' + id + '/change-role', {
    role_id: $('[name=role_id_change]').val()
  })
    .then(response => {
      window.location = response.data.redirect
    })
    .catch(error => {
      dispatchErrorMessages(error.response.data.errors)
      loadingBtn($btn, false)
    })
})
