/*
	Aplica as máscaras nos inputs iniciais da página
*/
applyCleave($('[name*=value]'), cleaveValueBRL);
applyCleave($('[name*=date]'), cleaveDate);
applyCleave($('[name=all_date]'), cleaveDate);

/*
	Pega a soma do valor total dos inputs de valor
*/
function getTotalValue() {
	let total = 0;

	$('[name*=value]').each(function() {
		total += +sanitizeMoney($(this).val());
	});

	return total;
}

/*
	Toda vez que houver alteração, ou seja, toda vez que um novo
	formulário em linha for adicionado, é aplicado as máscaras de input
	nesse formulário.
*/
let target = document.querySelector('#formExpenses');

let observer = new MutationObserver(mutations => {
	applyCleave($('[name*=value]').last(),  cleaveValueBRL);
	applyCleave($('[name*=date]').last(), cleaveDate);

	if ($('[name=all_date]').val().trim() !== '') {
		$('[name*=date]').last().val($('[name=all_date]').val());
	}
});

observer.observe(target, {attributes: true, childList: true, characterData: true});

/*
	Adiciona um novo formulário em linha quando clicado no botão de mais
*/
$('#btnNewExpense').on('click', function(e) {
	e.preventDefault();

	axios.get(getLocationURL() + '/get-inline-form')
		.then(response => {
			$('#btnNewExpense').parent().before(response.data.view);
		});
});

/*
	Atualiza o valor total de despesas ao alterar qualquer input value
*/
$('#formExpenses').on('input', '[name*=value]', function(e) {

	let value = sanitizeMoney($(this).val());
	let totalValue = getTotalValue();

	let formatter = new Intl.NumberFormat('pt-BR', {
		style: 'currency',
		currency: 'BRL'
	});

	$('#totalValue').text(formatter.format(getTotalValue()));
});

$('[name=all_date]').on('input focus', function() {
	$('[name*=date]').val($(this).val());
});

/*
	Deleta o elemento responsável pelo formulário em linha
*/
$('#formExpenses').on('click', '.btn-delete', function(e) {
	e.preventDefault();

	$(this).closest('.form-inline-wrapper').remove();
});

/*
	Envia os dados para o servidor para verificar se há erros,
	se não houver, redireciona para a página retornada pelo servidor.
*/
$('#formExpenses button[type="submit"]').on('click', function(e) {
	e.preventDefault();

	let formData = new FormData(document.querySelector('#formExpenses'));
	let $btn = $(this);

	loadingBtn($btn, true);

	axios.post(getLocationURL(), formData)
		.then(response => {
			window.location = response.data.redirect;
		})
		.catch(error => {
			console.log(error.response);
			loadingBtn($btn, false);
			dispatchErrorMessages(error.response.data.errors);
		});
});