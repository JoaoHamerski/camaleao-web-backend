<script>
import Order from './orders/Order'
import moment from 'moment'
import 'viewerjs/dist/viewer.css'
import AppTransitionGroup from '@/components/util/AppTransitionGroup'

export default {
  components: {
    Order,
    AppTransitionGroup
  },
  props: {
    roleId: {
      type: Number,
      required: true
    },
    date: {
      type: Object,
      default: null
    }
  },
  data () {
    return {
      moment,
      listener: event => {
        if (event.target === document.querySelector('#image-upload')) {
          return
        }

        console.log(event.target)
        if (event.target.contains(document.querySelector('.viewer-move'))
            || event.target.contains(document.querySelector('.viewer-close'))) {
          return
        }

        if (!this.$refs.column.contains(event.target)) {
          this.$emit('toggle', this.date)
        }
      }
    }
  },
  watch: {
    'date.isActive' (val) {
      if ($(window).width() >= 576) {
        if (val) {
          document.addEventListener('click', this.listener)
          return
        }

        document.removeEventListener('click', this.listener)
      }
    }
  },
  methods: {
    /**
     * Verifica se a data passada é o dia de hoje
     *
     * @param date Moment instance
     * @return Boolean
     */
    isToday (date) {
      return date.isSame(moment(), 'd')
    },
    onCancel (order) {
      this.$emit('order-cancel', order)
    },
    onCreated (order) {
      this.$emit('order-created', order)
    },
    onHeaderClick () {
      if ($(window).width() >= 576) {
        this.$emit('toggle', this.date)
      }
    },
    onImageUploaded (event) {
      if (event.target.files && event.target.files[0]) {
        const reader = new FileReader()

        reader.onload = e => {
          e.target.result
          this.$emit('image-uploaded', e.target.result)
        }

        reader.readAsDataURL(event.target.files[0])
      }
    }
  }
}
</script>

<template>
  <div ref="column">
    <div
      class="card card-date"
      :class="{'active': date.isActive}"
    >
      <div
        class="card-header bg-primary text-white text-center py-1 clickable selection-none"
        :class="[isToday(date.date) ? 'bg-success' : 'bg-primary']"
        @click="onHeaderClick"
      >
        <div class="font-weight-bold">
          {{ date.date.format('DD/MM') }} -
          <span
            class="text-uppercase"
          >{{ date.isActive ? date.date.format('dddd') : date.date.format('ddd') }}</span>
        </div>
      </div>

      <div
        class="card-body"
        :class="[date.isActive ? '' : 'px-0']"
      >
        <input
          id="image-upload"
          type="file"
          accept="image/*"
          class="d-none"
          @change="onImageUploaded"
        >

        <label
          v-if="[1, 2, 3].includes(roleId)"
          v-show="date.isActive"
          for="image-upload"
          class="btn btn-outline-success btn-sm mb-2 ml-n2"
        >
          <i class="fas fa-upload fa-fw mr-1" />Enviar imagem
        </label>

        <AppTransitionGroup
          v-if="!! date.items.length"
          class="orders"
          :class="{
            'row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4': date.isActive,
            'd-flex flex-column': !date.isActive
          }"
          tag="div"
          enter="zoomIn"
          speed="faster"
        >
          <Order
            v-for="order in date.items"
            :key="order.id"
            class="mb-3 px-1 has-divider"
            :is-active="date.isActive"
            :role-id="roleId"
            :order="order"
            @cancel="onCancel"
            @created="onCreated"
          />
        </AppTransitionGroup>

        <div
          v-else
          class="text-secondary text-center small"
        >
          SEM PEDIDOS
        </div>
      </div>
    </div>
  </div>
</template>
