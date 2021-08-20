<template>
  <transition-group
    :mode="mode"
    :name="name"
    :enter-active-class="`animate__animated animate__${enter} animate__${speed}`"
    :leave-active-class="`animate__animated animate__${leave} animate__${speed}`"
  >
    <slot />
  </transition-group>
</template>

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
            type: Boolean,
            default: false
        },
        name: {
            type: String,
            default: ''
        }
    }
}
</script>
