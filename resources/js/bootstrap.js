import jQuery from 'jquery'
import Swal from 'sweetalert2'
import Vue from 'vue'
import VueTippy from 'vue-tippy'

require('popper.js').default
require('bootstrap')

window.Vue = Vue
window.VueTippy = VueTippy
window.$ = window.jQuery = jQuery
window.axios = require('axios')
window.Swal = Swal
window._ = require('lodash')
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

