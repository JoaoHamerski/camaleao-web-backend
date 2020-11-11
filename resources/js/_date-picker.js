import jQuery from 'jquery';

window.$ = window.jQuery = jQuery;

require('bootstrap-datepicker');
require('bootstrap-datepicker/js/locales/bootstrap-datepicker.pt-BR');

$.fn.datepicker.defaults.format = 'dd/mm/yyyy';
$.fn.datepicker.defaults.language = 'pt-BR';
$.fn.datepicker.defaults.todayBtn = 'linked';
$.fn.datepicker.defaults.todayHighlight = true;
$.fn.datepicker.defaults.autoclose = true;

$('[data-toggle="datepicker"]').datepicker().on('changeDate', function(e) {
	$(this).focus().blur();
});


