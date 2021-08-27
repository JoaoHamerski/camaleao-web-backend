<script>
import { map, some } from 'lodash-es'
import moment from 'moment'
moment.locale('pt-BR')

import Column from './Column'

export default {
  components: {
    Column,
  },
  data () {
    return {
      moment,
      dates: [],
      image: ''
    }
  },
  computed: {
    getActiveDate () {
      return this.dates.find(date => date.isActive)
    },
    hasActiveDay () {
      return some(this.dates, 'isActive')
    }
  },
  mounted () {
    this.refresh()

    document.onpaste = (pasteEvent) => {
      const item = pasteEvent.clipboardData.items[0]

      if (item.type.indexOf('image') !== 0) {
        this.$toast.error('Não foi possível identificar uma imagem no item colado.')
        return
      }

      if (! this.hasActiveDay) {
        this.$toast.error('Por favor, abra o dia desejado primeiro.')
        return
      }

      if (item.type.indexOf('image') === 0) {
        const blob = item.getAsFile(),
          reader = new FileReader()

        reader.onload = (event) => {
          this.createOrder(event.target.result)
        }

        reader.readAsDataURL(blob)
      }
    }
  },
  methods: {
    createOrder(image) {
      const activeDate = this.getActiveDate

      activeDate.items.push({
        id: +new Date(),
        isNotCreated: true,
        imagePath: image
      })
    },
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
