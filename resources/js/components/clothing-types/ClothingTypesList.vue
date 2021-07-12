<template>
  <div class="position-relative">
    <AppLoading v-if="isLoading" />
    <div class="mb-4 text-secondary text-center small">
      Você pode criar um novo tipo de camisa para o formulário de pedidos.
      <hr>
      Ou esconder tipos que não quer que seja mais preenchido, os tipos escondidos ainda aparecerão em pedidos antigos, porém não aparecerão em formulários novos. 
    </div>

    <div class="mb-2 ">
      <ClothingTypesForm @created="refresh" />
    </div>


    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th></th>
            <th>NOME</th>
            <th class="text-center">AÇÕES</th>
          </tr>
        </thead>

        <Draggable v-model="clothingTypes" 
          handle=".handle"
          :animation="200"
          @start="drag = true"
          @stop="drag = false"
        >
          <transition-group type="transition"
            :name="! drag ? 'transition-list' : null"
            tag="tbody"
          >
            <tr v-for="type in clothingTypes" :key="type.key">
              <td>
                <i class="fas fa-bars handle"></i>
              </td>
              <td v-if="type.isEdit">
                <AppInput v-model="name"
                  id="name-edit"
                  name="name-edit"
                  :error="error"
                  @focus.capture="this.error = ''"
                />
              </td>
              <td v-else nowrap>{{ type.name }}</td>
              
              <td v-if="type.isEdit" class="text-center">
                <button 
                  class="btn btn-sm btn-success" 
                  @click="update(type)"
                  :disabled="type.isLoading"
                >
                  <span v-if="type.isLoading" class="spinner-border spinner-border-sm"></span>
                  Salvar
                </button>
                <button class="btn btn-sm btn-light ml-2" @click="cancelEdit(type)">Cancelar</button>
              </td>

              <td v-else class="text-center">
                <button v-if="type.is_hidden" @click="toggleHide(type)" 
                  class="btn btn-sm btn-outline-success"
                >EXIBIR</button>
                <button v-else @click="toggleHide(type)"
                  class="btn btn-sm btn-outline-primary"
                >ESCONDER</button>

                <button class="btn btn-sm btn-success ml-2" @click="edit(type)">EDITAR</button>
              </td>
            </tr>
          </transition-group>
        </Draggable>
      </table>
    </div>
  </div>
</template>

<style scoped>
  .handle:hover {
    cursor: grabbing;
  }
</style>

<script>
  import ClothingTypesForm from './ClothingTypesForm'
  import Draggable from 'vuedraggable'

  export default {
    components: {
      ClothingTypesForm,
      Draggable
    },
    data: function() {
      return {
        drag: false,
        name: '',
        error: '',
        isLoading: false,
        clothingTypes: []
      }
    },
    methods: {
      update(type) {
        type.isLoading = true
        axios.patch(`/tipos-de-roupas/${type.id}`, {
          name: this.name
        })
          .then(response => {
            this.cancelEdit(type)
            this.refresh()
          })
          .catch(error => {
            if (error.response.data.errors) {
              this.error = error.response.data.errors.name[0]
            }
          })
          .then(() => {
            type.isLoading = false
          })
      },
      edit(type) {
        for (let _type of this.clothingTypes) {
          _type.isEdit = false
        }

        type.isEdit = true
        this.name = type.name
      },
      cancelEdit(type) {
        this.name = ''
        type.isEdit = false
      },
      toggleHide(type) {
        this.isLoading = true

        axios.patch(`/tipos-de-roupas/${type.id}/toggle-hide`)
          .then(response => {
            if (type.is_hidden) {
              this.$toast.success(type.name + ' está sendo exibido')
            } else {
              this.$toast.success(type.name + ' está ocultado')
            }
            this.refresh()
          })
          .catch(() => {})
          .then(() => {
            this.isLoading = false
          })
      },
      refresh() {
        this.isLoading = true
        axios.get('/tipos-de-roupas/list')
          .then(response => {
            let clothingTypes = response.data.clothing_types.map(type => {
              return {...type, isEdit: false, isLoading: false}
            })

            this.clothingTypes = []
            this.clothingTypes.push(...clothingTypes)
          })
          .catch(() => {})
          .then(() => {
            this.isLoading = false
          })
      }
    },
    mounted() {
      this.refresh()
    }
  }
</script>