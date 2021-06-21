<template>
  <div>
    <BranchesButtons @refresh="refreshInfiniteHandler" />

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
          <td class="align-middle">{{ branch.city.name }}</td>
          <td class="align-middle">{{ branch.shipping_company.name }}</td>
          <td>
            <ul class="list-group list-group-sm">
              <li v-for="city in branch.cities" :key="`city-${city.id}`"
                class="list-group-item"
              >
                {{ city.name }} {{ city.state ? ' - ' + city.state.abbreviation : '' }}
              </li>
            </ul>
          </td>
          <td class="text-center align-middle">
            <button class="btn btn-outline-primary"
              v-tippy="{placement: 'bottom', duration: 150, arrow: true}"
              content="Editar"
            >
              <i class="fas fa-edit px-3"></i>
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <InfiniteLoading @infinite="infiniteHandler"
      :identifier="infiniteId"
    >
      <div slot="spinner">
        <div class="spinner-grow text-primary" role="status">
          <span class="sr-only">Loading...</span>
        </div>
      </div>

      <div slot="no-more"></div>

      <div slot="no-results">
        <div class="text-secondary small my-5">
          Nenhuma filial cadastrada
        </div>
      </div>
    </InfiniteLoading>
  </div>
</template>

<script>
  import BranchesButtons from './BranchesButtons'
  import InfiniteLoading from 'vue-infinite-loading';

  export default {
    components: {
      BranchesButtons,
      InfiniteLoading
    },
    data: function() {
      return {
        branches: [],
        page: 1,
        infiniteId: +new Date()
      }
    },
    methods: {
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