<template>
  <div>
    <div
      class="card card-date"
      :class="{'active': date.isActive}"
    >
      <div
        class="card-header bg-primary text-white text-center py-1 clickable selection-none"
        :class="[date.date.isSame(moment().subtract(2, 'days'), 'd') ? 'bg-success' : 'bg-primary']"
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
        :class="[date.isActive ? 'px-2' : 'px-0']"
      >
        <div>
          <div
            v-if="!! date.items.length"
            class="orders"
          >
            <div
              v-for="order in date.items"
              :key="order.id"
              class="order"
              :class="{'has-divider': !date.isActive}"
            >
              <Order
                :is-active="date.isActive"
                :order="order"
              />
              <hr
                v-if="! date.isActive"
                class="divider my-3"
              >
            </div>
          </div>
          <div
            v-else
            class="text-secondary text-center small"
          >
            SEM PEDIDOS
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Order from './orders/Order'
import moment from 'moment'
import 'viewerjs/dist/viewer.css'

export default {
  components: {
    Order
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

<style lang="scss">
.card.card-date {
    transition: all .15s;

    &:not(.active) {
        max-width: 25vw;
        .orders {
            display: flex;
            flex-direction: column;
        }
    }


    &.active {
        position: absolute;
        max-width: 90vw;
        left: 0;
        right: 0;
        margin-left: auto;
        margin-right: auto;
        z-index: 100;
        box-shadow: 2px 3px 7px rgba(0, 0, 0, .1);

        .orders {
            display: grid;
            grid-template-columns: 200px 200px 200px 200px;
            gap: 10px;
            justify-content: start;

            .card-order {
                box-shadow: 0px 5px 7px rgb(0, 0, 0, .1);
            }
        }
    }
}
</style>>
