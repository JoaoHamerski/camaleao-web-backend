import jQuery from 'jquery';
import Swal from 'sweetalert2';
import Vue from 'vue';
import Vuex from 'vuex';

require('popper.js').default;
require('bootstrap');

window.Vue = Vue;
window.Vuex = Vuex;
window.$ = window.jQuery = jQuery;
window.axios = require('axios');
window.Swal = Swal;
window._ = require('lodash');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';