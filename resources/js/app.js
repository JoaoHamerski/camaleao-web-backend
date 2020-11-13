require('./_helpers');
require('./bootstrap');
require('./cleave');
require('./_custom');

window.addEventListener('load', () => {
	if ('servieWorker' in navigator) {
		navigator.servieWorker.register('_service-worker.js');
	}
});

let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
  // Prevent the mini-infobar from appearing on mobile
  e.preventDefault();
  // Stash the event so it can be triggered later.
  deferredPrompt = e;
  // Update UI notify the user they can install the PWA
  showInstallPromotion();
});