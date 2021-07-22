import Vue from 'vue'
import { TippyComponent } from "vue-tippy";

Vue.component('cities-list', require('./cities/CitiesList').default)
Vue.component('branches-list', require('./branches/BranchesList').default)
Vue.component('client-modal', require('./clients/ClientModal').default)
Vue.component('new-city-modal', require('./cities/NewCityModal').default)
Vue.component('order-form', require('./orders/OrderForm').default)
Vue.component('clothing-types-list', require('./clothing-types/ClothingTypesList').default)
Vue.component('daily-cash-list', require('./daily-cash/DailyCashList').default)