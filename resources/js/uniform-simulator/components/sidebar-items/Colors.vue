<template>
	<div>
		<h6 class="text-center font-weight-bold text-camaleao border-bottom border-camaleao border-2 pb-2">
			<i class="fas fa-tshirt fa-2x"></i>
		</h6>

		<div class="sidebar-dropdown-content">
			<div class="row">			
				<!-- CAMISA -->
				<div class="col d-flex flex-column align-items-center">
					<color v-model="shirtColor"></color>

					<div class="label text-secondary font-weight-bold">CAMISA</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				shirtColor: '#1FBC9C'
			}
		},
		methods: {
			initUniformColors() {
				this.$nextTick(() => {
					EventBus.$emit('SHIRT_COLOR_CHANGED', this.shirtColor);
				});
			}
		},
		watch: {
			shirtColor: function(value) {
				EventBus.$emit('SHIRT_COLOR_CHANGED', value);
			}
		},
		mounted() {
			this.initUniformColors();
		}
	}
</script>