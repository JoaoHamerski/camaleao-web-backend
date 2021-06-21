window.addEventListener('load', () => {
	if ('serviceWorker' in navigator) {
		navigator.serviceWorker.register('/service-worker.js');
	}
});

window.addEventListener('beforeinstallprompt', (event) => {
  window.deferredPrompt = event;
});

$('#btnInstallPWA').on('click', function(event) {
	const promptEvent = window.deferredPrompt;

	if (! promptEvent) {
		return;
	}

	promptEvent.prompt();

	promptEvent.userChoice.then((result) => {
		window.deferredPrompt = null;
	});
});

if (matchMedia('(display-mode: standalone)').matches) {
  $('#btnInstallPWA').remove();
}
