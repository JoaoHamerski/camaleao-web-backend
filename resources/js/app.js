require('./_helpers');
require('./bootstrap');
require('./cleave');
require('./_custom');

window.addEventListener('load', () => {
	if ('serviceWorker' in navigator) {
		navigator.serviceWorker.register('_service-worker.js');
	}
});

window.addEventListener('beforeinstallprompt', (event) => {
  console.log('👍', 'beforeinstallprompt', event);
  // Stash the event so it can be triggered later.
  window.deferredPrompt = event;
});

$('#btnInstallPWA').on('click', function(event) {
	
	const promptEvent = window.deferredPrompt;

	if (! promptEvent) {
		return;
	}

	promptEvent.prompt();

	promptEvent.userChoice.then((result) => {
		console.log(result);

		window.deferredPrompt = null;
	})

});