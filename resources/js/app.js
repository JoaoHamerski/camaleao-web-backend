require('./_helpers');
require('./bootstrap');
require('./cleave');
require('./_custom');

window.addEventListener('load', () => {
	if ('serviceWorker' in navigator) {
		navigator.serviceWorker.register('_service-worker.js');
	}
});

function al() {
	var deferredPrompt;

	window.addEventListener('beforeinstallprompt', (e) => {
	  // Prevent the mini-infobar from appearing on mobile
	  e.preventDefault();

	  deferredPrompt = e;
	  // Update UI notify the user they can install the PWA
	  e.showInstallPromotion();
	});
}

al();
