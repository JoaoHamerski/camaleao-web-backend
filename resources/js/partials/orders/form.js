import {
  applyCleave,
  cleaveNumericInt,
  cleaveValueBRL,
  cleaveDate
} from '@/cleave'

import {
  loadingBtn,
  getLocationURL,
  dispatchErrorMessages
} from '@/helpers'

applyCleave($('[name=quantity]'), cleaveNumericInt(4))
applyCleave($('[name=quantity]'), cleaveNumericInt(4))
applyCleave($('[name=price]'), cleaveValueBRL)
applyCleave($('[name=down_payment]'), cleaveValueBRL)
applyCleave($('[name=delivery_date]'), cleaveDate)
applyCleave($('[name=production_date]'), cleaveDate)

$(document).on('input', 'input[type=file]', function() {
  const files = $(this)[0].files || null
  const names = []

  if (files.length > 0) {
    Object.entries(files).forEach(function(el) {
      names.push(el[1].name)
    })

    $(this).next('.custom-file-label').html(names.reverse().join(', '))
  } else {
    $(this).next('.custom-file-label').html('Escolher arquivos')
  }
})

$('#btnCreateOrder').on('click', function(e) {
  e.preventDefault()

  const formData = new FormData(document.querySelector('#formCreateOrder'))
  const $btn = $(this)

  loadingBtn($btn, true)

  axios.post(getLocationURL(), formData, { headers: { 'Content-Type': 'multipart/form-data' }})
    .then(response => {
      window.location = response.data.redirect
    })
    .catch(error => {
      dispatchErrorMessages(error.response.data.errors)
      loadingBtn($btn, false)
    })
})

$('#btnUpdateOrder').on('click', function(e) {
  e.preventDefault()

  const $btn = $(this)

  const formData = new FormData(document.querySelector('#formUpdateOrder'))

  loadingBtn($btn, true)

  axios.post(getLocationURL(), formData, { headers: { 'Content-Type': 'multipart/form-data' }})
    .then(response => {
      window.location = response.data.redirect
    })
    .catch(error => {
      dispatchErrorMessages(error.response.data.errors)
      loadingBtn($btn, false)
    })
})

$('.btn-delete-image, .btn-delete-payment-voucher').on('click', function(e) {
  e.preventDefault()

  const filepath = $(this).prev().attr('src') || $(this).prev().attr('href')
  let $wrapper

  if ($(this).hasClass('btn-delete-payment-voucher')) {
    $wrapper = $(this).closest('.list-group-item')
  } else {
    $wrapper = $(this).closest('.col-md-3')
  }

  axios.post(getLocationURL() + '/delete-file', {
    filepath: filepath
  })
    .then(() => {
      $wrapper.remove()
    })
})

$('[name=down_payment]').on('input', function() {
  if ($(this).val().replace('R$ ', '') == '') {
    $('[name=payment_via_id]').attr('disabled', 'disabled')
  } else {
    $('[name=payment_via_id]').removeAttr('disabled')
  }
})
