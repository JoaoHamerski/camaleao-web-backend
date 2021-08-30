import Vue from 'vue'
const state = Vue.observable({isProduction: false})

export const setIsProduction = (newState) => {
  state.isProduction = newState
}

export default state
