import {
  getLocationURL,
  loadingBtn,
  dispatchErrorMessages
} from '@/helpers'

$('[data-target="#changePaymentModal"]').on('click', function(e) {
  e.preventDefault()

  const id = $(this).parents('[data-id]').attr('data-id')

  axios.get(getLocationURL() + '/pagamento/' + id + '/get-change-payment-view')
    .then(response => {
      $('#changePaymentModal .modal-body').html(response.data.view)
    })
    .catch(error => {
      console.log(error.response)
    })
})

$(document).on('click', '#btnChangePayment', function(e) {
  e.preventDefault()

  const id = $(this).attr('data-id'),
    $btn = $(this),
    formData = new FormData($(this).parents('form').get(0))

  loadingBtn($btn, true)

  axios.post(getLocationURL() + '/pagamento/' + id, formData)
    .then(response => {
      window.location = response.data.redirect
    })
    .catch(error => {
      dispatchErrorMessages(error.response.data.errors)
      loadingBtn($btn, false)
    })
})
