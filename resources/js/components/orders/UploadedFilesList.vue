<script>
export default {
  props: {
    field: {
      type: String,
      default: ''
    },
    files: {
      type: Array,
      default: () => []
    },
    deleteFile: undefined
  }
}
</script>

<template>
  <div class="row mb-2">
    <template v-for="file in files">
      <div
        :key="file.key"
        class="col-3"
      >
        <img
          class="img-thumbnail img-fluid"
          :src="file.base64"
          alt=""
        >
        <div
          class="text-center mt-2"
          @click="deleteFile(file, field)"
        >
          <i class="fas fa-trash-alt fa-fw clickable text-danger" />
        </div>
      </div>
    </template>
  </div>
</template>
