<script>
export default {
  props: {
    enter: undefined,
    leave: undefined,
    speed: {
      type: String,
      default: 'fast'
    },
    mode: {
      type: String,
      default: ''
    },
    name: {
      type: String,
      default: ''
    }
  }
}
</script>

<template>
  <transition-group
    v-bind="$attrs"
    :mode="mode"
    :name="name"
    :enter-active-class="`animate__animated animate__${enter} animate__${speed}`"
    :leave-active-class="`animate__animated animate__${leave} animate__${speed}`"
  >
    <slot />
  </transition-group>
</template>
