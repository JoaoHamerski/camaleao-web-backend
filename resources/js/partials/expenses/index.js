import {
    applyCleave,
    cleaveValueBRL,
    cleaveDate
} from '@/cleave'

import {
    loadingBtn,
    getLocationURL,
    dispatchErrorMessages
} from '@/helpers'
applyCleave($('[name=value]'), cleaveValueBRL)
applyCleave($('[name*=date]'), cleaveDate)
applyCleave($('[name*=dia]'), cleaveDate)


/*
  Aplica as máscaras nos inputs caso haja alguma alteração no modal
  de formulário.
*/
const target = document.querySelector('#expensesEditModal .modal-body')

const observer = new MutationObserver(() => {
    applyCleave($('[name=value]'),  cleaveValueBRL)
    applyCleave($('[name=date]'), cleaveDate)
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
        $(this).next('.custom-file-label').html('Selecione o comprovante')
    }
})

observer.observe(target, {attributes: true, childList: true, characterData: true})

/*
  Adiciona um novo tipo de despesa ao clicar no botão de adicionar tipo de despesa
*/
$('#btnAddExpenseType').on('click', function(e) {
    e.preventDefault()

    const $btn = $(this)

    loadingBtn($btn, true)

    axios.post(getLocationURL() + '/tipo-de-despesa', {
        expense_type: $('[name=expense_type]').val()
    })
        .then(response => {
            $('.modal-body .list-group').append(response.data.view)
            $('[name=expense_type]').val('')
        })
        .catch(error => {
            dispatchErrorMessages(error.response.data.errors)
        })
        .then(function() {
            loadingBtn($btn, false)
        })
})

/*
  Deleta o tipo de despesa
*/
$(document).on('click', '.btn-delete-expense-type', function(e) {
    e.preventDefault()

    Swal.fire({
        icon: 'error',
        iconHtml: '<i class="fas fa-exclamation-triangle"></i>',
        title: 'Cuidado',
        html: `
    <div class="text-center">
      Ao deletar esse tipo de despesa você terá que alterar todas as despesas que estavam cadastradas nesse tipo para um tipo existente
      </div>
      <div class="font-weight-bold text-center mt-3">Você tem certeza?</div>
    `,
        showCancelButton: true,
        confirmButtonText: 'Tenho',
        cancelButtonText: 'Cancelar'
    })
        .then(result => {
            if (result.isConfirmed) {
                const id = $(this).parents('[data-id]').attr('data-id')
                const spinner = `
        <div class="loading-page">
            <div class="spinner-border text-primary"></div>
        </div>
      `

                $('.modal-body').prepend($(spinner))

                axios.delete(getLocationURL() + '/tipo-de-despesa/' + id + '/deletar')
                    .then(() => {
                        $('[data-id=' + id + ']').remove()
                    })
                    .catch(() => {})
                    .then(() => {
                        $('.loading-page').remove()
                    })

            }
        })
})

/*
  Edita o tipo de despesa
*/

$(document).on('click' ,'.btn-edit-expense-type', function(e) {
    e.preventDefault()

    const id = $(this).parents('[data-id]').attr('data-id')
    const expenseTypeName = $(this).parents('[data-id]').find('.expense-type-name').text().trim()

    const input = $(`
    <div class="col px-0">
      <form>
        <div class="input-group">
          <input class="form-control" name="expense_type_updated" value="${expenseTypeName}">
          <div class="input-group-append">
            <button class="btn btn-outline-primary btn-update-expense-type">Concluído</button>
          </div>
        </div>
      </form>
    </div>
  `)

    $(this).parents('[data-id=' + id + ']').html(input)
    $('[name=expense_type_updated]').focus()
})

/*
  Atualiza a despesa que foi editada ao clicar no botão "concluído"
*/
$(document).on('click', '.btn-update-expense-type', function(e) {
    e.preventDefault()

    const $btn = $(this)
    const id = $(this).parents('[data-id]').attr('data-id')

    loadingBtn($btn, true)

    axios.patch(getLocationURL() + '/tipo-de-despesa/' + id, {
        expense_type_updated: $btn.parents('.input-group').find('input').val()
    })
        .then(response => {
            $('#expenseTypesModal [data-id=' + id + ']').html($(response.data.view).children())
        })
        .catch(error => {
            dispatchErrorMessages(error.response.data.errors)
        })
        .then(function() {
            loadingBtn($btn, false)
        })
})


