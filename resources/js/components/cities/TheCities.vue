<script>
import NewCityModal from './NewCityModal'
import EditCityModal from './EditCityModal'
import EditCitiesModal from './EditCitiesModal'
import DeleteCityModal from './DeleteCityModal'
import InfiniteLoading from 'vue-infinite-loading'

export default {
  components: {
    NewCityModal,
    EditCityModal,
    EditCitiesModal,
    InfiniteLoading,
    DeleteCityModal,
  },
  data: function () {
    return {
      searchWasMade: false,
      name: '',
      selectedCity: null,
      cities: [],
      page: 1,
      infiniteId: +new Date()
    }
  },
  computed: {
    selectedCities () {
      return this.cities.filter(city => city.selected)
    }
  },
  watch: {
    name (value) {
      if (value.length === 0 && this.searchWasMade) {
        this.clearSearch()
      }
    }
  },
  methods: {
    clearSearch () {
      this.name = ''
      this.searchWasMade = false
      this.refreshInfiniteHandler()
    },
    searchByName () {
      this.searchWasMade = true
      this.refreshInfiniteHandler()
    },
    refreshInfiniteHandler () {
      this.cities = []
      this.page = 1
      this.infiniteId += 1
    },
    infiniteHandler ($state) {
      axios.get('/gerenciamento/cidades/list', {
        params: {
          page: this.page,
          name: this.name
        }
      })
        .then(({data}) => {
          if (data.cities.data.length) {
            this.page += 1
            const cities = data.cities.data.map(city => {
              return {...city, selected: false}
            })

            this.cities.push(...cities)

            $state.loaded()
          } else {
            $state.complete()
          }
        })
    },
    onUpdate () {
      this.refreshInfiniteHandler()
    },
    onCitiesUpdate () {
      this.onUpdate()
      this.$refs.citiesEditModal.close()
    },
    onDeleted () {
      this.refreshInfiniteHandler()
    }
  }
}
</script>
<template>
  <div>
    <div class="mb-3">
      <div class="d-flex justify-content-between flex-column flex-md-row">
        <button
          class="btn btn-success font-weight-bold mb-3 mb-md-0"
          data-toggle="modal"
          data-target="#newCityModal"
        >
          <i class="fas fa-plus fa-fw mr-1" />Nova cidade
        </button>

        <div class="input-group col col-md-5 px-0">
          <input
            v-model="name"
            type="text"
            class="form-control"
            placeholder="Nome..."
            @keypress.enter="searchByName"
          >

          <div class="input-group-append">
            <button
              class="btn btn-outline-primary"
              @click="searchByName"
            >
              Buscar
            </button>
          </div>
        </div>
      </div>

      <div
        v-show="searchWasMade"
        class="small text-right"
      >
        <span
          class="clickable"
          @click="clearSearch"
        >
          Limpar busca
        </span>
      </div>
      <div class="mb-3" />

      <div class="mb-3">
        <h6 class="font-weight-bold mb-0">
          Múltiplas ações
        </h6>
        <small class="text-secondary">Selecione múltiplas cidades abaixo para editar várias de uma vez</small>
      </div>

      <div v-if="selectedCities.length">
        <h6 class="text-primary font-weight-bold">
          Cidade selecionadas: {{ selectedCities.length }}
        </h6>

        <button
          class="btn btn-primary btn-sm font-weight-bold px-3"
          data-target="#editCitiesModal"
          data-toggle="modal"
          @click="$refs.editCitiesModal.$refs.citiesForm.$emit('cities-selected', selectedCities)"
        >
          Editar
        </button>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-sm table-hover">
        <thead>
          <th />
          <th>Cidade</th>
          <th>Estado</th>
          <th class="text-center">
            Ações
          </th>
        </thead>

        <tbody>
          <tr
            v-for="city in cities"
            :key="city.id"
          >
            <td>
              <div class="custom-control custom-checkbox">
                <input
                  :id="`cityCheck${city.id}`"
                  v-model="city.selected"
                  type="checkbox"
                  class="custom-control-input"
                >
                <label
                  class="custom-control-label"
                  :for="`cityCheck${city.id}`"
                />
              </div>
            </td>
            <td
              nowrap
              @click="city.selected = ! city.selected"
            >
              {{ city.name }}
            </td>
            <td
              nowrap
              @click="city.selected = ! city.selected"
            >
              {{ city.state ? city.state.name : 'N/A' }}
            </td>
            <td
              nowrap
              class="text-center"
            >
              <a
                v-tippy="{placement: 'bottom', duration: 150, arrow: true}"
                :href="`/gerenciamento/cidades/${city.id}`"
                class="btn btn-sm btn-primary mr-3"
                content="Clientes da cidade"
              >
                <i class="fas fa-users px-2" />
              </a>
              <button
                v-tippy="{placement: 'bottom', duration: 150, arrow: true}"
                content="Editar"
                class="btn btn-sm btn-outline-primary mr-3"
                data-toggle="modal"
                data-target="#editCityModal"
                @click="$refs.editCityModal.$refs.cityForm.$emit('city-selected', city)"
              >
                <i class="fas fa-edit px-2" />
              </button>

              <button
                v-tippy="{placement: 'bottom', duration: 150, arrow: true}"
                class="btn btn-sm btn-outline-danger"
                content="Excluir"
                data-toggle="modal"
                data-target="#deleteCityModal"
                @click="$refs.deleteCitiesModal.$emit('city-selected', city)"
              >
                <i class="fas fa-trash-alt px-2 " />
              </button>
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
          Nenhuma cidade encontrada
        </div>
      </div>
    </InfiniteLoading>

    <NewCityModal @created="refreshInfiniteHandler" />

    <EditCityModal
      ref="editCityModal"
      @updated="refreshInfiniteHandler"
    />
    <EditCitiesModal
      ref="editCitiesModal"
      @updated="refreshInfiniteHandler"
    />

    <DeleteCityModal
      ref="deleteCitiesModal"
      @closed="selectedCity = null"
      @deleted="onDeleted"
    />
  </div>
</template>
