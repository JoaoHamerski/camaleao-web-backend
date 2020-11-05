applyCleave($('[name*=data]'), cleaveDate);	

$('#formGenerateReport button[type=submit]').on('click', function(e) {
	e.preventDefault();

	let $btn = $(this);

	loadingBtn($btn, true);

	axios.get(getLocationURL() + '/relatorio', {
		params: {
			cidade: $('[name=cidade]').val(),
			status: $('[name=status]').val()
		}
	})
	.then(response => {
		$('#formGenerateReport').submit();
	})
	.catch(error => {
		dispatchErrorMessages(error.response.data.errors);
	})
	.then(function() {
		loadingBtn($btn, false);
	});
});

$('#formGenerateReportProduction button[type=submit]').on('click', function(e) {
	e.preventDefault();

	let $btn = $(this);

	loadingBtn($btn, true);
	
	axios.get(getLocationURL() + '/relatorio-data-producao', {
		params: {
			data_de_producao: $('[name=data_de_producao]').val()
		}
	})
	.then(response => {
		$('#formGenerateReportProduction').submit();
	})
	.catch(error => {
		dispatchErrorMessages(error.response.data.errors);
	})
	.then(function() {
		loadingBtn($btn, false);
	});
});