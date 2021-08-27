<script>
import ClothingTypesForm from './ClothingTypesForm'
import ChangeCommissionModal from './ChangeCommissionModal'
import Draggable from 'vuedraggable'

export default {
  components: {
    ClothingTypesForm,
    Draggable,
    ChangeCommissionModal
  },
  data: function() {
    return {
      selectedClothingType: null,
      isOrderComission: false,
      drag: false,
      name: '',
      error: '',
      isLoading: false,
      clothingTypes: []
    }
  },
  mounted() {
    this.refresh()
  },
  methods: {
    onChange(changed) {
      this.isLoading = true

      axios.patch('/tipos-de-roupas/update-order', {
        newIndex: changed.moved.newIndex,
        oldIndex: changed.moved.oldIndex
      })
        .then(() => {})
        .catch(() => {})
        .then(() => {
          this.isLoading = false
          this.refresh()
        })
    },
    update(type) {
      type.isLoading = true
      axios.patch(`/tipos-de-roupas/${type.id}`, {
        name: this.name
      })
        .then(() => {
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
      for (const _type of this.clothingTypes) {
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
        .then(() => {
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
          const clothingTypes = response.data.clothing_types.map(type => {
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
  }
}
</script>

<template>
  <div class="position-relative">
    <AppLoading v-if="isLoading" />

    <div class="mb-4 text-secondary text-center small">
      Você pode criar um novo tipo de camisa para o formulário de pedidos.
      <hr>
      Ou esconder tipos que não quer que seja mais preenchido, os tipos escondidos ainda aparecerão em pedidos antigos, porém não aparecerão em formulários novos.
    </div>

    <div class="mb-2">
      <button
        class="btn btn-primary"
        data-toggle="modal"
        data-target="#changeCommissionModal"
        @click="isOrderComission = true"
      >
        Comissão de estampa
      </button>
    </div>

    <div class="mb-2 ">
      <ClothingTypesForm @created="refresh" />
    </div>

    <Draggable
      v-model="clothingTypes"
      handle=".handle"
      :animation="200"
      tag="ul"
      class="list-group"
      @start="drag = true"
      @stop="drag = false"
      @change="onChange"
    >
      <transition-group
        type="transition"
        :name="! drag ? 'transition-list' : null"
        tag="tbody"
      >
        <li
          v-for="type in clothingTypes"
          :key="type.key"
          class="list-group-item d-flex justify-content-between align-items-center"
        >
          <div class="d-flex">
            <span
              v-if="false"
              class="mr-2 justify-content-center"
            >
              <i class="fas fa-bars handle" />
            </span>

            <span v-if="type.isEdit">
              <AppInput
                id="name-edit"
                v-model="name"
                name="name-edit"
                :error="error"
                @focus.capture="error = ''"
              />
            </span>
            <div v-else>
              <div>
                {{ type.name }}
              </div>
              <small class="text-secondary">
                Comissão: <strong>{{ $helpers.valueToBRL(type.commission) }}</strong>
              </small>
            </div>
          </div>

          <span
            v-if="type.isEdit"
            class="text-center"
          >
            <button
              class="btn btn-sm btn-success"
              :disabled="type.isLoading"
              @click="update(type)"
            >
              <span
                v-if="type.isLoading"
                class="spinner-border spinner-border-sm"
              />
              Salvar
            </button>
            <button
              class="btn btn-sm btn-light ml-2"
              @click="cancelEdit(type)"
            >Cancelar</button>
          </span>

          <span
            v-else
            class="text-center"
          >
            <div class="mb-2 d-flex justify-content-between">
              <button
                v-tippy="{duration: 150, arrow: true, placement: 'top'}"
                :content="type.is_hidden ? 'Exibir' : 'Esconder'"
                class="btn btn-sm btn-outline-primary px-4"
                @click="toggleHide(type)"
              >
                <i
                  v-if="type.is_hidden"
                  class="fas fa-eye-slash fa-fw"
                />
                <i
                  v-else
                  class="fas fa-eye fa-fw"
                />
              </button>

              <button
                v-tippy="{duration: 150, arrow: true, placement: 'top'}"
                class="btn btn-sm btn-success px-4"
                content="Editar"
                @click="edit(type)"
              >
                <i class="fas fa-edit fa-fw" />
              </button>
            </div>
            <button
              class="btn btn-sm btn-primary"
              data-toggle="modal"
              data-target="#changeCommissionModal"
              @click="selectedClothingType = type"
            >COMISSÃO COSTURA</button>
          </span>
        </li>
      </transition-group>
    </Draggable>

    <ChangeCommissionModal
      v-if="selectedClothingType || isOrderComission"
      :clothing-type="selectedClothingType"
      :is-order-comission="isOrderComission"
      @changed="refresh"
    />
  </div>
</template>

<style scoped>
  .handle:hover {
    cursor: grabbing;
  }
</style>
