require('./bootstrap')
require('./cleave')
require('./components/core')
require('./components/components')
require('./pwa')

// Hacky solution to handle vue DOM render with jQuery code
$(document).on('vue-loaded', function() {
	require('./helpers')
	require('./custom')
	require('./sidebar')
})

import helpers from './util/helpers'
import {swalToast, swalModal} from './swal'

const plugin = {
	install(Vue) {
		Vue.prototype.$helpers = helpers
		Vue.prototype.$toast = swalToast
		Vue.prototype.$modal = swalModal
	}
}

Vue.use(plugin)
Vue.use(VueTippy)

new Vue({
	el: '#app',
	mounted() {
		$(document).trigger('vue-loaded')
	}
})



