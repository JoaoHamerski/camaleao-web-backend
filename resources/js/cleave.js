import Cleave from 'cleave.js'

require('cleave.js/dist/addons/cleave-phone.br.js')

// Função que aplica a mascara de cleave.js
export const applyCleave = function (elements, options) {
  elements.each(function() {
    new Cleave(this, options)
  })
}

export const cleaveDate = {
  date: true,
  delimiter: '/',
  datePattern: ['d', 'm', 'Y']
}

export const cleavePhone = {
  phone: true,
  phoneRegionCode: 'BR'
}

export const cleaveNumericInt = function(length) {
  return {
    blocks: [length],
    numericOnly: true,
    numeralDecimalScale: 1
  }
}

export const cleaveValueBRL = {
  numeral: true,
  numeralDecimalMark: ',',
  delimiter: '.',
  prefix: 'R$ ',
  numeralPositiveOnly: true
}
