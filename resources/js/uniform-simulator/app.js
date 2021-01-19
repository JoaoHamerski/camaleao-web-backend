require('./bootstrap');
require('./../cleave');
require('./vue-mixins');

window.EventBus = new Vue();

Vue.component('vue-drag-resize', require('./components/VueDragResize').default);

Vue.component('navbar', require('./components/Navbar').default);
Vue.component('hamburger', require('./components/Hamburger').default);
Vue.component('sidebar', require('./components/Sidebar').default);
Vue.component('sidebar-item', require('./components/SidebarItem').default);
Vue.component('uniform', require('./components/Uniform').default);
Vue.component('color', require('./components/Color').default);
Vue.component('masked-input', require('vue-text-mask').default);
Vue.component('font-selector', require('./components/FontSelector').default);
Vue.component('sidebar-attachs', require('./components/SidebarAttachs').default);

// Sidebar items
Vue.component('sidebar-item-colors', require('./components/sidebar-items/Colors').default);
Vue.component('sidebar-item-name-and-number', require('./components/sidebar-items/NameAndNumber').default);
Vue.component('sidebar-item-shield', require('./components/sidebar-items/Shield').default);
Vue.component('sidebar-item-import-images', require('./components/sidebar-items/ImportImages').default);

Vue.use(Vuex);

const store = new Vuex.Store({
	state: {
		isFront: true,
		shirtColor: '',
		neckColor: '',
		nameColor: '',
		numberColor: '',
		nameFont: '',
		numberFont: '',
		number: '10',
		name: 'JOGADOR',
		hideBrand: false,
		hideShield: false,
		replaceShieldForNumber: false,
		attachs: [],
		brand: null,
		shield: null
	}
});

new Vue({
	el: '#app',
	store
});
