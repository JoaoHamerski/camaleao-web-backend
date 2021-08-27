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
      moment
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
    }
  }
}
</script>

<template>
  <div>
    <div
      class="card card-date"
      :class="{'active': date.isActive}"
    >
      <div
        class="card-header bg-primary text-white text-center py-1 clickable selection-none"
        :class="[isToday(date.date) ? 'bg-success' : 'bg-primary']"
        @click="$emit('header-clicked', date)"
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
            'row row-cols-4': date.isActive,
            'd-flex flex-column': !date.isActive
          }"
          tag="div"
          enter="zoomIn"
          leave="zoomOut"
          speed="faster"
        >
          <Order
            v-for="order in date.items"
            :key="order.id"
            class="col mb-3"
            :is-active="date.isActive"
            :order="order"
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
