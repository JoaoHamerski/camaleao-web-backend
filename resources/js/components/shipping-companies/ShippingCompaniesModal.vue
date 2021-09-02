<script>
import Form from '../../util/Form'

export default {
  data: function () {
    return {
      companyName: '',
      isLoading: false,
      newCompany: false,
      companies: [],
      form: new Form({
        name: ''
      })
    }
  },
  mounted () {
    this.$root.$on('REFRESH_SHIPPING_COMPANIES_LIST', () => {
      axios.get('/transportadoras/list')
        .then(response => {
          this.companies = response.data.companies.map(company => {
            return {...company, edit: false}
          })
        })
    })
  },
  methods: {
    enableEdit (shippingCompany) {
      for (const company of this.companies) {
        company.edit = false
      }

      shippingCompany.edit = true
    },
    update (shippingCompany, name) {
      this.isLoading = true

      axios.patch(`/transportadoras/${shippingCompany.id}`, { name })
        .then(() => {
          this.isLoading = false
          this.$root.$emit('REFRESH_SHIPPING_COMPANIES_LIST')
          this.$toast.success('Nome alterado')
          this.$emit('refresh')
        })
        .catch(() => {  })
        .then(() => {
          this.isLoading = false
        })
    },
    destroy (shippingCompany) {

      this.$modal.fire({
        icon: 'error',
        iconHtml: '<i class="fas fa-trash-alt fa-fw"></i>',
        title: 'Você tem certeza?',
        text: `
            Deletando a transportandora você
            terá que atualizar as
            filiais dessa transportadora. E também atualizar os clientes que foram cadastrados nessa transportadora.
          `
      })
        .then(response => {
          if (response.isConfirmed) {
            this.isLoading = true
            axios.delete(`/transportadoras/${shippingCompany.id}`)
              .then(() => {
                this.$root.$emit('REFRESH_SHIPPING_COMPANIES_LIST')
                this.$toast.success('Transportadora deletada')
                this.$emit('refresh')
              })
              .catch(() => {})
              .then(() => {
                this.isLoading = false
              })
          }
        })
    },
    onSubmit () {
      this.form.isLoading = true

      this.form.submit('POST', '/transportadoras')
        .then(() => {
          this.newCompany = false
          this.$root.$emit('REFRESH_SHIPPING_COMPANIES_LIST')
          this.form.reset()
          this.$toast.success('Transportadora criada')
          this.$emit('refresh')
        })
        .catch(() => {})
        .then(() => {
          this.form.isLoading = false
        })
    }
  }
}
</script>

<template>
  <AppModal
    id="shippingCompaniesModal"
    ref="modal"
    modal-dialog-class="modal-dialog-centered"
    modal-header-class="bg-primary"
  >
    <template #header>
      <h5 class="font-weight-bold text-white mb-0">
        <i class="fas fa-truck fa-fw mr-2" />Gerencie as transportadoras
      </h5>
    </template>

    <template #content>
      <AppLoading v-if="isLoading" />
    </template>

    <template #body>
      <button
        class="btn btn-sm font-weight-bold mb-3"
        :class="[newCompany ? 'btn-success' : 'btn-outline-success']"
        @click="() => {
          newCompany = ! newCompany

          $nextTick(() => {
            if (newCompany) {
              $refs.newCompanyInput.focusInput()
            }
          })
        }"
      >
        <i class="fas fa-plus fa-fw mr-1" />Nova transportadora
      </button>

      <div v-show="! companies.length">
        <h5 class="text-center text-secondary mt-3 mb-4">
          Nenhuma transportadora cadastrada
        </h5>
      </div>

      <table
        v-show="companies.length"
        class="table"
      >
        <thead>
          <tr>
            <th>Nome</th>
            <th class="text-center">
              Ações
            </th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="company in companies"
            :key="company.id"
          >
            <td
              v-if="! company.edit"
              class="w-50"
            >
              {{ company.name }}
            </td>
            <td v-else>
              <AppInput
                id="name"
                v-model="companyName"
                input-class="form-control-sm"
              />
            </td>

            <td
              v-if="! company.edit"
              nowrap
              class="text-center w-25"
            >
              <button
                class="btn btn-sm btn-outline-primary px-2 mr-3"
                @click="enableEdit(company); companyName = company.name"
              >
                <i class="fas fa-edit px-2" />
              </button>

              <button
                class="btn btn-sm btn-outline-danger px-2"
                @click="destroy(company)"
              >
                <i class="fas fa-trash-alt px-2" />
              </button>
            </td>
            <td
              v-else
              class="text-center w-25"
              nowrap
            >
              <button
                class="btn btn-sm btn-success font-weight-bold mr-3"
                @click="update(company, companyName)"
              >
                Salvar
              </button>

              <button
                class="btn btn-sm btn-light"
                @click="company.edit = false"
              >
                Cancelar
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </template>

    <template #footer>
      <form @submit.prevent="onSubmit">
        <div
          v-show="newCompany"
          class="form-group"
        >
          <AppInput
            id="name"
            ref="newCompanyInput"
            v-model="form.name"
            placeholder="Digite o nome da nova transportadora..."
            :error="form.errors.get('name')"
          />
        </div>

        <div class="form-group mb-0 d-flex justify-content-between">
          <div v-show="newCompany">
            <button
              type="submit"
              class="btn btn-success font-weight-bold"
              :disabled="form.isLoading"
            >
              <span
                v-if="form.isLoading"
                class="spinner-border spinner-border-sm mr-1"
              />
              Salvar
            </button>
            <button
              class="btn btn-light"
              @click.prevent="newCompany = false"
            >
              Cancelar
            </button>
          </div>

          <button
            data-dismiss="modal"
            class="btn btn-light ml-auto"
          >
            Fechar
          </button>
        </div>
      </form>
    </template>
  </AppModal>
</template>
