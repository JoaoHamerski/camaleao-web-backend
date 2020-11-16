require('./bootstrap.js');
require('./../cleave.js');

Vue.component('sidebar', require('./components/Sidebar'));

new Vue({
	el: '#app'
});