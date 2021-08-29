<script>
import VueViewer from 'v-viewer'
import Vue from 'vue'
Vue.use(VueViewer)

export default {
  props: {
    imagePath: undefined,
    order: {
      type: Object,
      required: true
    },
    viewerConfig: {
      type: Object,
      required: true
    }
  }
}
</script>

<template>
  <div class="generic-order text-center mb-4">
    <div class="mb-2">
      <a
        target="_blank"
        :href="order.path"
        class="font-weight-bold"
        :class="[order.is_pre_registered && 'text-warning']"
      >{{ order.is_pre_registered ? 'PRE-REGISTRO' : order.code }}</a>
    </div>

    <div v-if="imagePath !== undefined">
      <div
        v-viewer="viewerConfig"
        class="images"
      >
        <img
          class="img-fluid clickable"
          :src="imagePath"
        >
      </div>
    </div>
    <div
      v-else
      class="text-center font-weight-bold text-secondary small mx-2 my-2"
    >
      [PEDIDO SEM IMAGEM]
    </div>
  </div>
</template>
