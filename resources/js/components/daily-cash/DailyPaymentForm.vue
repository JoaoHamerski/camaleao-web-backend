<template>
  <form @submit.prevent="onSubmit" data-type="vue"
    @focus.capture="form.errors.clear($event.target.name)"
  >
    <div class="form-group mb-4" v-if="! form.isNewClient">
      <h6 class="d-flex justify-content-between">
        <label for="clients" class="text-primary font-weight-bold">Cliente</label> 

        <button type="button" 
          class="btn btn-sm btn-success font-weight-bold ml-2 py-0"
          @click="setIsNewClient(true)"  
        >
          Cadastrar novo
        </button>
      </h6>

      <Multiselect v-model="form.client"
        :class="{'is-invalid': form.errors.has('client.id')}"
        id="clients"
        :customLabel="({name, phone}) => `${name} ${phone ? ' - ' + $helpers.maskPhone(phone) : ''}`"
        placeholder="Procure por nome ou telefone"
        :internalSearch="false"
        :options="clients.items"
        :loading="clients.isLoading"
        :clearOnSelect="false"
        selectLabel="Selecionar"
        selectedLabel="Selecionado"
        deselectLabel="Remover"
        @search-change="asyncFindClients"
        @select="onClientSelect"
        @open="form.errors.clear('client.id')"
      >
        <template slot="noResult">
          Nenhum cliente encontrado, 
          <a class="font-weight-bold" href="" 
            @click.prevent="setIsNewClient(true)"
          >cadastrar novo</a>.
        </template>

        <template slot="noOptions">
          Faça uma busca para exibir resultados
        </template>
      </Multiselect>
      <small v-if="form.errors.has('client.id')" class="text-danger">
        {{ form.errors.get('client.id') }}
      </small>
    </div>

    <div v-else class="form-group">
      <h6 class="d-flex justify-content-between">
        <label for="client" class="font-weight-bold">Nome do cliente</label>
        <button type="button" 
          class="btn btn-sm btn-primary font-weight-bold py-0"
          @click="setIsNewClient(false)"
        >
          Procurar cliente
        </button>
      </h6>

      <AppInput v-model="form.client" 
        id="client"
        name="client"
        placeholder="Digite o nome..."
        :error="form.errors.get('client')"
      />
    </div>

    <div class="form-group" v-if="! form.isNewOrder">
      <h6 class="d-flex justify-content-between">
        <label for="orders" class="font-weight-bold text-primary">Pedido</label>

        <button v-if="form.client" type="button"
          @click="setIsNewOrder(true)" 
          class="btn btn-sm btn-success font-weight-bold py-0"
        >Cadastrar novo</button>
      </h6>

      <Tippy v-if="! form.client" 
        to="tippy-order"
        :duration="150"
        arrow
      >Selecione o cliente primeiro</Tippy>

      <span class="d-block" name="tippy-order">
        <Multiselect v-model="form.order"
          :class="{'is-invalid': form.errors.has('order.id')}"
          id="orders"
          :customLabel="({code, price}) => `${code} - ${$helpers.valueToBRL(price)}`"
          :options="orders.items"
          :disabled="! form.client"
          :loading="orders.isLoading"
          placeholder="Procure por código"
          selectLabel="Selecionar"
          selectedLabel="Selecionado"
          deselectLabel="Remover"
          @open="form.errors.clear('order.id')"
        >
          <template slot="noResult">
            Nenhum pedido encontrado, 
            <a href="" class="font-weight-bold" 
              @click.prevent="setIsNewOrder(true)"
            >cadastrar novo</a>
          </template>

          <template slot="noOptions">
            Nenhum pedido em aberto, 
            <a href="" class="font-weight-bold" 
              @click.prevent="setIsNewOrder(true)"
            >
              cadastrar novo
            </a>
          </template>
        </Multiselect>

        <small v-if="form.errors.has('order.id')" class="text-danger">
          {{ form.errors.get('order.id') }}
        </small>
      </span>

      <div v-if="form.order && typeof form.order === 'object'"
        class="small text-secondary"
      >
        <div v-if="form.order.name">
          Nome: <span class="font-weight-bold">{{ form.order.name }}</span> 
        </div>

        <div>
          Falta pagar: <span class="font-weight-bold">{{ $helpers.valueToBRL(form.order.total_owing) }}
        </span>
        </div>
      </div>
    </div>

    <div v-else>
      <div class="form-group">
        <h6 class="d-flex justify-content-between">
          <label for="order" class="font-weight-bold">Código</label>
          <button v-if="! form.isNewClient" type="button" 
            @click="setIsNewOrder(false)"
            class="btn btn-sm btn-primary py-0 font-weight-bold"
          >
            Procurar pedidos
          </button>
        </h6>

        <AppInput v-model="form.order"
          id="order"
          name="order"
          placeholder="Digite o código do pedido..."
          :error="form.errors.get('order')"
        />
      </div>

      <div class="form-group">
        <AppInput v-model="form.order_value"
          id="order_value"
          name="order_value"
          placeholder="Digite o valor do pedido"
          :mask="masks.valueBRL"
          :error="form.errors.get('order_value')"
        >Valor </AppInput>
      </div>

      <div class="form-group">
        <div class="custom-control custom-checkbox">
          <input type="checkbox" v-model="form.hasReminder" 
            class="custom-control-input" 
            id="reminderCheck"
          >
          <label class="custom-control-label" for="reminderCheck">
            Adicionar lembrete
          </label>
          <i class="fas fa-question-circle fa-fw ml-1 text-primary"
            v-tippy="{arrow: true, duration: 150, placement: 'top'}"
            content="Você pode adicionar uma nota sobre o pedido para lembrar dele, visto que ele terá informações gerais pouco identificáveis."
          ></i>
        </div>
      </div>

      <div class="form-group">

        <AppInput v-model="form.reminder" v-if="form.hasReminder"
          id="reminder"
          name="reminder"
          placeholder="Algo que lembre o pedido..."
          :optional="true"
        >Lembrete: </AppInput>
      </div>
    </div>

    <h6 class="horizontal-line text-center mt-4 pt-2">
      <span class="font-weight-bold">PAGAMENTO</span>
    </h6>

    <div class="d-flex form-row">
      <div class="form-group col">
        <AppInput v-model="form.value"
          id="value"
          name="value"
          placeholder="R$ "
          :mask="masks.valueBRL"
          :error="form.errors.get('value')"
        >Valor</AppInput>
      </div>

      <div class="form-group col">
        <label for="via_id" class="font-weight-bold">Via</label>
        <select v-model="form.via_id" 
          name="via_id" 
          id="via_id" 
          class="custom-select"
          :class="{'is-invalid': form.errors.has('via_id')}"
        >
          <option value="">Selecione uma via</option>
          <option v-for="via in vias" 
            :key="via.id" 
            :value="via.id"
          >{{ via.name }}</option>
        </select>
        <small v-if="form.errors.has('via_id')" class="text-danger">
          {{ form.errors.get('via_id') }}
        </small>
      </div>
    </div>

    <div class="d-flex justify-content-between mt-3">
      <button class="btn btn-success font-weight-bold" 
        type="submit"
        :disabled="form.isLoading"
      >
          <span v-if="form.isLoading" class="spinner-border spinner-border-sm mr-1"></span>
          Registrar
      </button>

      <button class="btn btn-light" data-dismiss="modal">
        Fechar
      </button>
    </div>
  </form>
