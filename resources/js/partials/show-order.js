$('a[data-option]').on('click', function(e) {
	e.preventDefault();

	let option = $(this).attr('data-option');

	axios.post(getLocationURL() + '/file-view', {
		option: option
	})
	.then(response => {
		$('#fileViewerModal').find('.modal-body').html(response.data.view);
		$('#fileViewerModal').modal('show');
		console.log(response);
	})
	.catch(error => {
		console.log(error.response);
	});
});

$('#btnDeleteOrder').on('click', function(e) {
	e.preventDefault();

	Swal.fire({
		icon: 'error',
		iconHtml: '<i class="fas fa-trash-alt"></i>',
		title: 'Tem certeza?',
		html: '<div class="text-center">Todos os anexos e pagamentos serão excluídos também</div>',
		showCancelButton: true,
		confirmButtonText: 'Tenho',
		cancelButtonText: 'Cancelar'
	})
	.then(result => {
		if (result.isConfirmed) {
      
      $('#content').prepend($('<div class="loading-page"><div class="spinner-border text-primary"></div></div>'))

			axios.delete(getLocationURL())
				.then(response => {
					window.location = response.data.redirect;
				});
		}
	});
});

$('#btnAddNote').on('click', function(e) {
  e.preventDefault();
  let $btn = $(this);

  loadingBtn($btn, true);

  axios.post(getLocationURL() + '/new-note', {
    'order_note': $('[name=order_note]').val()
  })
  .then(response => {
    $('#listGroupNotes').append(response.data.noteListItem);
    $('button[data-target="#notesModal"]').html('Anotações (' + response.data.countNotes  + ')');

    $('[name=order_note').val('');
  })
  .catch(error => { 
    console.log(error.response);
  	dispatchErrorMessages(error.response.data.errors);
  })
  .then(function() {
  	loadingBtn($btn, false);
  });
});

$(document).on('click', '.btn-delete-item', function(e) {
  e.preventDefault();
  let $itemWrapper = $(this).parents('[data-id]');
  let id = $itemWrapper.attr('data-id');

  axios.delete(getLocationURL() + '/delete-note/' + id)
    .then(response => {
      $itemWrapper.remove();
       $('button[data-target="#notesModal"]').html('Anotações (' + response.data.countNotes  + ')');
    })
    .catch(error => { 
    })
}); 

applyCleave($('[name=value]'), cleaveValueBRL);
applyCleave($('[name=date]'), cleaveDate);

$('#today').on('click', function(e) {
  e.preventDefault();

  let date = new Date();
  let today = '';
  let month = date.getMonth() + 1;

  today += date.getDate() + '/';
  today += (month < 10) ? '0' + month : month;
  today += '/';
  today += date.getFullYear();

  $('[name=date]').val(today).focus();

});

$('#btnAddPayment').on('click', function(e) {
  e.preventDefault();

  $btn = $(this);
  let id = $(this).attr('data-id');

  loadingBtn($btn, true);

  axios.post(getLocationURL() + '/new-payment', {
    value: $('[name=value]').val(),
    date: $('[name=date]').val(),
    note: $('[name=note]').val()
  })
  .then(response => {
    window.location = response.data.redirect;
  })
  .catch(error => {
    console.log(error.response);
    dispatchErrorMessages(error.response.data.errors);
  })
  .then(function() {
    loadingBtn($btn, false);
  });
}); 