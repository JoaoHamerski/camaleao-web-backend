<template>
  <AppModal id="commissionImageModal"
    modalDialogClass="modal-dialog-centered"
  >
    <template #body>
      <div v-for="(image, index) in images" :key="image">
        <h5 class="font-weight-bold text-secondary text-center">Imagem {{index + 1}}</h5>
        <img class="img-fluid" 
          :src="`/storage/imagens_da_arte/${image}`"
        >
      </div>
    </template>
  </AppModal>
</template>

<script>
  export default {
    props: {
      images: { default: () => [] }
    }
  }
</script>