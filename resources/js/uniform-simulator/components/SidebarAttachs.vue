<template>
  <div class="sidebar-attachs no-select" :class="{ 'active' : hasAttach }">
    <transition enter-active-class="animate__animated animate__fadeIn animate__fast">
      <div key="frontImages" v-if="isFront">
        <div class="sidebar-attachs-title mb-2">
          IMAGENS DA FRENTE
        </div>

        <div class="sidebar-attachs-content">
          <ul class="sidebar-attachs-items list-group list-group-flush">
            <sidebar-attach-item v-if="brand" :attach="brand">
              <template #label>
                <div class="attach-item-label">MARCA</div>
              </template>

              <template #attach-name>
                <div class="attach-item-name">{{ brand.name }}</div>
              </template>
            </sidebar-attach-item>

            <sidebar-attach-item v-if="shield" :attach="shield">
              <template #label>
                <div class="attach-item-label">ESCUDO</div>
              </template>

              <template #attach-name>
                <div class="attach-item-name">{{ shield.name }}</div>
              </template>
            </sidebar-attach-item>

            <template v-for="(attach, index) in frontAttachs">
              <sidebar-attach-item :attach="attach">
                <template #label>
                 <div class="attach-item-label">IMAGEM</div> 
                </template>

                <template #attach-name>
                  <div class="attach-item-name">{{ attach.name }}</div>
                </template>
              </sidebar-attach-item>
            </template>
          </ul>
        </div>
      </div>

      <div key="backImages" v-else>
        <div class="sidebar-attachs-title mb-2">
          IMAGENS DAS COSTAS
        </div>
        <div class="sidebar-attachs-content">
          <ul class="sidebar-attachs-items list-group list-group-flush">
            <template v-for="attach in backAttachs">
              <sidebar-attach-item :attach="attach">
                <template #label> {{ attach.name }} </template>
              </sidebar-attach-item>
            </template>
          </ul>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex';

  export default {
    computed: {
      ...mapGetters(['hasAttach', 'shield', 'brand', 'frontAttachs', 'backAttachs']),
      isFront() {
        return this.$store.state.isFront;
      }
    }
  }
</script>