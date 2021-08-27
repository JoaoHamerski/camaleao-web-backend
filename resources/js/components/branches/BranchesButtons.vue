<script>
import NewBranchModal from './NewBranchModal'
import ShippingCompaniesModal from '../shipping-companies/ShippingCompaniesModal'

export default {
  components: {
    ShippingCompaniesModal,
    NewBranchModal
  }
}
</script>

<template>
  <div>
    <div class="d-flex justify-content-between">
      <button
        class="btn btn-success font-weight-bold"
        data-toggle="modal"
        data-target="#newBranchModal"
      >
        <i class="fas fa-plus fa-fa mr-1" />  Nova filial
      </button>

      <button
        class="btn btn-primary font-weight-bold"
        data-toggle="modal"
        data-target="#shippingCompaniesModal"
      >
        <i class="fas fa-truck" /> Transportadoras
      </button>
    </div>

    <NewBranchModal @refresh="$emit('refresh')" />
    <ShippingCompaniesModal @refresh="$emit('refresh')" />
  </div>
</template>
