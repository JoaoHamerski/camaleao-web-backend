<template>
  <div>
    <div class="d-flex justify-content-between">
      <button class="btn btn-success font-weight-bold"
        data-toggle="modal"
        data-target="#newBranchModal"
      >
        <i class="fas fa-plus fa-fa mr-1"></i>  Nova filial
      </button>

      <button class="btn btn-primary font-weight-bold"
        data-toggle="modal"
        data-target="#shippingCompaniesModal"
      >
        <i class="fas fa-truck"></i> Transportadoras
      </button>
    </div>

    <NewBranchModal @refresh="$emit('refresh')" />
    <ShippingCompaniesModal @refresh="$emit('refresh')" />
  </div>
</template>

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