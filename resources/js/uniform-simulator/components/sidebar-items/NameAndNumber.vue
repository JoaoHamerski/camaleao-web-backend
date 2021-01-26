<template>
  <div>
    <div>
      <h6 class="sidebar-dropdown-title">
        NOME
      </h6>

      <div class="sidebar-dropdown-content">
        <div class="d-flex justify-content-between align-items-center no-gutters">
          <div class="col-md-9">
            <masked-input 
              class="form-control form-control-sm w-100 text-center"
              :mask="(value) => {
                let regex = /[a-zA-Z\u00C0-\u024F\u1E00-\u1EFF0-9_ .]/,
                    pattern = [];

                for (let i = 0; i < 12; i++) { pattern.push(regex) }

                return pattern;
              }"
              :pipe="(value) => { return value.toUpperCase() }"
              v-model="name"
              :guide="false"
              placeholder="Nome na camisa"></masked-input>
          </div>
          <div class="col-md-3 text-center">
            <color v-model="nameColor"></color> 
          </div>
        </div>

        <div class="mt-3">
          <font-selector 
            ref="nameFontSelector" 
            @font-changed="(...args) => { 
              this.nameFontChanged(args[0]);

              if (this.isFontsLocked) {
                this.$refs.numberFontSelector
                    .$refs.vueper
                    .goToSlide(args[1], {emit: false}); 
                this.numberFontChanged(args[0]);
              }
            }">ABC</font-selector>
        </div>
      </div>
    </div>

    <div class="mt-4">
      <h6 class="sidebar-dropdown-title">
        NÚMERO
      </h6>

      <div class="sidebar-dropdown-content">
        <div class="d-flex justify-content-between align-items-center no-gutters">
          <div class="col-md-9">
            <masked-input 
              class="form-control form-control-sm w-100 text-center"
              :mask="[/\d/, /\d/]"
              v-model="number"
              :guide="false"
              placeholder="Número na camisa"></masked-input>
          </div>
          <div class="col-md-3 text-center">
            <color v-model="numberColor"></color> 
          </div>
        </div>

        <div class="mt-3">
          <font-selector
            ref="numberFontSelector" 
            @font-changed="(...args) => {
              this.numberFontChanged(args[0]);

              if (this.isFontsLocked) {
                this.$refs.nameFontSelector
                    .$refs.vueper
                    .goToSlide(args[1], {emit: false}); 
                this.nameFontChanged(args[0]);
              }
            }">123</font-selector>
        </div>
      </div>
    </div>

    <div class="mt-4">
      <h6 class="sidebar-dropdown-title text-primary border-primary"></h6>
      <div>
        <button @click="toggleLockFonts()" 
          class="btn btn-block" 
          :class="[isFontsLocked ? 'btn-primary' : 'btn-outline-primary']">

          <div v-if="isFontsLocked">
            <i class="fas fa-lock fa-fw"></i>
            Desbloquear fontes
          </div>
          <div v-else>
            <i class="fas fa-unlock fa-fw"></i>
            Bloquear fontes
          </div>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
  import { shirtDefaultStateAfterClose } from '../../mixins';
  
  export default {
    mixins: [shirtDefaultStateAfterClose],
    data() {
      return {
        isFontsLocked: false,
      }
    },
    computed: {
      name: {
        get() { return this.$store.state.name },
        set(value) { this.$store.commit('update', { name: value }) }
      },
      number: {
        get() { return this.$store.state.number },
        set(value) { this.$store.commit('update', { number: value }) }
      },
      nameColor: {
        get() { return this.$store.state.nameColor },
        set(value) { this.$store.commit('update', { nameColor: value }) }
      },
      numberColor: {
        get() { return this.$store.stateNumberColor },
        set(value) { this.$store.commit('update', { numberColor: value }) }
      }
    },
    methods: {
      toggleLockFonts() {
        let currentSlide = this.$refs.nameFontSelector.$refs.vueper.slides.current;

        this.$refs.numberFontSelector.$refs.vueper.goToSlide(currentSlide);

        this.isFontsLocked = ! this.isFontsLocked;
      },
      nameFontChanged(font) {
        this.$store.commit('update', { nameFont: font });
      },
      numberFontChanged(font) {
        this.$store.commit('update', { numberFont: font });
      },
      initFonts() {
        let font = this.$refs.nameFontSelector.fonts[0];
        this.nameFontChanged(font);
        this.numberFontChanged(font);
      },
    },
    mounted() {
      $(this.$parent.$el).on(
        'shown.bs.dropdown', 
        () => { this.$store.commit('update', { isFront: false }) }
      );

      this.initFonts();
    }
  }
</script>