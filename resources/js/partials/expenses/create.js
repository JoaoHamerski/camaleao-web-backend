import { applyCleave, cleaveDate, cleaveValueBRL} from '@/cleave'
import { sanitizeMoney, loadingBtn, getLocationURL, dispatchErrorMessages  } from '@/helpers'
/*
	Aplica as máscaras nos inputs iniciais da página
*/
applyCleave($('[name*=value]'), cleaveValueBRL)
applyCleave($('[name*=date]'), cleaveDate)
applyCleave($('[name=all_date]'), cleaveDate)

/*
	Pega a soma do valor total dos inputs de valor
*/
function getTotalValue() {
  let total = 0

  $('[name*=value]').each(function() {
    total += +sanitizeMoney($(this).val())
  })

  return total
}

/*
	Toda vez que houver alteração, ou seja, toda vez que um novo
	formulário em linha for adicionado, é aplicado as máscaras de input
	nesse formulário.
*/
const target = document.querySelector('#formExpenses')

const observer = new MutationObserver(() => {
  applyCleave($('[name*=value]').last(),  cleaveValueBRL)
  applyCleave($('[name*=date]').last(), cleaveDate)

  if ($('[name=all_date]').val().trim() !== '') {
    $('[name*=date]').last().val($('[name=all_date]').val())
  }
})

observer.observe(target, {attributes: true, childList: true, characterData: true})

/*
	Adiciona um novo formulário em linha quando clicado no botão de mais
*/
$('#btnNewExpense').on('click', function(e) {
  e.preventDefault()

  const $btn = $(this)

  loadingBtn($(this), true)

  let index = $('.form-inline-wrapper').last().attr('data-index')

  index = isNaN(index) ? 0 : index

  axios.get(getLocationURL() + '/get-inline-form', {
    params: {
      index: +index + 1
    }
  })
    .then(response => {
      $('#btnNewExpense').parent().before(response.data.view)
    })
    .catch(() => {})
    .then(function() {
      loadingBtn($btn, false)
    })
})

$(document).on('input', 'input[type=file]', function() {
  const files = $(this)[0].files || null
  const names = []

  if (files.length > 0) {
    Object.entries(files).forEach(function(el) {
      names.push(el[1].name)
    })

    $(this).next('.custom-file-label').html(names.reverse().join(', '))
  } else {
    $(this).next('.custom-file-label').html('Comprovante')
  }
})

/*
	Atualiza o valor total de despesas ao alterar qualquer input value
*/
$('#formExpenses').on('input', '[name*=value]', function() {
  const formatter = new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  })

  $('#totalValue').text(formatter.format(getTotalValue()))
})

$('[name=all_date]').on('input focus', function() {
  $('[name*=date]').val($(this).val())
})

/*
	Deleta o elemento responsável pelo formulário em linha
*/
$('#formExpenses').on('click', '.btn-delete', function(e) {
  e.preventDefault()

  $(this).closest('.form-inline-wrapper').remove()
})

/*
	Envia os dados para o servidor para verificar se há erros,
	se não houver, redireciona para a página retornada pelo servidor.
*/
$('#formExpenses button[type="submit"]').on('click', function(e) {
  e.preventDefault()

  const formData = new FormData(document.querySelector('#formExpenses'))
  const $btn = $(this)

  loadingBtn($btn, true)

  axios.post(getLocationURL(), formData)
    .then(response => {
      window.location = response.data.redirect
    })
    .catch(error => {
      loadingBtn($btn, false)
      dispatchErrorMessages(error.response.data.errors)
    })
})

$(document).on('change', '[name*=expense_type_id]', function() {
  const dataIndex = $(this).parents('.form-inline-wrapper').attr('data-index')
  const formGroup = `
		<div class="form-row">
			<div class="form-group col col-md-4">
				<input type="text" class="form-control" name="employee_name[${dataIndex}]" placeholder="Nome do funcionário...">
			</div>
		</div>
	`

  const text = $(`[name="expense_type_id[${dataIndex}]"] option:selected`).text().trim()

  if (text.toUpperCase() == 'mão de obra'.toUpperCase()) {
    $(this).parents('.form-inline-wrapper').find('.form-row:nth-child(2)').after($(formGroup))
  } else {
    $(this).parents('.form-inline-wrapper').find('.form-row:nth-child(3)').remove()
  }
})
