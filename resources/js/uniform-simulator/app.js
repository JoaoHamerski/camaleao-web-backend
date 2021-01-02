require('./bootstrap.js');
require('./../cleave.js');

window.EventBus = new Vue();

Vue.component('navbar', require('./components/Navbar').default);
Vue.component('hamburger', require('./components/Hamburger').default);
Vue.component('sidebar', require('./components/Sidebar').default);
Vue.component('sidebar-item', require('./components/SidebarItem').default);
Vue.component('uniform', require('./components/Uniform').default);
Vue.component('color', require('./components/Color').default);

// Sidebar items
Vue.component('sidebar-item-colors', require('./components/sidebar-items/Colors').default);
Vue.component('sidebar-item-name-and-number', require('./components/sidebar-items/NameAndNumber').default);

new Vue({
	el: '#app'
});