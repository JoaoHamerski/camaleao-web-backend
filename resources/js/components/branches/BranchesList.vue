<template>
  <div>
    <BranchesButtons @refresh="refreshInfiniteHandler" />

    <div class="table-responsive">
      <table class="table mt-3">
        <thead>
          <tr>
            <th>Filial</th>
            <th>Transportadora</th>
            <th class="text-center">Cidades</th>
            <th class="text-center">Ações</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="branch in branches" :key="branch.id">
            <td class="align-middle" v-if="branch.city" nowrap>
              {{ branch.city.name }} {{ branch.city.state ? ' - ' + branch.city.state.abbreviation : '' }}
            </td>
            <td v-else class="align-middle text-danger">[cidade deletada]</td>

            <td v-if="branch.city.shipping_company" class="align-middle" nowrap>
              {{ branch.city.shipping_company.name }}
            </td>
            <td v-else class="align-middle text-danger">
              [transportadora deletada]
            </td>

            <td nowrap>
              <ul class="list-group list-group-sm">
                <li v-for="city in branch.cities" :key="`city-${city.id}`"
                  class="list-group-item"
                >
                  {{ city.name }} {{ city.state ? ' - ' + city.state.abbreviation : '' }}
                </li>
              </ul>
            </td>
            
            <td class="text-center align-middle">
              <button class="btn btn-outline-primary btn-sm mb-2"
                @click="select(branch)"
                data-toggle="modal"
                data-target="#editBranchModal"
                v-tippy="{placement: 'bottom', duration: 150, arrow: true}"
                content="Editar"
              >
                <i class="fas fa-edit px-2"></i>
              </button>

              <br>

              <button @click="destroy(branch)" 
                class="btn btn-outline-danger btn-sm"
                v-tippy="{placement: 'bottom', duration: 150, arrow: true}"
                content="Excluir"
              >
                <i class="fas fa-trash-alt px-2"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
 
    <InfiniteLoading @infinite="infiniteHandler"
      :identifier="infiniteId"
    >
      <div slot="spinner">
        <div class="spinner-grow text-primary"></div>
      </div>

      <div slot="no-more"></div>

      <div slot="no-results">
        <div class="text-secondary small my-5">
          Nenhuma filial cadastrada
        </div>
      </div>
    </InfiniteLoading>

    <EditBranchModal ref="editBranchModal" 
      @refresh="refreshInfiniteHandler" 
    />
  </div>
</template>

<script>
  import BranchesButtons from './BranchesButtons'
  import InfiniteLoading from 'vue-infinite-loading'
  import EditBranchModal from './EditBranchModal'

  export default {
    components: {
      BranchesButtons,
      InfiniteLoading,
      EditBranchModal
    },
    data: function() {
      return {
        selectedBranch: null,
        branches: [],
        page: 1,
        infiniteId: +new Date()
      }
    },
    methods: {
      select(branch) {
        this.$refs.editBranchModal.$emit('branch-selected', branch)
      },
      destroy(branch) {
        this.$modal.fire({
          icon: 'error',
          iconHtml: '<i class="fas fa-trash-alt fa-fw"></i>',
          title: 'Você tem certeza?',
          html: `
            Você está deletando a filial 
            de <strong>${branch.city ? branch.city.name : '[cidade deletada]'}</strong>
          `
        })
          .then(response => {
            if (response.isConfirmed) {
              axios.delete(`/gerenciamento/filiais/${branch.id}`)
                .then(() => {
                  this.$toast.success('Filial deletada!')
                  this.refreshInfiniteHandler()
                })
            }
          })
      },
      refreshInfiniteHandler() {
        this.branches = []
        this.page = 1
        this.infiniteId += 1
      },
      infiniteHandler($state) {
        axios.get('/gerenciamento/filiais/list', {
          params: {
            page: this.page
          }
        })
          .then(({data}) => {
            if (data.branches.data.length) {
              this.page += 1
              this.branches.push(...data.branches.data)
              
              $state.loaded()
            } else {
              $state.complete()
            }
          })
          .catch(() => {})
      }
    }
  }
</script>