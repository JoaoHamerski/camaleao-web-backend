<template>
  <AppModal
    id="viewFileModal"
    modal-header-class="bg-primary"
    modal-dialog-class="modal-dialog-centered modal-dialog-scrollable"
  >
    <template #header>
      <h5 class="font-weight-bold text-white mb-0">
        Comprovante
      </h5>
    </template>

    <template #body>
      <div v-if="fileObject">
        <div
          v-if="fileObject.type === 'application/pdf'"
          class="embed-responsive embed-responsive-16by9 mb-3"
        >
          <iframe
            class="embed-responsive-item"
            :src="file.base64"
            allowfullscreen
          />
        </div>
        <div
          v-else
          class="text-center"
        >
          <img
            class="img-fluid"
            :src="file.base64"
          >
        </div>
      </div>
    </template>
  </AppModal>
</template>

<script>
export default {
    props: {
        file: undefined,
    },
    data: () => {
        return {
            fileObject: null
        }
    },
    watch: {
        file() {
            this.fileObject = null

            if (this.file) {
                this.fileObject = this.$helpers.dataURLtoFile(
                    this.file.base64, 'file'
                )
            }
        }
    }
}
</script>
