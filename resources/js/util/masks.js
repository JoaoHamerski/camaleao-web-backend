import createNumberMask from 'text-mask-addons/dist/createNumberMask';
import createAutoCorrectedDatePipe from 'text-mask-addons/dist/createAutoCorrectedDatePipe';

export default {
	// Formatação de CPF
	cpf: [/\d/, /\d/, /\d/, '.', /\d/, /\d/, /\d/, '.', /\d/, /\d/, /\d/, '-', /\d/, /\d/],

	// Formatação de CNPJ
	cnpj: [/\d/, /\d/, '.', /\d/, /\d/, /\d/, '.', /\d/, /\d/, /\d/, '/', /\d/, /\d/, /\d/, /\d/, '-', /\d/, /\d/],

	// Formatação de telefone
	phone: function(value) {
		value = value.replaceAll(' ', '');

		if (value.length <= 8) {
			return [/\d/, /\d/, /\d/, /\d/, ' ', /\d/, /\d/, /\d/, /\d/];
		}

		if (value.length == 9) {
			return [/\d/, ' ', /\d/, /\d/, /\d/, /\d/, ' ', /\d/, /\d/, /\d/, /\d/];
		}

		if (value.length == 10) {
			return [/\d/, /\d/, ' ', /\d/, /\d/, /\d/, /\d/, ' ', /\d/, /\d/, /\d/, /\d/];
		}


		return [/\d/, /\d/, ' ', /\d/, ' ', /\d/, /\d/, /\d/, /\d/, ' ', /\d/, /\d/, /\d/, /\d/];
	},

	// Formatação de data
	date: [/\d/, /\d/, '/', /\d/, /\d/, '/', /\d/, /\d/, /\d/, /\d/],
	autoCorrectedDatePipe: createAutoCorrectedDatePipe('dd/mm/yyyy', {maxYear: 2100, minYear: 2000}),

	// Formatação de valor em BRL
	valueBRL: createNumberMask({
		prefix: 'R$ ',
		allowDecimal: true,
		thousandsSeparatorSymbol: '.',
		decimalSymbol: ','
	}),

	numericInt: function(options) {
		return createNumberMask({
			prefix: options.prefix ?? '',
			includeThousandsSeparator: options.includeThousandsSeparator ?? false,
			integerLimit: options.integerLimit ?? null,
			allowNegative: options.allowNegative ?? false
		});
	}
}