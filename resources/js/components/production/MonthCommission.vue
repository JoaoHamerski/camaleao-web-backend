<template>
  <div>
    <div v-if="! hasCommission && monthResult.length" class="text-center text-secondary">
      Sem comissões confirmadas no mês 
      <strong>{{ monthResult }}</strong>
    </div>
    <div v-if="monthCommission" class="text-center">
      Comissão do mês <strong>{{ monthResult }}</strong>
      <h5 class="text-success font-weight-bold">
        {{ $helpers.valueToBRL(monthCommission) }}
      </h5>
    </div>

    <div class="text-center font-weight-bold mb-2">
      Selecione um mês para calcular a comissão
    </div>

    <div v-if="isAdmin" class="form-group">
      <select class="custom-select" v-model="user_role"
        name="user_role" 
        id="user_role"
      >
        <option value="">Selecione um tipo de usuário</option>
        <option value="4">Costura</option>
        <option value="5">Estampa</option>
      </select>
    </div>

    <div class="form-group">
      <select class="custom-select" 
        id="month" 
        name="month" 
        v-model="month"
      >
        <option value="">Selecione um mês</option>
        <option v-for="month in months" 
          :value="month.number"
          :key="month.number"
        >
          {{ month.name  }}
        </option>
      </select>
    </div>

    <div class="form-group">
      <button class="btn btn-primary font-weight-bold mt-2"
        @click="calculate"
      >
        Calcular
      </button>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      isAdmin: { default: false}
    },
    data() {
      return {
        monthCommission: '',
        monthResult: '',
        hasCommission: null,
        months: [
          {number: 1, name: 'Janeiro'},
          {number: 2, name: 'Feveiro'},
          {number: 3, name: 'Março'},
          {number: 4, name: 'Abril'},
          {number: 5, name: 'Maio'},
          {number: 6, name: 'Junho'},
          {number: 7, name: 'Julho'},
          {number: 8, name: 'Agosto'},
          {number: 9, name: 'Setembro'},
          {number: 10, name: 'Outubro'},
          {number: 11, name: 'Novembro'},
          {number: 12, name: 'Dezembro'},
        ],
        month: '',
        user_role: ''
      }
    },
    methods: {
      calculate() {
        let params = {
          month: this.month
        }
        
        if (typeof this.month != 'number') {
          this.$toast.error('Por favor, selecione o mês')
          return
        }

        if (this.month < 1 || this.month > 12) {

          return
        }

        if (this.isAdmin && this.user_role) {
          params['user_role'] = this.user_role
        }

        axios.get('/producao/comissao-do-mes', { params })
          .then(response => {
            this.monthCommission = response.data.commission
            this.monthResult = `${response.data.month}/${response.data.year}`
            this.hasCommission = response.data.has_commission
          })
      }
    }
  }
</script>