
// Seta um cookie, em que "data" é informado o "name" e "value" do cookie.
window.setCookie = function(data) {
	axios.post('/set-cookie', data);
}

// Deleta um cookie, em que "name" é informado o "name" do cookie a ser deletado.
window.destroyCookie = function(name) {
	axios.delete('/destroy-cookie', {name: name});
}

window.sanitizeMoney = function(str) {
	str = str.replace(/\./g, '');
	str = str.replace(',', '.');
	str = str.replace('R$', '');

	return str.trim();
}

/*
	Exibe todas as mensagens de erro nos inputs automaticamente,
	que foram recebidas do servidor.
*/
window.dispatchErrorMessages = function(errors, wrapper = '') {
	scrollToElement($('[name*=' + Object.entries(errors)[0][0].split('.')[0] + ']'));

	Object.entries(errors).forEach(function(el, index) {
		$errorMessage = $('<div class="text-danger small">' + el[1][0] + '</div>');
		let name = el[0],
			child,
			selector = '[name=' + name + ']';

		if (name.includes('.')) {
			child = name.split('.')[1];
			name = name.split('.')[0];
			selector = '[name^=' + name + ']';
		}

		if (wrapper !== '') {
			selector = wrapper + ' ' + selector;
		}

		if (name == 'password') {
			$('[name=password]').val('');
			$('[name=password_confirmation]').val('');
		}

		if ($(selector).parent().hasClass('input-group')) {
			$(selector).parent().next('.text-danger').remove();

			$(selector).removeClass('is-invalid')
				.addClass('is-invalid');

			$(selector).parent().after($errorMessage);
		} else {
			if (el[0].includes('.')) {
				selector = $(selector).[child];
			} 
			
			$(selector).next('.text-danger').remove();
			$(selector)
				.removeClass('is-invalid')
				.addClass('is-invalid')
				.after($errorMessage);
		}
	});
}

// Altera o estado de um botão passado, adicionando ou removendo
// o ícone de loading
window.loadingBtn = function(btn, add) {

	if (add) {
		btn.attr('disabled', 'disabled');
		btn.find('i').hide();
		btn.prepend('<span class="spinner-border spinner-border-sm mr-1"></span>');
	} else {
		btn.removeAttr('disabled');
		btn.find('.spinner-border').remove();
		btn.find('i').show();
	}
}

window.getLocationURL = function() {
	return window.location.protocol + '//' + window.location.host + window.location.pathname;
}

// Scrolla para o elemento informado.
window.scrollToElement = function(element, duration = 400) {
  $('html, body').animate({
    scrollTop: element.offset().top - 100
  }, duration);
}