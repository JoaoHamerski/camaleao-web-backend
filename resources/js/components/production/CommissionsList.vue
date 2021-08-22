<template>
  <div>
    <div
      v-if="somethingWasChanged"
      class="text-center bg-warning-lighter text-secondary py-3"
    >
      Parece que o número de peças de alguns pedidos foram alterados.
      <div class="font-weight-bold">
        Você precisa analisar e re-confirmar esses pedidos (destacados em amarelo).
      </div>
    </div>

    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>CÓD. PEDIDO</th>
            <th>PEÇAS</th>
            <th>COMISSÃO</th>
            <th class="text-center">
              CADASTRO EM
            </th>
            <th class="text-center">
              IMAGEM
            </th>
            <th class="text-center">
              <i class="fas fa-clipboard-check fa-fw fa-lg" />
            </th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="commission in commissions"
            :key="commission.id"
            :class="{'table-warning': commission.pivot.was_quantity_changed }"
          >
            <td>
              {{ commission.order.code }}
            </td>
            <td>
              {{ commission.order.quantity }}
            </td>
            <td nowrap>
              <div class="position-static d-flex justify-content-between align-items-center">
                <div>
                  {{ $helpers.valueToBRL(commission.pivot.commission_value) }}
                </div>

                <i
                  class="fas fa-info-circle fa-fw clickable ml-1"
                  data-toggle="dropdown"
                />

                <CommissionDetailsDropdown
                  :commission="commission"
                />
              </div>
            </td>

            <td class="text-center">
              {{ moment(commission.order.created_at).format('DD/MM HH:mm') }}
            </td>

            <td class="text-center">
              <Tippy
                v-if="! commission.order.art_paths.length"
                :to="`commission-${commission.id}`"
                placement="top"
                :duration="150"
                arrow
              >
                Nenhuma imagem armazenada ainda
              </Tippy>

              <span :name="`commission-${commission.id}`">
                <button
                  class="btn btn-outline-primary btn-sm"
                  data-toggle="modal"
                  data-target="#commissionImageModal"
                  :disabled="! commission.order.art_paths.length"
                  @click="selectImages(commission)"
                >
                  <i class="fas fa-eye fa-fw" />
                </button>
              </span>
            </td>

            <td class="text-center">
              <div v-if="! commission.pivot.confirmed_at">
                <button
                  class="btn btn-outline-success btn-sm px-3"
                  :disabled="commission.isLoading"
                  @click="confirm(commission)"
                >
                  <i
                    v-if="! commission.isLoading"
                    class="fas fa-check fa-fw"
                  />
                  <span
                    v-else
                    class="spinner-border spinner-border-sm"
                  />
                </button>
              </div>
              <div v-else>
                <i class="fas fa-check fa-fw text-success" />
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <InfiniteLoading
      :identifier="infiniteId"
      @infinite="infiniteHandler"
    >
      <div slot="spinner">
        <div
          class="spinner-grow text-primary"
          role="status"
        >
          <span class="sr-only">Carregando...</span>
        </div>
      </div>

      <div slot="no-more" />

      <div slot="no-results">
        <div class="text-secondary my-5">
          Nenhum pedido encontrado
        </div>
      </div>
    </InfiniteLoading>

    <CommissionImageModal
      v-if="images.length"
      :images="images"
    />
  </div>
</template>

<script>
import CommissionImageModal from './CommissionImageModal'
import CommissionDetailsDropdown from './CommissionDetailsDropdown'
import InfiniteLoading from 'vue-infinite-loading'

import moment from 'moment'
import { TippyComponent } from 'vue-tippy'

export default {
  components: {
    CommissionImageModal,
    CommissionDetailsDropdown,
    Tippy: TippyComponent,
    InfiniteLoading
  },
  data() {
    return {
      moment,
      images: [],
      commissions: [],
      page: 1,
      infiniteId: +new Date()
    }
  },
  computed: {
    somethingWasChanged() {
      return this.commissions.some((commission) => commission.pivot.was_quantity_changed)
    }
  },
  methods: {
    confirm(commission) {
      this.$modal.fire({
        icon: 'info',
        iconHtml:'<i class="fas fa-info-circle"></i>',
        iconColor: '#3490dc',
        title: 'Você tem certeza?',
        text: 'Você está confirmando que a produção deste pedido foi efetuada.'
      })
        .then(response => {
          if (response.isConfirmed) {
            commission.isLoading = true

            axios.post(`/producao/${commission.pivot.id}/confirm`)
              .then(() => {
                this.$toast.success('Comissão confirmada')
                commission.isLoading = false
                this.refresh()
              })
          }
        })

    },
    selectImages(commission) {
      if (commission.order.art_paths.length) {
        this.images = JSON.parse(commission.order.art_paths)
      }
    },
    refresh() {
      this.commissions = []
      this.page = 1
      this.infiniteId += 1
    },
    infiniteHandler($state) {
      axios.get('/producao/get-commissions', {
        params: {
          page: this.page
        }
      })
        .then(({data}) => {
          if (data.commissions.data.length) {
            this.page += 1

            const commissions = data.commissions.data.map(commission => {
              return {...commission, isLoading: false}
            })

            this.commissions = commissions

            $state.loaded()
          } else {
            $state.complete()
          }
        })
    }
  }
}
</script>
