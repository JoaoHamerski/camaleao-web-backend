<script>
export default {
  props: {
    id: {
      type: String,
      required: true
    },
    value: undefined
  }
}
</script>

<template>
  <div class="form-check mb-3">
    <input
      :id="id"
      class="form-check-input"
      type="checkbox"
      :value="value"
      @input="$emit('input', $event.target.value)"
    >
    <label
      v-if="$slots.default"
      class="form-check-label"
      :for="id"
    >
      <slot />
    </label>
  </div>
</template>
