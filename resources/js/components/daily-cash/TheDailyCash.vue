<script>
import DailyPaymentModal from './DailyPaymentModal'
import PendenciesModal from './PendenciesModal'

import { TippyComponent } from 'vue-tippy'
import moment from 'moment'
moment.locale('pt-BR')

import BaseInput from '@/components/util/BaseInput.vue'

export default {
  components: {
    DailyPaymentModal,
    PendenciesModal,
    Tippy: TippyComponent,
    BaseInput
  },
  props: {
    userRole: {
      type: Number,
      default: 0
    }
  },
  data: () => {
    return {
      moment,
      isLoading: false,
      payments: [],
      totalPendencies: 0,
      currentDate: ''
    }
  },
  mounted () {
    this.refresh()
  },
  methods: {
    onLoadPendencies (date) {
      this.refreshPayments({date, only_pendency: true})
    },
    onPaymentCreated () {
      this.refresh()
    },
    assign (payment, confirmation) {
      payment.isLoading = true

      axios.post(`/caixa-diario/${payment.id}/assign-confirmation`, {
        confirmation
      })
        .then(() => {
          this.$toast.success(
            confirmation
              ? 'Pagamento aceito'
              : 'Pagamento rejeitado'
          )

          if (moment(payment.created_at).isBefore(moment(), 'day')) {
            this.refresh({date: this.currentDate, only_pendency: true})
            this.$refs.pendenciesModal.$emit('refresh-pendencies')
          } else {
            this.refresh()
          }
        })
        .catch(({response}) => {
          if (response) {
            const totalOwing = response.data.totalOwing,
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
    refreshTotalPendencies (params = {}) {
      axios.get('/caixa-diario/get-total-pendencies ', { params })
        .then(response => {
          this.totalPendencies = response.data.totalPendencies
        })
    },
    refreshPayments (params = {}) {
      this.isLoading = true

      if (params.date) {
        this.currentDate = params.date
      } else {
        this.currentDate = new Date()
      }

      axios.get('/caixa-diario/payments', { params })
        .then(response => {
          const payments = response.data.payments.map(payment => {
            return {...payment, isLoading: false}
          })

          this.payments = []
          this.payments.push(...payments)
        })
        .catch(() => {})
        .then(() => {
          this.isLoading = false
        })

      this.refreshTotalPendencies()
    },
    refresh (params) {
      this.refreshPayments(params)
    }
  }
}
</script>
<template>
  <div>
    <AppLoading v-if="isLoading" />

    <BaseInput placeholder="Olá mundo" />

    <div class="d-flex justify-content-between">
      <div>
        <button
          class="btn btn-lg btn-primary font-weight-bold"
          data-toggle="modal"
          data-target="#dailyPaymentModal"
        >
          <i class="fas fa-plus fa-fw mr-1" />Nova entrada
        </button>
      </div>

      <div>
        <Tippy
          v-if="! totalPendencies"
          to="pendenciesBtn"
          placement="bottom"
          :duration="150"
          arrow
        >
          Sem pendências no momento
        </Tippy>

        <span
          class="d-inline-block"
          name="pendenciesBtn"
        >
          <button
            class="btn text-white font-weight-bold"
            :class="! totalPendencies ? 'btn-success' : 'btn-warning'"
            data-toggle="modal"
            data-target="#pendenciesModal"
            :disabled="! totalPendencies"
          >
            Pendências
            <span class="badge badge-pill badge-light">{{ totalPendencies }}</span>
          </button>
        </span>

        <button
          class="btn btn-outline-primary"
          @click="refresh"
        >
          <i class="fas fa-sync-alt fa-fw" />
        </button>
      </div>
    </div>

    <div class="text-center mt-4">
      <h4
        v-tippy="{duration: 150}"
        class="font-weight-bold mb-0 horizontal-line mb-3"
        :content="moment(currentDate).format('DD [de] MMMM')"
      >
        <span>{{ moment(currentDate).format('DD/MM') }}</span>
      </h4>

      <h6 class="text-secondary">
        {{ moment(currentDate).isSame(moment(), 'day') ? 'Pagamentos de hoje' : 'Pagamentos antigos' }}
      </h6>
    </div>

    <div
      v-if="!! payments.length"
      class="table-responsive mt-4"
    >
      <table class="table">
        <thead>
          <th>PEDIDO</th>
          <th>CLIENTE</th>
          <th>VALOR</th>
          <th>VIA</th>
          <th class="text-center">
            HORÁRIO
          </th>
          <th class="text-center">
            <i class="fas fa-clipboard-check fa-lg" />
          </th>
        </thead>

        <tbody>
          <tr
            v-for="payment in payments"
            :key="payment.id"
            :class="{
              'table-success': payment.is_confirmed == true,
              'table-danger': payment.is_confirmed == false,
              'table-warning': payment.is_confirmed == null
            }"
          >
            <td class="font-weight-bold">
              <a
                :href="payment.order.path"
                target="_blank"
              >{{ payment.order.code }}</a>
            </td>
            <td
              nowrap
              class="font-weight-bold"
            >
              <a
                :href="payment.order.client.path"
                target="_blank"
              >{{ payment.order.client.name }}</a>
            </td>
            <td class="font-weight-bold">
              {{ $helpers.valueToBRL(payment.value) }}
            </td>
            <td nowrap>
              {{ payment.via.name }}
            </td>
            <td class="text-center">
              {{ moment(payment.created_at).format('HH:mm') }}
            </td>

            <td
              v-if="userRole === 3"
              nowrap
              class="text-center"
            >
              <div v-if="payment.is_confirmed === null">
                <button
                  class="btn btn-outline-primary btn-sm px-3"
                  :disabled="payment.isLoading"
                  @click="assign(payment, true)"
                >
                  <i class="fas fa-check fa-fw" />
                </button>

                <button
                  class="btn btn-outline-danger btn-sm px-3"
                  :disabled="payment.isLoading"
                  @click="assign(payment, false)"
                >
                  <i class=" fas fa-times fa-fw" />
                </button>
              </div>
              <span
                v-else-if="payment.is_confirmed == true"
                class="font-weight-bold"
              >
                <i class="fas fa-check text-success" />
              </span>
              <span
                v-else-if="payment.is_confirmed == false"
                class="font-weight-bold"
              >
                <i class="fas fa-times text-danger" />
              </span>
            </td>

            <td
              v-else
              class="font-weight-bold text-center"
            >
              <span v-if="payment.is_confirmed === null">
                <i class="fas fa-minus text-warning" />
              </span>
              <span v-else-if="payment.is_confirmed == true">
                <i class="fas fa-check text-success" />
              </span>
              <span v-else-if="payment.is_confirmed == false">
                <i class="fas fa-times text-danger" />
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div
      v-else
      class="text-center text-secondary my-5"
    >
      <h5>Nenhum pagamento registrado</h5>
    </div>

    <DailyPaymentModal @created="onPaymentCreated" />
    <PendenciesModal
      ref="pendenciesModal"
      @load-pendencies="onLoadPendencies($event)"
    />
  </div>
</template>
