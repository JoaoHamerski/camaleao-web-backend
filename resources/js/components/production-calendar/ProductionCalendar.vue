<template>
  <div>
    <h4 class="text-center my-3 font-weight-bold text-primary">
      <i class="fas fa-calendar-alt fa-fw mr-1" />CALENDÁRIO DE PRODUÇÃO
    </h4>

    <div class="row no-gutters mt-3 position-relative">
      <template v-for="(date) in dates">
        <Column
          :key="date.date.format('DD')"
          :date="date"
          class="col-2 px-1 position-static"
          @header-clicked="toggleColumn"
        />
      </template>
    </div>
  </div>
</template>

<script>
import { map } from 'lodash-es'
import moment from 'moment'
moment.locale('pt-BR')

import Column from './Column'

export default {
  components: {
    Column
  },
  data () {
    return {
      moment,
      dates: []
    }
  },
  mounted () {
    this.refresh()
  },
  methods: {
    toggleColumn (date) {
      const _date = this.dates.find(_date => _date === date),
        activeDate = this.dates.find(date => date.isActive)

      if (activeDate) {
        activeDate.isActive = false
      }

      if (activeDate === _date) {
        activeDate.isActive = false
        return
      }

      _date.isActive = ! _date.isActive
    },
    refresh () {
      axios.get('/calendario-de-producao/pedidos/semana', {
        params: {
          startWeek: '16/08/2021'
        }
      })
        .then(response => {
          this.dates = map(response.data.dates, (date, index) => ({
            date: moment(index, 'YYYY/MM/DD'),
            items: date,
            isActive: false
          }))
        })
    }
  }
}
</script>
