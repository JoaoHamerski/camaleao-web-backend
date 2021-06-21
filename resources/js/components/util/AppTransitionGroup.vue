<template>
  <transition-group 
    :mode="mode"
    :name="name"
    :enter-active-class="`animate__animated animate__${enter} animate__${speed}`"
    :leave-active-class="`animate__animated animate__${leave} animate__${speed}`"
  >
    <slot></slot>
  </transition-group>
</template>

<script>
  export default {
    props: {
      enter: {
        required: false
      },
      leave: {
        required: false
      },
      speed: {
        default: 'fast'
      },
      mode: {
        default: false
      },
      name: {
        default: ''
      }
    }
  }
</script>