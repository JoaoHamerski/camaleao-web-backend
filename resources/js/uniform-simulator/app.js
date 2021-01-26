require('./bootstrap');
require('./../cleave');
require('./components');

import helpers from './util/helpers';
import { state, getters, mutations } from './store/app';

const plugin = {
	install(Vue, options) {
		Vue.prototype.$helpers = helpers;
	}
} 

Vue.use(plugin);

Vue.use(Vuex);

const store = new Vuex.Store({
	state, mutations, getters 
});

new Vue({
	el: '#app',
	store
});
