<template>
  <form @submit.prevent="onSubmit" data-type="vue"
    @focus.capture="form.errors.clear($event.target.name)"
  >
    <h5 class="font-weight-bold text-secondary">Informações básicas</h5>

    <div class="form-row d-flex flex-column flex-md-row">
      <div class="form-group col">
        <AppInput v-model="form.name"
          id="name" 
          name="name" 
          placeholder="Nome que descreva o pedido"
          :optional="true"
          :error="form.errors.get('name')"
        >
          Nome do pedido
        </AppInput>
      </div>

      <div class="form-group col">
        <AppInput v-model="form.code"
          id="code" 
          name="code"
          :error="form.errors.get('code')"
        >
          Código
        </AppInput>
      </div>
    </div>

    <hr>

    <h5 class="font-weight-bold text-secondary">Valores</h5>
    <h6 class="font-weight-bold text-secondary">Tipos de roupa</h6>

    <div class="table-responsive">
      <div :style="{minWidth: '550px'}">
        <div class="d-flex mb-3">
          <div class="col-3"></div>
          <div class="col-3 text-center text-uppercase font-weight-bold text-secondary">Quantidade</div>
          <div class="col-3 text-center text-uppercase font-weight-bold text-secondary">Valor unit.</div>
          <div class="col-3 text-center text-uppercase font-weight-bold text-secondary">Total</div>
        </div>

        <template v-for="type in clothingTypes">
          <div class="form-group row mx-0" :key="type.id">
            <div class="font-weight-bold col-3 pl-0"
              :class="{'text-primary': form['quantity_' + type.key].length && form['value_' + type.key]}"
              :style="{transition: 'color .15s'}"
            >
              {{ type.name }}
            </div>

            <AppInput v-model="form['quantity_' + type.key]"
              :mask="masks.numericInt({integerLimit: 4})"
              class="col-3"
              :id="'quantity_' + type.key"
              :name="'quantity_' + type.key"
              @focus.native.capture="form.errors.clear('price')"
            />

            <AppInput v-model="form['value_' + type.key]"
              :mask="masks.valueBRL"
              placeholder="R$"
              class="col-3"
              :id="'value_' + type.key"
              :name="'value_' + type.key"
              @focus.native.capture="form.errors.clear('price')"
            />

            <AppInput class="col-3 pr-0"
              :value="$helpers.valueToBRL(
                evaluateTotal(
                  form['quantity_' + type.key], 
                  form['value_' + type.key]
                )
              )"
              :disabled="true"
              :id="'total_value_' + type.key"
            />
          </div>
        </template>

        <div class="d-flex">
          <div class="col-3 pl-0 font-weight-bold">TOTAL</div>
          <AppInput :value="totalQuantity.toString()"
            inputClass="font-weight-bold"
            class="col-3"
            id="totalQuantity"
            name="totalQuantity"
            :disabled="true"
          />
          <div class="col-3"><hr></div>
          <AppInput :value="$helpers.valueToBRL(totalClothingsValue)"
            inputClass="font-weight-bold"
            class="col-3 pr-0"
            id="totalClothingsValue"
            name="totalClothingsValue"
            :disabled="true"
          />
        </div>

        <div class="small text-secondary mt-4">
          A quantidade total só é calculada se o valor também for informado.
        </div>
        

        <div v-if="form.errors.has('price')" 
          class="small text-danger text-center my-3"
        >
        <span v-if="totalValue < 0">
          O valor final deve ser maior que R$ 0,00
        </span>
        <span v-else>
          É necessário informar o valor de pelo menos um tipo acima para gerar o preço final do pedido
        </span>
        </div>

        <div class="small mt-2 text-right">
          <span class="clickable" @click="clearAllClothingTypes">Limpar todos valores</span>
        </div>
      </div>
    </div>

    <div class="form-row d-flex">
      <div class="form-group col">
        <AppInput :value="$helpers.valueToBRL(totalValue)"
          id="totalValue"
          name="totalValue"
          :disabled="true"
          disabledMessage="O preço final é calculado automaticamente, informe pelo menos um preço parcial."
        >
          Preço final
        </AppInput>
      </div>
      <div class="form-group col">
        <AppInput v-model="form.discount"
          id="discount"
          name="discount"
          :mask="masks.valueBRL"
          placeholder="R$"
          :optional="true"
          :error="form.errors.get('discount')"
          @focus.native.capture="form.errors.clear('price')"
        >
          Desconto
        </AppInput>
      </div>
    </div>

    <div v-if="! isEdit" class="form-row d-flex flex-column flex-md-row">
      <div class="form-group col">
        <AppInput v-model="form.down_payment"
          id="down_payment"
          name="down_payment"
          placeholder="R$"
          :mask="masks.valueBRL"
          :optional="true"
          :error="form.errors.get('down_payment')"
        >Entrada</AppInput>
      </div>
      
      <div class="form-group col">
        <label for="payment_via_id" 
          class="font-weight-bold"
        >
          Via da entrada
        </label>
        <select class="custom-select" v-model="form.payment_via_id"
          :disabled="! form.down_payment.length"
          :class="{'is-invalid': form.errors.has('payment_via_id')}"
          name="payment_via_id" 
          id="payment_via_id"
        >
          <option value="">Selecione a via</option>
          <option v-for="via in paymentVias" :value="via.id" :key="via.id">
            {{ via.name }}
          </option>
        </select>
        
        <small v-if="form.errors.has('payment_via_id')" class="text-danger">
          {{ form.errors.get('payment_via_id') }}
        </small>
      </div>
    </div>

    <hr>

    <h5 class="font-weight-bold text-secondary">Produção e entrega</h5>

    <div class="form-row d-flex flex-column flex-md-row">
      <div class="form-group col">
        <AppInput v-model="form.production_date"
          id="production_date"
          name="production_date"
          placeholder="dd/mm/aaaa"
          :mask="masks.date"
          :optional="true"
          :error="form.errors.get('production_date')"
        >Data de produção</AppInput>
      </div>

      <div class="form-group col">
        <AppInput v-model="form.delivery_date"
          id="delivery_date"
          name="delivery_date"
          placeholder="dd/mm/aaaa"
          :mask="masks.date"
          :optional="true"
          :error="form.errors.get('delivery_date')"
        >Data de entrega</AppInput>
      </div>
    </div>

    <hr>

    <h5 class="font-weight-bold text-secondary">Anexos</h5>

    <div class="form-group col px-0">
      <AppInput @change="appendFileToForm($event, 'art_paths')"
        id="art_paths" 
        name="art_paths" 
        type="file"
        accept="image/*"
        :multiple="true"
        :optional="true"
      >
        Imagens da arte
      </AppInput>
    </div>
    
    <UploadedFilesList 
      :files="form.art_paths" 
      :deleteFile="deleteFile"
      field="art_paths"
    />

    <div class="form-group col px-0">
      <AppInput @change="appendFileToForm($event, 'size_paths')"
        id="size_paths" 
        name="size_paths" 
        type="file"
        accept="image/*"
        :multiple="true"
        :optional="true"
      >
        Imagens do tamanho
      </AppInput>
    </div>

    <UploadedFilesList 
      :files="form.size_paths" 
      :deleteFile="deleteFile"
      field="size_paths"
    />

    <div class="form-group col px-0">
      <AppInput @change="appendFileToForm($event, 'payment_voucher_paths')"
        id="payment_voucher_paths" 
        name="payment_voucher_paths" 
        type="file"
        accept="image/*,.pdf"
        :multiple="true"
        :optional="true"
      >
        Comprovantes de pagamento
      </AppInput>
    </div>

    <ul class="list-group">
      <li class="list-group-item d-flex justify-content-between"
        v-for="(file, index) in form.payment_voucher_paths"
        :key="file.key"
      >
        <div class="text-primary">
          <i class="fas fa-file fa-fw mr-1"></i>
          
          Comprovante {{ index + 1}}
        </div>

        <div class="text-danger clickable" @click="deleteFile(file, 'payment_voucher_paths')">
          <i class="fas fa-trash-alt fa-fw"></i>
        </div>
      </li>
    </ul>

    <button type="submit" 
      class="font-weight-bold btn btn-success mt-3"
      :disabled="form.isLoading"
    >
      <span class="spinner-border spinner-border-sm mr-1" v-if="form.isLoading"></span>
      {{ isEdit ? 'Salvar' : 'Cadastrar' }}
    </button>
  </form>
