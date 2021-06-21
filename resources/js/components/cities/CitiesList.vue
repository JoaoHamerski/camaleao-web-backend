<template>
  <div>
    <div class="mb-3">
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
        >Editar</button>
      </div>
    </div>

    <table class="table table-sm">
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
          <td>{{ city.name }}</td>
          <td>{{ city.state ? city.state.name : 'N/A'}}</td>
          <td nowrap class="text-center">
            <a :href="`/gerenciamento/cidades/${city.id}`" 
              class="btn btn-sm btn-outline-primary mr-3"
              content="Clientes da cidade"
              v-tippy="{placement: 'bottom', duration: 150, arrow: true}"
            >
              <i class="fas fa-users px-2"></i>
            </a>
            <button @click="selectedCity = city"
              content="Editar"
              v-tippy="{placement: 'bottom', duration: 150, arrow: true}" 
              class="btn btn-sm btn-outline-primary mr-3"
              data-toggle="modal"
              data-target="#cityEditModal"
            >
              <i class="fas fa-edit px-2"></i>
            </button>

            <button @click="selectedCity = city" 
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

    <CityEditModal v-if="selectedCity" 
      id="cityEditModal"
      ref="cityEditModal"
      @closed="selectedCity = null"
    >
      <template #title>Alterar cidade</template>
      <template #content>
        <CityForm 
          :city="selectedCity"
          @updated="onCityUpdate" 
        />
      </template>
    </CityEditModal>

    <CityEditModal v-if="selectedCities.length"
      id="citiesEditModal"
      ref="citiesEditModal"
    >
      <template #title> Alterar cidades</template>
      <template #content>
        <CitiesForm 
          :cities="selectedCities" 
          @updated="onCitiesUpdate"

        />
      </template>
    </CityEditModal>

    <CityDeleteModal v-if="selectedCity"
      :city="selectedCity"
      :cities="cities"
      @closed="selectedCity = null"
      @deleted="onDeleted"
    />
  </div>
</template>

<script>
  import CityEditModal from './CityEditModal'
  import CityDeleteModal from './CityDeleteModal'
  import CityForm from './CityForm'
  import CitiesForm from './CitiesForm'

  export default {
    components: {
      CityEditModal,
      CityDeleteModal,
      CityForm,
      CitiesForm
    },
    data: function() {
      return {
        selectedCity: null,
        cities: []
      }
    },
    computed: {
      selectedCities() {
        return this.cities.filter(city => city.selected)
      }
    },
    methods: {
      refresh() {
        axios.get('/gerenciamento/cidades/list')
          .then(response => {
            this.cities = response.data.cities.map(city => {
              return {...city, selected: false}
            })
          })
      },
      onUpdate() {
        this.refresh()
      },
      onCityUpdate() {
        this.onUpdate()
        this.$refs.cityEditModal.close()
      },
      onCitiesUpdate() {
        this.onUpdate()
        this.$refs.citiesEditModal.close()
      },
      onDeleted() {
        this.refresh()
      }
    },
    mounted() {
      this.refresh()
    }
  }
</script>