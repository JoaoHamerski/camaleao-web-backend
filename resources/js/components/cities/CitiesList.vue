<template>
  <div>
    <div class="mb-3">
      <button class="btn btn-success mb-3 font-weight-bold"
        data-toggle="modal"
        data-target="#cityNewModal"
      >
        <i class="fas fa-plus fa-fw mr-1"></i>Nova cidade
      </button>

      <div class="mb-3">
        <h6 class="font-weight-bold mb-0">Múltiplas ações</h6>
        <small class="text-secondary">Selecione múltiplas cidades abaixo para editar várias de uma vez</small>
      </div>

      <div v-if="selectedCities.length">
        <h6 class="text-primary font-weight-bold">
          Cidade selecionadas: {{selectedCities.length}}
        </h6>
          
        <button class="btn btn-primary btn-sm font-weight-bold px-3" 
          data-target="#citiesEditModal"
          data-toggle="modal"
          @click="$refs.citiesForm.$emit('cities-selected', selectedCities)"
        >Editar</button>
      </div>
    </div>

    <table class="table table-sm table-hover">
      <thead>
        <th></th>
        <th>Cidade</th>
        <th>Estado</th>
        <th class="text-center">Ações</th>
      </thead>

      <tbody>
        <tr v-for="city in cities" :key="city.id">
          <td>
            <div class="custom-control custom-checkbox">
              <input type="checkbox" 
                class="custom-control-input" 
                v-model="city.selected"
                :id="`cityCheck${city.id}`"
              >
              <label class="custom-control-label" :for="`cityCheck${city.id}`"></label>

            </div>
          </td>
          <td @click="city.selected = ! city.selected">{{ city.name }}</td>
          <td @click="city.selected = ! city.selected">{{ city.state ? city.state.name : 'N/A'}}</td>
          <td nowrap class="text-center">
            <a :href="`/gerenciamento/cidades/${city.id}`" 
              class="btn btn-sm btn-primary mr-3"
              content="Clientes da cidade"
              v-tippy="{placement: 'bottom', duration: 150, arrow: true}"
            >
              <i class="fas fa-users px-2"></i>
            </a>
            <button @click="$refs.cityForm.$emit('city-selected', city)"
              content="Editar"
              v-tippy="{placement: 'bottom', duration: 150, arrow: true}" 
              class="btn btn-sm btn-outline-primary mr-3"
              data-toggle="modal"
              data-target="#cityEditModal"
            >
              <i class="fas fa-edit px-2"></i>
            </button>

            <button @click="$refs.cityDeleteModal.$emit('city-selected', city)" 
              class="btn btn-sm btn-outline-danger" 
              content="Excluir"
              v-tippy="{placement: 'bottom', duration: 150, arrow: true}" 
              data-toggle="modal"
              data-target="#cityDeleteModal"
            >
              <i class="fas fa-trash-alt px-2 "></i>
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
          Nenhuma cidade cadastrada
        </div>
      </div>
    </InfiniteLoading>

    <CityModal
      id="cityNewModal"
      ref="cityNewModal"
    >
      <template #title>Nova cidade</template>
      <template #content>
        <CityForm :isEdit="false" 
          @created="onCityCreate"
        />
      </template>
    </CityModal>

    <CityModal 
      id="cityEditModal"
      ref="cityEditModal"
      @closed="selectedCity = null"
      :isEdit="true"
    >
      <template #title>Alterar cidade</template>
      <template #content>
        <CityForm ref="cityForm"
          @updated="onCityUpdate" 
          :isEdit="true"
        />
      </template>
    </CityModal>

    <CityModal
      id="citiesEditModal"
      ref="citiesEditModal"
    >
      <template #title> Alterar cidades</template>
      <template #content>
        <CitiesForm ref="citiesForm"
          @updated="onCitiesUpdate"
        />
      </template>
    </CityModal>

    <CityDeleteModal ref="cityDeleteModal"
      @closed="selectedCity = null"
      @deleted="onDeleted"
    />
  </div>
</template>

<script>
  import CityModal from './CityModal'
  import CityDeleteModal from './CityDeleteModal'
  import CityForm from './CityForm'
  import CitiesForm from './CitiesForm'
  import InfiniteLoading from 'vue-infinite-loading'

  export default {
    components: {
      InfiniteLoading,
      CityModal,
      CityDeleteModal,
      CityForm,
      CitiesForm
    },
    data: function() {
      return {
        selectedCity: null,
        cities: [],
        page: 1,
        infiniteId: +new Date()
      }
    },
    computed: {
      selectedCities() {
        return this.cities.filter(city => city.selected)
      }
    },
    methods: {
      refreshInfiniteHandler() {
        this.cities = []
        this.page = 1
        this.infiniteId += 1
      },
      infiniteHandler($state) {
        axios.get('/gerenciamento/cidades/list', {
          params: {
            page: this.page
          }
        })
          .then(({data}) => {
            if (data.cities.data.length) {
              this.page += 1
              let cities = data.cities.data.map(city => {
                return {...city, selected: false}
              })

              this.cities.push(...cities)

              $state.loaded()
            } else {
              $state.complete()
            }
          })
      },
      onUpdate() {
        this.refreshInfiniteHandler()
      },
      onCityUpdate() {
        this.onUpdate()
        this.$refs.cityEditModal.close()
      },
      onCityCreate() {
        this.$refs.cityNewModal.close()
        this.refreshInfiniteHandler()
      },
      onCitiesUpdate() {
        this.onUpdate()
        this.$refs.citiesEditModal.close()
      },
      onDeleted() {
        this.refreshInfiniteHandler()
      }
    }
  }
</script>