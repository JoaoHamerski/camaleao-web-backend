<template>
  <div class="uniform no-select">

    <!-- FRENTE -->
    <transition enter-active-class="animate__animated animate__flipInY animate__faster">
      <div v-show="isFront" class="uniform-front ">
        <div ref="frontBase" class="front-base">
          <slot name="front-base-svg"></slot>
        </div>

        <div ref="frontMask" class="front-mask">
          <img src="images/uniform-simulator/front-mask.png">
        </div>

        <div ref="frontNeckBase" class="front-neck-base">
          <slot name="front-neck-base-svg"></slot>
        </div>

        <div class="uniform-mask" :class="{ 'mask-front' : ! hasActiveAttach }">
          <div v-show="! hideBrand">
            <transition enter-active-class="animate__animated animate__fadeIn animate__faster">
              <div ref="frontNumber" v-show="replaceBrandByNumber" class="front-number" :class="numberFont.class">
                {{ number.length === 0 ? '10' : number }}
              </div>
            </transition>

            <transition enter-active-class="animate__animated animate__fadeIn animate__faster">
              <div v-show="! replaceBrandByNumber" ref="brand" :class="{ 'front-brand' : ! brand }">
                <transition enter-active-class="animate__animated animate__fadeIn animate__faster">
                  <vue-drr key="brand" v-if="brand"
                    :attach-id="brand.id"
                    :x="105"
                    :y="95"
                    :w="70"
                    :h="(brand.height / brand.width) * 70">
                      <img class="img-fluid" :src="brand.image" alt="">
                  </vue-drr>
                  <div key="brand-svg" v-else>
                    <slot name="brand-svg"></slot>
                  </div>
                </transition>
              </div>
            </transition>
          </div>

          <div v-show="! hideShield" :class="{'front-shield': ! shield}" ref="shield">
            <transition enter-active-class="animate__animated animate__fadeIn animate__faster">
              <vue-drr key="shield" v-if="shield"
                :attach-id="shield.id"
                :x="200"
                :y="95"
                :w="36"
                :h="(shield.height / shield.width) * 36">
                  <img class="img-fluid" :src="shield.image" alt="Escudo da camisa">
              </vue-drr>

              <div key="shield-svg" v-else>
                <slot name="shield-svg"></slot>
              </div>
            </transition>
          </div>

          <transition-group 
            enter-active-class="animate__animated animate__zoomIn animate__faster"
            leave-active-class="animate__animated animate__zoomOut animate__faster">
            <template v-for="attach in frontAttachs">
              <vue-drr
                :attach-id="attach.id"
                :x="155"
                :y="170"
                :w="100"
                :h="(attach.height / attach.width) * 100"
                :key="attach.id">
                <img class="img-fluid" :src="attach.image" alt="Anexo da frente da camisa">
              </vue-drr>
            </template>
          </transition-group>
        </div>
      </div>
    </transition>

    <!-- COSTAS -->
    <transition enter-active-class="animate__animated animate__flipInY animate__faster">
      <div v-show="! isFront" class="uniform-back">
        <div ref="backBase" class="back-base">
          <slot name="back-base-svg"></slot>
        </div>

        <div ref="backMask" class="back-mask">
          <img src="images/uniform-simulator/back-mask.png">
        </div>

        <div ref="backNeckBase" class="back-neck-base">
          <slot name="back-neck-base-svg"></slot>
        </div>

        <div class="uniform-mask" :class="{'mask-back' : ! hasActiveAttach}">
          <div ref="backNumber" class="back-number" :class="[numberFont.class]">
            {{ number.length == 0 ? '10' : number }}
          </div>

          <div ref="backName" class="back-name" :class="nameFont.class"> 
            {{ name.length === 0 ? 'JOGADOR' : name }}
          </div>

          <transition-group
            enter-active-class="animate__animated animate__zoomIn animate__faster"
            leave-active-class="animate__animated animate__zoomOut animate__faster">
            <template v-for="attach in backAttachs">
              <vue-drr :key="attach.id"
                :attach-id="attach.id"
                :x="155"
                :y="170"
                :w="100"
                :h="(attach.height / attach.width) * 100">
                <img class="img-fluid" :src="attach.image" alt="Anexo de trás da camisa">
              </vue-drr>
            </template>
          </transition-group>
        </div>
      </div>

    </transition>

    <div @click="isFrontState = ! isFrontState" class="uniform-rotate-icon">
      <i class="custom-icon uniform-rotate"></i>
    </div>

  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex';

  export default {
    computed: {...mapState([
      'shirtColor', 
      'neckColor', 
      'nameColor', 
      'numberColor', 
      'nameFont', 
      'numberFont', 
      'number', 
      'name', 
      'hideBrand', 
      'hideShield', 
      'replaceBrandByNumber', 
      'attachs'
    ]),
    ...mapGetters(['brand', 'shield', 'frontAttachs', 'backAttachs', 'hasActiveAttach']),
    isFront: {
      get() { return this.$store.state.isFront },
      set(value) { this.$store.commit('update', { isFront: value })}
    },
    isFrontState: {
      get() { return this.$store.state.isFrontState },
      set(value) { this.$store.commit('update', { isFrontState: value })}
    }
  },
    methods: {
      changeShirtColor(color) {
        this.$refs.backBase.querySelector('.back-base-svg').style.fill = color;
        this.$refs.frontBase.querySelector('.front-base-svg').style.fill = color;
        this.$refs.frontNeckBase.querySelector('.front-neck-base-intern-svg').style.fill = color;
      },
      changeNeckColor(color) {
        this.$refs.backNeckBase.querySelector('.back-neck-base-svg').style.fill = color;
        this.$refs.frontNeckBase.querySelector('.front-neck-base-extern-svg').style.fill = color;
      },
      changeNameColor(color) {
        this.$refs.backName.style.color = color;
      },
      changeNumberColor(color) {
        this.$refs.backNumber.style.color = color;
        this.$refs.frontNumber.style.color = color;
      },
      initShirtColors() {
        this.changeShirtColor(this.shirtColor);
        this.changeNeckColor(this.neckColor);
        this.changeNameColor(this.nameColor);
        this.changeNumberColor(this.numberColor);
      }
    },
    watch: {
      shirtColor: function(value) {
        this.changeShirtColor(value);
      },
      neckColor: function(value) {
        this.changeNeckColor(value);
      },
      nameColor: function(value) {
        this.changeNameColor(value);
      },
      numberColor: function(value) {
        this.changeNumberColor(value);
      },
      isFrontState: function(value) {
        this.isFront = value;
      }
    },
    mounted() {
      this.initShirtColors();
    }
  }
</script>