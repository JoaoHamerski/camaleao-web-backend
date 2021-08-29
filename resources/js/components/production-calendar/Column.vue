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
    date: {
      type: Object,
      default: null
    }
  },
  data () {
    return {
      moment,
      listener: event => {
        console.log('clicou')
        if (!this.$refs.column.contains(event.target)) {
          this.$emit('toggle', this.date)
        }
      }
    }
  },
  watch: {
    'date.isActive' (val) {
      if (val) {
        document.addEventListener('click', this.listener)
        return
      }

      document.removeEventListener('click', this.listener)
    }
  },
  methods: {
    /**
     * Verifica se a data passada é o dia de hoje
     *
     * @param date Moment instance
     * @return Boolean
     */
    isToday(date) {
      return date.isSame(moment(), 'd')
    },
    onCancel(order) {
      this.$emit('order-cancel', order)
    },
    onCreated(order) {
      this.$emit('order-created', order)
    },
    onHeaderClick () {
      this.$emit('toggle', this.date)
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
