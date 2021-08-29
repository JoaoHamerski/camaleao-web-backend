<script>
import { map, some, isEmpty } from 'lodash-es'
import moment from 'moment'
moment.locale('pt-BR')
import { Carousel, Slide } from 'vue-carousel'

import DatePicker from 'vue2-datepicker'
import 'vue2-datepicker/index.css'
import 'vue2-datepicker/locale/pt-br'

import Column from './Column'

export default {
  components: {
    Column,
    DatePicker,
    Carousel,
    Slide
  },
  data () {
    return {
      lang: {
        formatLocale: {
          firstDayOfWeek: 1,
          weekdaysMin: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb']
        },
        monthBeforeYear: false,
      },
      moment,
      week: '',
      dates: [],
      image: '',
      isLoading: false
    }
  },
  computed: {
    activeDate () {
      return this.dates.find(date => date.isActive)
    },
    hasActiveDay () {
      return some(this.dates, 'isActive')
    },
    datePickerInput () {
      let dateMoment = null

      if (isEmpty(this.week)) {
        return ''
      }

      dateMoment = moment(this.week, 'DD/MM/YYYY')
      dateMoment.startOf('week').weekday(1)

      return this.weekOfMonth(dateMoment) + 'ª semana' + ' de ' + dateMoment.format('MMMM')
    }
  },
  watch: {
    week () {
      if (isEmpty(this.week)) {
        this.refresh()
        return
      }

      this.refresh(moment(this.week, 'DD/MM/YYYY').startOf('week').weekday(1))
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
    weekOfMonth(m) {
      return m.weekday(1).week() - m.startOf('month').weekday(1).week() + 1
    },
    createOrder(image) {
      const activeDate = this.activeDate
      activeDate.items.push({
        id: +new Date(),
        production_date: activeDate.date.format('YYYY-MM-DD'),
        isNotCreated: true,
        imagePath: image
      })
    },
    onOrderCancel(order) {
      const index = this.activeDate.items.findIndex(
        item => item.id === order.id
      )

      this.activeDate.items.splice(index, 1)
    },
    onOrderCreated(order) {
      const index = this.activeDate.items.findIndex(
        item => item.id === order.id
      )

      this.activeDate.items.splice(index, 1, order.new)
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
    refresh (weekDay = moment().weekday(1)) {
      this.isLoading = true

      axios.get('/calendario-de-producao/pedidos/semana', {
        params: {
          startWeek: weekDay.format('DD/MM/YYYY')
        }
      })
        .then(response => {
          this.dates = map(response.data.dates, (date, index) => ({
            date: moment(index, 'YYYY/MM/DD'),
            items: date,
            isActive: false
          }))
        })
        .catch(error  => {
          console.log(error.response)
        })
        .then(() => {
          this.isLoading = false
        })
    }
  }
}
</script>

<template>
  <div>
    <div class="d-flex flex-column flex-md-row justify-content-between my-3">
      <h4 class="font-weight-bold text-primary text-right text-md-left">
        <i class="fas fa-calendar-alt fa-fw mr-1" />CALENDÁRIO DE PRODUÇÃO
      </h4>

      <div class="d-flex align-items-center justify-content-end">
        <DatePicker
          v-model="week"
          class="mr-1"
          value-type="DD/MM/YYYY"
          :lang="lang"
          type="week"
          :show-week-number="false"
        >
          <template #icon-clear>
            <i class="fas fa-times" />
          </template>
          <template #icon-calendar>
            <i class="fas fa-calendar-alt" />
          </template>
          <template #input>
            <input
              :value="datePickerInput"
              type="text"
              class="form-control"
            >
          </template>
        </DatePicker>
        <button
          :disabled="isLoading"
          class="btn btn-outline-primary"
          @click="week === '' ? refresh() : week = ''"
        >
          <i class="fas fa-sync-alt" />
        </button>
      </div>
    </div>

    <div class="mt-3 position-relative">
      <AppLoading v-if="isLoading" />

      <Carousel
        :per-page="1"
        :per-page-custom="[[576, 2], [768, 3], [992, 4], [1200, 6]]"
      >
        <template v-for="(date) in dates">
          <Slide :key="date.date.format('DD')">
            <Column
              :date="date"
              class="mx-1"
              @header-clicked="toggleColumn"
              @order-cancel="onOrderCancel"
              @order-created="onOrderCreated"
            />
          </Slide>
        </template>
      </Carousel>
    </div>
  </div>
</template>

<style>
.mx-calendar-header .mx-icon-double-left,
.mx-calendar-header .mx-icon-double-right {
  display: none;
}
</style>
