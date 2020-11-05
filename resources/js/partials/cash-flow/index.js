applyCleave($('[name*=dia]'), cleaveDate);

$('.btn-view-detail').on('click', function(e) {
  e.preventDefault();

  let id = $(this).parents('tr').attr('data-expense-id') 
  	|| $(this).parents('tr').attr('data-payment-id');

	axios.get(getLocationURL() + '/get-details', {
		params: {
			id: id,
			entity: $(this).parents('tr').get(0).hasAttribute('data-expense-id') ? 'expense' : 'payment'
		}
	})
	.then(response => {
		$('#detailsModal .modal-body').html(response.data.view);
	});
});

$('.btn-today').on('click', function(e) {
	$('[name=dia_final]').val('');
});

$('.btn-current-week').on('click', function(e) {
	e.preventDefault();

	var current = new Date;
	var first = current.getDate() - current.getDay(); 

	var firstDay = new Date(current.setDate(first));
	var lastDay = new Date(current.setDate(firstDay.getDate() + 6));

	$('[name=dia_inicial]').val(new Intl.DateTimeFormat('pt-BR').format(firstDay));
	$('[name=dia_final]').val(new Intl.DateTimeFormat('pt-BR').format(lastDay));	
});

$('.btn-current-month').on('click', function(e) {
	e.preventDefault();

	var date = new Date();
	var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
	var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);

	$('[name=dia_inicial]').val(new Intl.DateTimeFormat('pt-BR').format(firstDay));
	$('[name=dia_final]').val(new Intl.DateTimeFormat('pt-BR').format(lastDay));
});

$('#btnFilter').on('click', function(e) {
	e.preventDefault();

	let $btn = $(this);

	loadingBtn($btn, true);

	axios.get(getLocationURL(), {
		params: {
			dia_inicial: $('[name=dia_inicial]').val(),
			dia_final: $('[name=dia_final]').val()
		}
	})
	.then(response => {
		$btn.parents('form').submit();
	})
	.catch(error => {
		dispatchErrorMessages(error.response.data.errors);
		loadingBtn($btn, false);
	});
})
