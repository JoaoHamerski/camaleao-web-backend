<template>
  <div>
    <h6 class="sidebar-dropdown-title">
      UNIFORME
    </h6>

    <div class="sidebar-dropdown-content">
      <div class="row">     
        <div class="col d-flex flex-column align-items-center">
          <color v-model="shirtColor"></color>

          <div class="sidebar-content-label text-secondary font-weight-bold">CAMISA</div>
        </div>

        <div class="col d-flex flex-column align-items-center">
          <color v-model="neckColor"></color>

          <div class="sidebar-content-label text-secondary font-weight-bold">GOLA</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex';
  import { shirtDefaultStateAfterClose } from '../../mixins';

  export default {
    mixins: [shirtDefaultStateAfterClose],
    computed: {
      shirtColor: {
        get() { return this.$store.state.shirtColor },
        set(value) { this.$store.commit('update', { shirtColor: value }) } 
      },
      neckColor: {
        get() { return this.$store.state.neckColor },
        set(value) { this.$store.commit('update', { neckColor: value }) }
      }
    }
  }
</script>