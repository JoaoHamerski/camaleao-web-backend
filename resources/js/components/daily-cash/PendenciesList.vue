<template>
  <div>
    <ul class="list-group list-hover">
      <li v-for="pendency in pendencies" 
        :key="pendency.date_registered"
        class="list-group-item d-flex justify-content-between list-group-item-action clickable font-weight-bold"  
        @click="onItemClick(pendency)"
      >
        {{ moment(pendency.date_registered).format('DD/MM/YYYY') }}
        <h5 class="mb-0">
          <span class="badge badge-primary">{{ pendency.total }}</span>
        </h5>
      </li>
    </ul>
  </div>
</template>

<script>
  import moment from 'moment'
  moment.locale('pt-br')

  export default {
    data() {
      return {
        pendencies: [],
        moment
      }
    },
    methods: {
      onItemClick(pendency) {
        this.$emit('load-pendencies', pendency.date_registered)
      },
      refreshPendencies() {
        axios.get('/caixa-diario/get-pendencies')
          .then(response => {
            this.pendencies = response.data.pendencies
          })
      },
      refresh() {
        this.refreshPendencies()
      }
    },
    mounted() {
      this.refresh()
      
      this.$on('refresh-pendencies', () => {
        this.refresh()
      })
    }
  }
</script>