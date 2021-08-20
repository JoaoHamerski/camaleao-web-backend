
/*
	Seta um cookie, em que "data"
	é informado o "name" e "value" do cookie.
*/
export const setCookie = function(data) {
    axios.post('/set-cookie', data)
}

/*
	Deleta um cookie com o nome especificado.
*/
export const destroyCookie = function(name) {
    axios.delete('/destroy-cookie', {name: name})
}

/*
	Sanitiza o valor em dinheiro
	Ex.: R$ 123,45 => 123.45
*/
export const sanitizeMoney = function(str) {
    str = str.replace(/\./g, '')
    str = str.replace(',', '.')
    str = str.replace('R$', '')

    return str.trim()
}

/*
	Exibe todas as mensagens de erro nos inputs automaticamente,
	que foram recebidas do servidor.
*/
export const dispatchErrorMessages = function(errors, wrapper = '') {
    scrollToElement($('[name*=' + Object.entries(errors)[0][0].split('.')[0] + ']'))

    Object.entries(errors).forEach(function(el) {
        const $errorMessage = $('<div class="text-danger small">' + el[1][0] + '</div>')
        let name = el[0],
            child,
            selector = '[name=' + name + ']'

        if (name.includes('.')) {
            child = name.split('.')[1]
            name = name.split('.')[0]
            selector = '[name^=' + name + ']'
        }

        if (wrapper !== '') {
            selector = wrapper + ' ' + selector
        }

        if (name == 'password') {
            $('[name=password]').val('')
            $('[name=password_confirmation]').val('')
        }

        if ($(selector).parent().hasClass('input-group')) {
            if (el[0].includes('.')) {
                selector = $(selector).get(child)
            }

            $(selector).parent().next('.text-danger').remove()

            $(selector).removeClass('is-invalid')
                .addClass('is-invalid')

            $(selector).parent().after($errorMessage)
        } else {
            if (el[0].includes('.')) {
                selector = $(selector).get(child)
            }

            $(selector).next('.text-danger').remove()
            $(selector)
                .removeClass('is-invalid')
                .addClass('is-invalid')
                .after($errorMessage)
        }
    })
}

/*
	Altera o estado de um botão passado,
	adicionando ou removendo o ícone de loading
*/
export const loadingBtn = function(btn, add) {

    if (add) {
        btn.attr('disabled', 'disabled')
        btn.find('i').hide()
        btn.prepend('<span class="spinner-border spinner-border-sm mr-1"></span>')
    } else {
        btn.removeAttr('disabled')
        btn.find('.spinner-border').remove()
        btn.find('i').show()
    }
}

/*
	Retorna a URL atual sem parâmetros.
*/
export const getLocationURL = function() {
    return window.location.protocol + '//' + window.location.host + window.location.pathname
}

/*
	Scrolla para o elemento informado.
*/
export const scrollToElement = function(element, duration = 400) {
    $('html, body').animate({
        scrollTop: element.offset().top - 100
    }, duration)
}

/*
	Abre a URL passada em uma nova guia.
*/
export const openInNewTab = function(href) {
    Object.assign(document.createElement('a'), {
        target: '_blank',
        href: href,
    }).click()
}
