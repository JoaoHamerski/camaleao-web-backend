Vue.component('navbar', require('./components/Navbar').default);
Vue.component('hamburger', require('./components/Hamburger').default);
Vue.component('sidebar', require('./components/Sidebar').default);
Vue.component('sidebar-item', require('./components/SidebarItem').default);
Vue.component('uniform', require('./components/Uniform').default);
Vue.component('color', require('./components/Color').default);
Vue.component('masked-input', require('vue-text-mask').default);
Vue.component('font-selector', require('./components/FontSelector').default);
Vue.component('sidebar-attachs', require('./components/SidebarAttachs').default);
Vue.component('sidebar-attach-item', require('./components/SidebarAttachItem').default);
Vue.component('vue-drr', require('./components/VueDRR').default);

// Sidebar items
Vue.component('sidebar-item-colors', require('./components/sidebar-items/Colors').default);
Vue.component('sidebar-item-name-and-number', require('./components/sidebar-items/NameAndNumber').default);
Vue.component('sidebar-item-shield', require('./components/sidebar-items/Shield').default);
Vue.component('sidebar-item-import-images', require('./components/sidebar-items/ImportImages').default);