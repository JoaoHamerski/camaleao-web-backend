// Função que aplica a mascara de cleave.js
window.applyCleave = function (elements, options) {
	elements.each(function() {
		new Cleave(this, options);
	});
}

window.cleaveDate = {
	date: true,
	delimiter: '/',
	datePattern: ['d', 'm', 'Y']
};

window.cleavePhone = {
	phone: true,
    phoneRegionCode: 'BR'
};

window.cleaveDate = {
	date: true,
	delimiter: '/',
	datePattern: ['d', 'm', 'Y']
};

window.cleaveNumericInt = function(length) {
	return {
		blocks: [length],
		numericOnly: true,
		numeralDecimalScale: 1
	};
};

window.cleaveValueBRL = {
	numeral: true,
	numeralDecimalMark: ',',
	delimiter: '.',
	prefix: 'R$ ',
	numeralPositiveOnly: true
}