<template>
  <div class="row mb-2">
    <template v-for="file in files">
      <div class="col-3" :key="file.key">
        <img class="img-thumbnail img-fluid" :src="file.base64" alt="">
        <div class="text-center mt-2" @click="deleteFile(file, field)">
          <i class="fas fa-trash-alt fa-fw clickable text-danger"></i>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
  export default {
    props: {
      field: { default: ''},
      files: { default: [] },
      deleteFile: { default: null }
    }
  }
</script>