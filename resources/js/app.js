require('./_helpers');
require('./bootstrap');
require('./cleave');
require('./_custom');

window.addEventListener('load', () => {
	if ('servieWorker' in navigator) {
		navigator.servieWorker.register('_service-worker.js');
	}
})