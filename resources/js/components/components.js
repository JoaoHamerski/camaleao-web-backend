import Vue from 'vue'
import upperFirst from 'lodash/upperFirst'
import camelCase from 'lodash/camelCase'

// Auto register globally every base component: "App*.vue"
const requireComponent = require.context(
  '../components',
  true,
  /App[A-z]\w+\.(vue)$/
)

for (let fileName of requireComponent.keys()) {
  const componentConfig = requireComponent(fileName)
  
  const componentName = upperFirst(
    camelCase(
      fileName
        .split('/')
        .pop()
        .replace(/\.\w+$/, '')
    )
  )
  
  Vue.component(
    componentName,
    componentConfig.default || componentConfig
  )
}