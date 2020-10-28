applyCleave($('[name=date]'), cleaveDate);	

$('#formGenerateReport button[type=submit]').on('click', function(e) {
	e.preventDefault();

	axios.get(window.location.href + '/relatorio', {
		params: {
			city: $('[name=city]').val(),
			status: $('[name=status]').val(),
			only_open: $('[name=only_open]:checked').val()
		}
	})
	.then(response => {
		$('#formGenerateReport').submit();
	})
	.catch(error => {
		console.log(error.response);
		dispatchErrorMessages(error.response.data.errors);
	});
});

$('#formGenerateReportProduction button[type=submit]').on('click', function(e) {
	e.preventDefault();

	axios.get(window.location.href + '/relatorio-data-producao', {
		params: {
			date: $('[name=date]').val()
		}
	})
	.then(response => {
		$('#formGenerateReportProduction').submit();
	})
	.catch(error => {
		console.log(error.response);
		dispatchErrorMessages(error.response.data.errors);
	});
});