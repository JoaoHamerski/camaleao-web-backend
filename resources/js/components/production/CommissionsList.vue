<template>
  <div>
    <div v-if="somethingWasChanged" 
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
            <th class="text-center">CADASTRO EM</th>
            <th class="text-center">IMAGEM</th>
            <th class="text-center"><i class="fas fa-clipboard-check fa-fw fa-lg"></i></th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="commission in commissions" :key="commission.id"
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

                <i class="fas fa-info-circle fa-fw clickable ml-1" 
                  data-toggle="dropdown"
                ></i>

                <CommissionDetailsDropdown 
                  :commission="commission" 
                  :userRole="userRole"
                />
              </div>
            </td>

            <td class="text-center">
              {{ moment(commission.order.created_at).format('DD/MM HH:mm') }}
            </td>

            <td class="text-center">
              <Tippy v-if="! commission.order.art_paths.length" 
                :to="`commission-${commission.id}`"
                placement="top"
                :duration="150"
                arrow
              >
                Nenhuma imagem armazenada ainda
              </Tippy>

              <span :name="`commission-${commission.id}`">
                <button class="btn btn-outline-primary btn-sm"
                  data-toggle="modal"
                  data-target="#commissionImageModal"
                  @click="selectImages(commission)"
                  :disabled="! commission.order.art_paths.length"
                >
                  <i class="fas fa-eye fa-fw"></i>
                </button>
              </span>
            </td>

            <td class="text-center">
              <div v-if="! commission.pivot.confirmed_at">
                <button class="btn btn-outline-success btn-sm px-3"
                  :disabled="commission.isLoading"
                  @click="confirm(commission)"
                >
                  <i class="fas fa-check fa-fw"></i>
                </button>
              </div>
              <div v-else>
                <i class="fas fa-check fa-fw text-success"></i>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <CommissionImageModal v-if="images.length" 
      :images="images"
    />
  </div>
</template>

<script>
  import CommissionImageModal from './CommissionImageModal'
  import CommissionDetailsDropdown from './CommissionDetailsDropdown'

  import moment from 'moment'
  import { TippyComponent } from 'vue-tippy'

  export default {
    components: {
      CommissionImageModal,
      CommissionDetailsDropdown,
      Tippy: TippyComponent 
    },
    props: {
      userRole: { default: '' }
    },
    data() {
      return {
        moment,
        images: [],
        commissions: []
      }
    },
    computed: {
      somethingWasChanged() {
        return this.commissions.some((commission) => commission.pivot.was_quantity_changed);
      }
    },
    methods: {
      confirm(commission) {
        commission.isLoading = true
        
        axios.post(`/producao/${commission.pivot.id}/confirm`)
          .then(response => {
            this.$toast.success('Comissão confirmada')
            commission.isLoading = false
            this.refresh()
          })
      },
      selectImages(commission) {
        if (commission.order.art_paths.length) {
          this.images = JSON.parse(commission.order.art_paths)
        }
      },  
      refresh() {
        axios.get('/producao/get-commissions')
          .then(response => {
            this.commissions = response.data.commissions.map((commission) => {
              return {...commission, isLoading: false}
            })
          })
          .catch(error => {
            console.log(error.response)
          })
      }
    },
    mounted() {
      this.refresh()
    }
  }
</script>