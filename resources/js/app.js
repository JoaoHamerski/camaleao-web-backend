require('./bootstrap');
require('./cleave');

// Seta um cookie, em que "data" é informado o "name" e "value" do cookie.
window.setCookie = function(data) {
	axios.post('/set-cookie', data);
}

// Deleta um cookie, em que "name" é informado o "name" do cookie a ser deletado.
window.destroyCookie = function(name) {
	axios.delete('/destroy-cookie', {name: name});
}

$('#btnSidebar').click(function() {
  if ($('.sidebar').hasClass('is-active')) {
    setCookie({name: 'sidebar_active', value: true });
  } else {
    setCookie({name: 'sidebar_active', value: false });
  }

	$(this).toggleClass('is-active');
	$('.sidebar').toggleClass('is-active');
});

window.dispatchErrorMessages = function(errors) {
	Object.entries(errors).forEach(function(el, index) {
		$errorMessage = $('<div class="text-danger small">' + el[1][0] + '</div>');
		let name = el[0];
		let selector = '[name=' + name + ']';

		if (name.includes('.')) {
			name = name.split('.')[0];
			selector = '[name*=' + name + ']';
		}

		if ($(selector).parent().hasClass('input-group')) {
			$(selector).parent().next('.text-danger').remove();

			$(selector).removeClass('is-invalid')
				.addClass('is-invalid');

			$(selector).parent().after($errorMessage);
		} else {
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

$(document).on('focus', 'input', function() {
	$(this).removeClass('is-invalid')
		.next('.text-danger')
		.remove();
		
	$(this).parent().next('.text-danger').remove();
});	

$('[data-toggle="tooltip"]').tooltip();
