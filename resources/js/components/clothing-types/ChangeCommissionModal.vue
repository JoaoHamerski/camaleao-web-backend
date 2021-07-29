<template>
  <AppModal id="changeCommissionModal"
    ref="modal"
    modalHeaderClass="bg-primary"
    modalDialogClass="modal-dialog-centered"
  >
    <template #header>
      <h5 class="font-weight-bold text-white mb-0">
        Alterar comissão
      </h5>
    </template>

    <template #body>
      <h5 class="text-secondary text-center mb-4 mt-3">
        <span v-if="! isOrderComission">
          Comissão para <strong>{{ clothingType.name }}</strong>
        </span>
        <span v-else>
          Comissão de estampa de pedidos
        </span>
      </h5>

      <form @submit.prevent="onSubmit">
        <div class="form-group">
          <AppInput v-model="form.value"
            id="value"
            name="value"
            ref="inputValue"
            placeholder="R$ "
            :error="form.errors.get('value')"
            :mask="masks.valueBRL"
          >
            Valor da comissão
          </AppInput>
        </div>

        <div class="d-flex justify-content-between">
          <button class="btn btn-success font-weight-bold" 
            type="submit"
            :disabled="form.isLoading"
          >
            <span class="spinner-border spinner-border-sm" v-if="form.isLoading"></span>
            SALVAR
          </button>
          <button class="btn btn-light" data-dismiss="modal">Fechar</button>
        </div>
      </form>
    </template>

  </AppModal>
</template>

<script>
  import Form from '../../util/Form'
  import masks from '../../util/masks'

  export default {
    props: {
      clothingType: { default: null },
      isOrderComission: { default: false }
    },
    data() {
      return {
        masks,
        orderComission: 0,
        form: new Form({
          value: ''
        })
      }
    },
    methods: {
      populateIfClothingTypes() {
        this.form.value = this.$helpers.valueToBRL(
            this.clothingType.commission
          )
      },
      populateIfOrderCommission() {
        return new Promise((resolve, reject) => {
          axios.get('/pedidos/order-commission')
            .then(response => {
              this.form.value = this.$helpers.valueToBRL(
                response.data.commission
              )
              resolve()
            })
        })
      },
      changeClothingTypesComission() {
        this.form.isLoading = true
        this.form.submit('POST', `/tipos-de-roupas/${this.clothingType.id}/change-commission`)
          .then(response => {
            $(this.$refs.modal.$el).modal('hide')
            this.$toast.success('Valor da comissão alterada')
            this.$emit('changed')
          })
          .catch(() => {})
          .then(() => {
            this.form.isLoading = false
          })
      },
      changeOrderComission() {
        this.form.isLoading = true
        this.form.submit('POST', '/pedidos/change-order-commission')
          .then(response => {
            this.$toast.success('Valor da comissão alterada')
          })
          .catch(() => {})
          .then(() => {
            this.form.isLoading = false
            $(this.$refs.modal.$el).modal('hide')
          })
      },
      onSubmit() {
        if (this.isOrderComission) {
          this.changeOrderComission()

          return
        }

        this.changeClothingTypesComission()
      }
    },
    mounted() {
      $(this.$refs.modal.$el).on('show.bs.modal', async () => {
        if (this.clothingType) {
          this.populateIfClothingTypes()
        } else {
          await this.populateIfOrderCommission()
        }

        this.$nextTick(() => {
          this.$refs.inputValue.$refs.input.updateValue()
        })
      })
    }
  }
</script>