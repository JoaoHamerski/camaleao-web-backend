require('./bootstrap.js');
require('./../cleave.js');

Vue.component('sidebar', require('./components/Sidebar').default);
Vue.component('uniform-selection', require('./components/UniformSelection').default);
Vue.component('uniform', require('./components/Uniform').default);

new Vue({
	el: '#app'
});