/*
  Deleta uma despesa
*/

$('.btn-delete').on('click', function(e) {
    e.preventDefault()

    const id = $(this).parents('[data-id]').attr('data-id')
    const $btn = $(this)


    Swal.fire({
        icon: 'error',
        iconHtml: '<i class="fas fa-trash-alt"></i>',
        title: 'Tem certeza?',
        html: `
    <div class="text-center">
      Isso irá deletar a despesa
      </div>
    `,
        showCancelButton: true,
        confirmButtonText: 'Tenho',
        cancelButtonText: 'Cancelar'
    })
        .then(result => {
            if (result.isConfirmed) {
                loadingBtn($btn, true)
                axios.delete(getLocationURL() + '/' + id + '/deletar')
                    .then(response => {
                        window.location = response.data.redirect

                        loadingBtn($btn, false)
                    })
            }
        })
})

/*
  Cria uma única despesa
*/

$('#btnCreateUniqueExpense').on('click', function(e) {
    e.preventDefault()

    const $btn = $(this)

    loadingBtn($btn, true)

    const formData = new FormData($(this).parents('form').get(0))

    axios.post(getLocationURL() + '/cadastro', formData)
        .then(response => {
            window.location = response.data.redirect
        })
        .catch(error => {
            console.log(error.response)
            dispatchErrorMessages(error.response.data.errors, '#createFormModal')
            loadingBtn($btn, false)
        })
})

/*
  Atualiza a despesa caso clique no botão de atualizar despesa
*/
$(document).on('click', '#btnUpdateExpense', function(e) {

    e.preventDefault()

    const id = $(this).attr('data-id')
    const $btn = $(this)

    loadingBtn($btn, true)

    const formData = new FormData($(this).parents('form').get(0))

    axios.post(getLocationURL() + '/' + id, formData)
        .then(response => {
            window.location = response.data.redirect
        })
        .catch(error => {
            dispatchErrorMessages(error.response.data.errors, '#editFormModal')
            loadingBtn($btn)
        })
})

/*
  Abre o modal de atualizar despesas ao clicar no botão de editar despesa
*/
$('.btn-edit').on('click', function() {
    const id = $(this).parents('[data-id]').attr('data-id')

    axios.get(getLocationURL() + '/' + id + '/get-edit-form')
        .then(response => {
            $('#expensesEditModal .modal-body').html(response.data.view)
        })
})

/*
  Valida e dá submit no relatório das despesas caso validado.
*/
$('#btnGenerateReport').on('click', function(e) {
    e.preventDefault()

    const $btn = $(this)

    loadingBtn($btn, true)

    axios.get(getLocationURL() + '/relatorio', {
        params: {
            dia_inicial: $('[name=dia_inicial]').val(),
            dia_final: $('[name=dia_final]').val()
        }
    })
        .then(() => {
            $('#reportForm').submit()
        })
        .catch(error => {
            dispatchErrorMessages(error.response.data.errors)
        })
        .then(function() {
            loadingBtn($btn, false)
        })
})

$(document).on('click', '#deleteReceipt', function(e) {
    e.preventDefault()

    const id = $(this).parents('[data-id]').attr('data-id')
    const $btn = $(this)

    axios.delete(getLocationURL() + '/' + id + '/delete-receipt')
        .then(() => {
            $btn.parents('[data-id]').remove()
        })
})

$('.btn-view-receipt').on('click', function(e) {
    e.preventDefault()

    const id = $(this).parents('[data-id]').attr('data-id')

    axios.get(getLocationURL() + '/' + id + '/get-view-receipt')
        .then(response => {
            $('#viewReceiptModal .modal-body').html(response.data.view)
        })
})

$(document).on('change', '[name=expense_type_id]', function() {
    const formGroup = `
    <div class="form-group">
      <label for="employee_name" id="employee_name" class="font-weight-bold">Nome do funcionário</label>
      <small class="text-secondary">(opcional)</small>
      <input type="text" name="employee_name" class="form-control">
    </div>
  `

    const text = $('[name=expense_type_id] option:selected').text().trim()

    if (text.toUpperCase() == 'mão de obra'.toUpperCase()) {
        $(this).parents('.form-group').after($(formGroup))
    } else {
        $('[name=employee_name]').parents('.form-group').remove()
    }
})
