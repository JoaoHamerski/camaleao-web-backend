<template>
  <div class="uniform">
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

        <div class="uniform-wrapper-attachs uniform-mask-front">
          <div v-show="! hideBrand" ref="brand" :class="{'front-brand': brand == null}">
            <slot v-if="brand == null" name="brand-svg"></slot>
            <div v-else>
              <vue-drag-resize class="uniform-attach"  
                :aspect-ratio="true"
                h="100%"
                :w="70"
                :minw="20"
                :minh="20"
                :x="73"
                :y="85"
                :sticks="['tl', 'tr', 'bl', 'br']">
                  <img class="w-100" :src="brand.image" alt="">
              </vue-drag-resize>
            </div>
          </div>

          <div v-show="! hideShield">
            <transition enter-active-class="animate__animated animate__bounceIn animate__faster">
              <div ref="frontNumber" v-show="numberInFront" class="front-number" :class="numberFont.class">
                {{ number }}
              </div>
            </transition>

            <transition enter-active-class="animate__animated animate__bounceIn animate__faster">
              <div v-show="! numberInFront" ref="shield" class="front-shield">
                <slot name="shield-svg"></slot>
              </div>
            </transition>
          </div>

          <div class="uniform-wrapper-attachs uniform-mask-front">
            <vue-drag-resize class="uniform-attach"  
              :aspect-ratio="true"
              :minw="20"
              :minh="20"
              :x="100"
              :y="130"
              :sticks="['tl', 'tr', 'bl', 'br']">
                <img class="w-100 h-100" src="" alt="">
            </vue-drag-resize>
          </div>
        </div>
      </div>

    </transition>

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

        <div ref="backNumber" class="back-number" :class="[numberFont.class]">
          {{ number }}
        </div>

        <div ref="backName" class="back-name" :class="[nameFont.class]"> 
          {{ name }}
        </div>

        <div class="uniform-wrapper-attachs uniform-mask-back">
          
        </div>
      </div>

    </transition>

    <div @click="isFrontState = ! isFrontState" class="uniform-rotate-icon">
      <i class="custom-icon uniform-rotate"></i>
    </div>

  </div>
</template>

<script>
  /**
  * front
  *   Especifica o estado do uniforme programaticamente, se for true, inicializa
  *   exibindo a frente, senão a trás.
  *
  * isFront
  *   Determina o estado que o uniforme está sendo exibido, se é a frente ou não.
  *
  * isFrontState
  *   Indica o estado que o usuário deixou o uniforme
  *   manualmente, ignorando mudanças feitas dinamicamente, essa variável 
  *   é utilizada para retornar o estado padrão do uniforme quando necessário.
  **/
  export default {
    props: {
      front: {
        type: Boolean, default: true
      }
    },
    data() {
      return {
        isFront: this.front,
        isFrontState: this.front,
        shirtColor: '',
        neckColor: '',
        nameColor: '',
        numberColor: '',
        nameFont: '',
        numberFont: '',
        number: '10',
        name: 'JOGADOR',
        hideBrand: false,
        hideShield: false,
        numberInFront: false,
        attachs: [],
        brand: null,
        shield: null
      }
    },
    methods: {
      initListeners() {
        EventBus.$on('SHIRT_COLOR_CHANGED', (color) => { this.shirtColor = color });
        EventBus.$on('SHIRT_NAME_CHANGED', (name) => { this.name = name });
        EventBus.$on('SHIRT_NUMBER_CHANGED', (number) => { this.number = number });
        EventBus.$on('SHIRT_NAME_COLOR_CHANGED', (color) => { this.nameColor = color });
        EventBus.$on('SHIRT_NUMBER_COLOR_CHANGED', (color) => { this.numberColor = color });
        EventBus.$on('NECK_COLOR_CHANGED', (color) => { this.neckColor = color });
        EventBus.$on('SHIRT_FONT_NAME_CHANGED', (font) => { this.nameFont = font });
        EventBus.$on('SHIRT_FONT_NUMBER_CHANGED', (font) => { this.numberFont = font });

        EventBus.$on('SHIRT_BACK_SHOW', () => { this.isFront = false });
        EventBus.$on('SHIRT_FRONT_SHOW', () => { this.isFront = true });
        EventBus.$on('SHIRT_DEFAULT_STATE', () => { this.isFront = this.isFrontState });

        EventBus.$on('SHIRT_HIDE_BRAND', (value) => { this.hideBrand = value });
        EventBus.$on('SHIRT_HIDE_SHIELD', (value) => { this.hideShield = value });
        EventBus.$on('SHIRT_SWITCH_SHIELD', (value) => { this.numberInFront = value });

        EventBus.$on('ATTACH_UPLOADED', (attach) => { 
          if (attach.class == 'brand') {
            this.brand = attach
            return;
          }

          if (attach.class == 'shield') {
            this.shield = attach;
            return;
          }

          this.attachs.push(attach);
        });
      }
    },
    watch: {
      shirtColor: function(value) {
        this.$refs.backBase.querySelector('.back-base-svg').style.fill = value;
        this.$refs.frontBase.querySelector('.front-base-svg').style.fill = value;
        this.$refs.frontNeckBase.querySelector('.front-neck-base-intern-svg').style.fill = value;
      },
      neckColor: function(value) {
        this.$refs.frontNeckBase.querySelector('.front-neck-base-extern-svg').style.fill = value;
        this.$refs.backNeckBase.querySelector('.back-neck-base-svg').style.fill = value;
      },
      nameColor: function(value) {
        this.$refs.backName.style.color = value;
      },
      numberColor: function(value) {
        this.$refs.backNumber.style.color = value;
        this.$refs.frontNumber.style.color = value;
      },
      isFrontState: function(value) {
        this.isFront = value;
      }
    },
    mounted() {
      this.initListeners();
    }
  }
</script>