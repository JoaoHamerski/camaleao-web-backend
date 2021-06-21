<template>
  <transition
    :mode="mode"
    :enter-active-class="enterActiveClass"
    :leave-active-class="leaveActiveClass"
  >
    <slot></slot>
  </transition>
</template>

<script>
  /* 
    Based on animate.css
  */
  export default {
    props: {
      enter: String,
      leave: String,
      mode: {default: ''},
      speed: {
        type: String,
        default: 'fast'
      }
    },
    computed: {
      enterActiveClass() {
        if (this.enter === undefined) {
          return ''
        }

        return `animate__animated animate__${this.enter} animate__${this.speed}` 
      },
      leaveActiveClass() {
        if (this.leave === undefined) {
          return ''
        }

        return `animate__animated animate__${this.leave} animate__${this.speed}`
      }
    }
  }
</script>