<template>
  <div>
    <AppLoading v-if="isLoading" />
    
    <div class="d-flex justify-content-between">
      <button class="btn btn-lg btn-primary font-weight-bold"
        data-toggle="modal"
        data-target="#dailyPaymentModal"
      >
        <i class="fas fa-plus fa-fw mr-1"></i>Nova entrada
      </button>

      <button class="btn btn-outline-primary"
        @click="refreshPayments"
      >
        <i class="fas fa-sync-alt fa-fw"></i>
      </button>
    </div>

    <div class="text-center mt-4">
      <h4 class="font-weight-bold mb-0 horizontal-line mb-3"
        v-tippy="{duration: 150}"
        :content="moment().format('DD [de] MMMM')"
      >
        <span>{{ moment().format('DD/MM') }}</span>
      </h4>

      <h6 class="text-secondary">Pagamentos de hoje</h6>
    </div>

    <div v-if="!! payments.length" class="table-responsive mt-4">
      <table class="table">
        <thead>
          <th>PEDIDO</th>
          <th>CLIENTE</th>
          <th>VALOR</th>
          <th>VIA</th>
          <th class="text-center">HORÁRIO</th>
          <th class="text-center">
            <i class="fas fa-clipboard-check fa-lg"></i>
          </th>
        </thead>

        <tbody>
          <tr v-for="payment in payments" :key="payment.id"
            :class="{
              'table-success': payment.is_confirmed == true,
              'table-danger': payment.is_confirmed == false,
              'table-warning': payment.is_confirmed == null
            }"
          >
            <td>{{ payment.order.code }}</td>
            <td nowrap>{{ payment.order.client.name }}</td>
            <td class="font-weight-bold">{{ $helpers.valueToBRL(payment.value) }}</td>
            <td nowrap>{{ payment.via.name }}</td>
            <td class="text-center">
              {{ moment(payment.created_at).format('HH:mm') }}
            </td>

            <td nowrap v-if="userRole === 3" class="text-center">
              <div v-if="payment.is_confirmed === null">
                <button class="btn btn-outline-primary btn-sm px-3"
                  @click="assign(payment, true)"
                  :disabled="payment.isLoading"
                >
                  <i class="fas fa-check fa-fw"></i>
                </button>
                
                <button class="btn btn-outline-danger btn-sm px-3"
                  @click="assign(payment, false)"
                  :disabled="payment.isLoading"
                >
                  <i class=" fas fa-times fa-fw"></i>
                </button>
              </div>
              <span v-else-if="payment.is_confirmed == true" class="font-weight-bold">
                <i class="fas fa-check text-success"></i>
              </span>
              <span v-else-if="payment.is_confirmed == false" class="font-weight-bold">
                <i class="fas fa-times text-danger"></i>
              </span>
            </td>

            <td v-else class="font-weight-bold text-center">
              <span v-if="payment.is_confirmed === null">
                <i class="fas fa-minus text-warning"></i>
              </span>
              <span v-else-if="payment.is_confirmed == true">
                <i class="fas fa-check text-success"></i>
              </span>
              <span v-else-if="payment.is_confirmed == false">
                <i class="fas fa-times text-danger"></i>
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-else class="text-center text-secondary my-5">
      <h5>Nenhum pagamento registrado</h5>
    </div>

    <DailyPaymentModal @created="onPaymentCreated" />
  </div>
</template>

<script>
  import DailyPaymentModal from './DailyPaymentModal.vue'

  import moment from 'moment'
  moment.locale('pt-BR')

  export default {
    components: {
      DailyPaymentModal
    },
    props: {
      userRole: {default: 0}
    },
    data: () => {
      return {
        moment,
        isLoading: false,
        payments: []
      }
    },
    methods: {
      onPaymentCreated() {
        this.refreshPayments()
      },
      assign(payment, confirmation) {
        payment.isLoading = true

        axios.post(`/caixa-diario/${payment.id}/assign-confirmation`, {
          confirmation
        })
          .then(response => {
            this.$toast.success(
              confirmation 
                ? 'Pagamento aceito'
                : 'Pagamento rejeitado'
            )
            
            this.refreshPayments()
          })
          .catch(({response}) => {
            if (response) {
              let totalOwing = response.data.totalOwing,
                  payment = response.data.payment

              this.$modal.fire({
                icon: 'error',
                iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                title: 'Erro ao confirmar',
                html: `
                  <div class="small">Este pagamento excede o total que resta pagar do pedido.</div>
                  <br/>
                  <div>Valor do pagamento: <strong>${this.$helpers.valueToBRL(payment)}</strong></div>
                  <div class="mb-1">Resta pagar: <strong>${this.$helpers.valueToBRL(totalOwing)}</strong></div>
                `,
                confirmButtonText: 'OK',
                showCancelButton: false
              })
            }
          })
          .then(() => {
            payment.isLoading = false
          })
      },
      refreshPayments() {
        this.isLoading = true

        axios.get('/caixa-diario/payments')
          .then(response => {
            let payments = response.data.payments.map(payment => {
              return {...payment, isLoading: false}
            })

            this.payments = []
            this.payments.push(...payments)
          })
          .catch(() => {})
          .then(() => {
            this.isLoading = false
          })
      }
    },
    mounted() {
      this.refreshPayments()
    }
  }
</script>