import Vue from 'vue'

Vue.component('CitiesList', require('./cities/CitiesList').default)
Vue.component('BranchesList', require('./branches/BranchesList').default)
Vue.component('ClientModal', require('./clients/ClientModal').default)
Vue.component('ClientDeleteModal', require('./clients/ClientDeleteModal').default)
Vue.component('NewCityModal', require('./cities/NewCityModal').default)
Vue.component('OrderForm', require('./orders/OrderForm').default)
Vue.component('ClothingTypesList', require('./clothing-types/ClothingTypesList').default)
Vue.component('DailyCashList', require('./daily-cash/DailyCashList').default)
Vue.component('CommissionsList', require('./production/CommissionsList').default)
Vue.component('MonthCommission', require('./production/MonthCommission').default)
Vue.component('ProductionCalendar', require('./production-calendar/ProductionCalendar').default)
