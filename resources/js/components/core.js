import Vue from 'vue'

Vue.component('cities-list', require('./cities/CitiesList').default)
Vue.component('branches-list', require('./branches/BranchesList').default)
Vue.component('client-modal', require('./clients/ClientModal').default)