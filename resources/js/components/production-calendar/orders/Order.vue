<script>
import 'viewerjs/dist/viewer.css'
import { isEmpty } from 'lodash-es'
import VueViewer from 'v-viewer'
import Vue from 'vue'
Vue.use(VueViewer)

import GenericOrder from './GenericOrder'
import CardOrder from './CardOrder'
import CardOrderNotCreated from './CardOrderNotCreated'

export default {
  components: {
    GenericOrder,
    CardOrder,
    CardOrderNotCreated
  },
  props: {
    isActive: {
      type: Boolean,
      default: false
    },
    order: {
      type: Object,
      default: null
    }
  },
  data () {
    return {
      viewerConfig: {
        inline: false,
        button: true,
        navbar: false,
        title: true,
        toolbar: false,
        tooltip: false,
        movable: true,
        zoomable: true,
        rotatable: false,
        scalable: true,
        transition:true,
        fullscreen: true,
        keyboard: true,
      }
    }
  },
  computed: {
    imagePath () {
      let artPaths = '',
        sizePaths = ''

      if (this.order.isNotCreated) {
        return this.order.imagePath
      }

      artPaths = this.jsonParsePaths(this.order.art_paths),
      sizePaths = this.jsonParsePaths(this.order.art_paths)

      if (artPaths.length) {
        return `/storage/imagens_da_arte/${artPaths}`
      }

      if (sizePaths.length) {
        return `/storage/imagens_do_tamanho/${sizePaths}`
      }

      return undefined
    }
  },
  methods: {
    jsonParsePaths (paths) {
      if (isEmpty(paths)) {
        return paths
      }

      return JSON.parse(paths)
    }
  }
}
</script>

<template>
  <div>
    <GenericOrder
      v-if="! isActive"
      v-bind="{order, imagePath, viewerConfig}"
    />
    <template v-else>
      <CardOrderNotCreated
        v-if="order.isNotCreated"
        v-bind="{order, imagePath, viewerConfig}"
      />
      <CardOrder
        v-else
        v-bind="{order, imagePath, viewerConfig}"
      />
    </template>
  </div>
</template>
