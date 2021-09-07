import Vue from 'vue'

Vue.component('TheBranches', require('./branches/TheBranches').default)
Vue.component('TheCities', require('./cities/TheCities').default)
Vue.component('TheClothingTypes', require('./clothing-types/TheClothingTypes').default)
Vue.component('ClientModal', require('./clients/ClientModal').default)
Vue.component('ClientDeleteModal', require('./clients/ClientDeleteModal').default)
Vue.component('NewCityModal', require('./cities/NewCityModal').default)
Vue.component('OrderForm', require('./orders/OrderForm').default)
Vue.component('TheDailyCash', require('./daily-cash/TheDailyCash').default)
Vue.component('TheCommissions', require('./production/TheCommissions').default)
Vue.component('MonthCommission', require('./production/MonthCommission').default)
Vue.component('TheProductionCalendar', require('./production-calendar/TheProductionCalendar').default)
Vue.component('TheLogin', require('./TheLogin').default)