</template>

<script>
  import Form from '../../util/Form'
  import masks from '../../util/masks'
  import Multiselect from 'vue-multiselect'
  import { TippyComponent }  from 'vue-tippy'

  export default {
    components: {
      'Tippy': TippyComponent,
      Multiselect
    },
    data() {
      return {
        masks,
        vias: [],
        clients: {
          isLoading: false,
          items: []
        },
        orders: {
          isLoading: false,
          items: []
        },
        form: new Form({
          isNewClient: false,
          isNewOrder: false,
          via_id: '',
          value: '',
          client: '',
          order: '',
          reminder: '',
          hasReminder: false,
          order_value: ''
        })
      }
    },
    methods: {
      clearClientAndOrderErrors() {
        this.form.errors.clear([
          'client.id', 
          'client', 
          'order', 
          'order.id'
        ])
      },
      setIsNewClient(isNewClient) {
        this.clearClientAndOrderErrors()

        this.form.client = ''
        this.form.order = ''

        this.form.isNewClient = isNewClient

        if (isNewClient) {
          this.form.isNewOrder = true

          return
        }

        this.form.isNewOrder = false
      },
      setIsNewOrder(isNewOrder) {
        this.clearClientAndOrderErrors()
        
        this.form.order = ''
        this.form.isNewOrder = isNewOrder
      },  
      onSubmit() {
        this.form.isLoading = true

        this.form.submit('POST', '/caixa-diario/clientes/daily-payment')
          .then(response => {
            this.$toast.success('Pagamento registrado!')
            this.form.reset()
            this.$emit('created')
          })
          .catch(error => {})
          .then(() => {
            this.form.isLoading = false
          })
      },
      onClientSelect(client) {
        this.orders.isLoading = true

        axios.get(`/cliente/${client.id}/pedidos/list`)
          .then(response => {
            let orders = response.data.orders

            if (typeof orders === 'object') {
              orders = Object.values(response.data.orders)
            }

            this.form.order = ''
            this.orders.items = []

            this.orders.items = orders
            this.orders.isLoading = false
          })
      },
      asyncFindClients(search) {
        if (! search.length) {
          this.clients.items = []
          return
        }

        this.clients.isLoading = true

        axios.get('/clientes/list', {
          params: {
            name: search
          }
        })
          .then(response => {
            this.clients.items = response.data.clients
            this.clients.isLoading = false
          })
      },
      populateVias() {
        axios.get('/pagamentos/vias/list')
          .then(response => {
            this.vias = response.data.vias
          })
      }
    },
    mounted() {
      this.populateVias()
    }
  }
</script>