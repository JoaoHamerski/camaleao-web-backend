<template>
	<div class="uniform">
		<!-- TORSO -->
		<div class="torso">
			<div ref="backBase" class="back-base">
				<slot name="back-base-svg"></slot>
			</div>
			<div ref="backMask" class="back-mask">
				<img src="images/uniform-simulator/back-mask.png">
			</div>
			<div ref="backNumber" class="number text-uppercase">{{ number }}</div>
			<div ref="backName" class="name text-uppercase">{{ name }} </div>
		</div>

		<div class="uniform-rotate-icon">
			<i class="custom-icon uniform-rotate"></i>
		</div>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				shirtColor: '',
				nameColor: '',
				numberColor: '',
				number: '10',
				name: 'JOGADOR',
			}
		},
		methods: {
			initListeners() {
				EventBus.$on('SHIRT_COLOR_CHANGED', (color) => { this.shirtColor = color });
				EventBus.$on('SHIRT_NAME_CHANGED', (name) => { this.name = name });
				EventBus.$on('SHIRT_NUMBER_CHANGED', (number) => { this.number = number });
				EventBus.$on('SHIRT_NAME_COLOR_CHANGED', (color) => { this.nameColor = color });
				EventBus.$on('SHIRT_NUMBER_COLOR_CHANGED', (color) => { this.numberColor = color });
			}
		},
		watch: {
			shirtColor: function(value) {
				this.$refs.backBase.querySelector('.back-base-svg').style.fill = value;
			},
			nameColor: function(value) {
				this.$refs.backName.style.color = value;
			},
			numberColor: function(value) {
				this.$refs.backNumber.style.color = value;
			}
		},
		mounted() {
			this.initListeners();
		}
	}
</script>