</template>

<script>
  import masks from '../../util/masks'
  import Form from '../../util/Form'
  import accounting from 'accounting-js'

  import UploadedFilesList from './UploadedFilesList'
  
  export default {
    components: {
      UploadedFilesList
    },
    props: {
      isEdit: { default: false },
      orderCode: { default: '' },
      clientId: { default: '' }
    },
    data: function() {
      return {
        masks,
        clothingTypes: [],
        paymentVias: [],
        form: new Form({
          name: '',
          code: '',
          discount: '',
          down_payment: '',
          payment_via_id: '',
          production_date: '',
          delivery_date: '',
          art_paths: [],
          size_paths: [],
          payment_voucher_paths: []
        })
      }
    },
    computed: {
      totalQuantity() {
        let total  = 0

        for (let type of this.clothingTypes) {
          if (this.form[`value_${type.key}`].length) {
            total += +this.form[`quantity_${type.key}`]
          }
        }

        return total
      },
      totalValue() {
        return this.totalClothingsValue - accounting.unformat(this.form.discount, ',')
      },
      totalClothingsValue() {
        let total = 0

        for (let type of this.clothingTypes) {
          total += accounting.unformat(this.evaluateTotal(
            this.form[`quantity_${type.key}`],
            this.form[`value_${type.key}`]
          ), ',')
        }
        
        return total
      },
    },
    methods: {
      deleteFile(file, field) {
        let index = this.form[field].findIndex(_file => _file.key === file.key)

        this.form[field].splice(index, 1);
      },
      async appendFileToForm(event, field) {
        const toBase64 = file => new Promise((resolve, reject) => {
          const reader = new FileReader()
          reader.readAsDataURL(file)

          reader.onload = () => resolve(reader.result)
          reader.onerror = error => reject(error)
        })

        for (let file of event.target.files) {
          let base64 = await toBase64(file)

          this.form[field].push({
            key: (+ new Date()).toString(),
            base64
          })
        }
      },
      create() {
        this.form.isLoading = true

        this.form.submit('POST', window.location.href)
          .then(response => {
            window.location.href = response.redirect
          })
          .catch(error => {
            this.$toast.error('Verifique os campos incorretos')
            console.log(error)
          })
          .then(() => {
            this.form.isLoading = false
          })
      },
      update() {
        this.form.isLoading = true

        this.form.submit('PATCH', window.location.href)
          .then(response => {
            window.location.href = response.redirect
          })
          .catch(error => {
            this.$toast.error('Verifique os campos incorretos')
          })
          .then(() => {
            this.form.isLoading = false
          })
      },
      onSubmit() {
        if (this.isEdit) {
          this.update()
        } else {
          this.create()
        }
      },
      clearAllClothingTypes() {
        for (let type of this.clothingTypes) {
          this.form[`quantity_${type.key}`] = ''
          this.form[`value_${type.key}`] = ''
          this.form.quantity = '1'
        }
      },  
      evaluateTotal(quantity, value) {
        let sanitizedValue = accounting.unformat(value, ','),
          result = (quantity * sanitizedValue)

        return result
      },
      populateClothingTypes() {
        axios.get('/tipos-de-roupas/list', {
          params: {
            hidden: false
          }
        })
          .then(response => {
            this.clothingTypes.push(...response.data.clothing_types)

            for (let type of this.clothingTypes) {
              this.$set(this.form.originalData, `quantity_${type.key}`, '')
              this.$set(this.form.originalData, `value_${type.key}`, '')
              this.$set(this.form, `quantity_${type.key}`, '')
              this.$set(this.form, `value_${type.key}`, '')
            }
          })
      },
      populateVias() {
        axios.get('/pagamentos/vias/list')
          .then(response => {
            this.paymentVias.push(...response.data.vias)
          })
      },
      populateForm() {
        axios.get(`/cliente/${this.clientId}/pedido/${this.orderCode}/json`)
          .then(response => {
            let order = response.data.order,
                paths = ['art_paths', 'size_paths', 'payment_voucher_paths']

            this.form.name = order.name
            this.form.code = order.code
            this.form.production_date = order.production_date
            this.form.delivery_date= order.delivery_date
            this.form.discount = order.discount == 0 
              ? ''
              : this.$helpers.valueToBRL(order.discount)

            for (let path of paths) {
              if (order[path].length) {
                let files = order[path].map(_path => {
                  return {key: + new Date(), base64: _path}
                })

                this.form[path].push(...files)
              }
            }

            for (let type of this.clothingTypes) {
              if (order[`value_${type.key}`]) {
                this.form[`value_${type.key}`] = this.$helpers.valueToBRL(
                  order[`value_${type.key}`]
                )
              }

              if (order[`quantity_${type.key}`]) {
                this.form[`quantity_${type.key}`] = `${order[`quantity_${type.key}`]}`
              }
            }
          })
      }
    },
    mounted() {
      this.populateClothingTypes()
      this.populateVias()

      if (this.isEdit) {
        this.populateForm()
      }
    }
  }
</script>