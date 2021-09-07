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
  },
  computed: {
    enterActiveClass () {
      if (this.enter === undefined) {
        return ''
      }

      return `animate__animated animate__${this.enter} animate__${this.speed}`
    },
    leaveActiveClass () {
      if (this.leave === undefined) {
        return ''
      }

      return `animate__animated animate__${this.leave} animate__${this.speed}`
    }
  }
}
</script>

<template>
  <transition-group
    v-bind="$attrs"
    :mode="mode"
    :name="name"
    :enter-active-class="enterActiveClass"
    :leave-active-class="leaveActiveClass"
  >
    <slot />
  </transition-group>
</template>
