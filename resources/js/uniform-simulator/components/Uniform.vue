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
			}
		},
		methods: {
			initListeners() {
				EventBus.$on('SHIRT_COLOR_CHANGED', (color) => { this.shirtColor = color });
			}
		},
		watch: {
			shirtColor: function(value) {
				this.$refs.backBase.querySelector('.back-base-svg').style.fill = value;
			}
		},
		mounted() {
			this.initListeners();
		}
	}
</script>