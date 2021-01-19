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
	export default {
		mixins: [shirtDefaultStateAfterClose],
		data() {
			return {
				shirtColor: '#1FBC9C',
				neckColor: '#3d556e'
			}
		},
		methods: {
			initUniformColors() {
				EventBus.$emit('SHIRT_COLOR_CHANGED', this.shirtColor);
				EventBus.$emit('NECK_COLOR_CHANGED', this.neckColor);
			}
		},
		watch: {
			shirtColor: function(value) {
				EventBus.$emit('SHIRT_COLOR_CHANGED', value);
			},
			neckColor: function(value) {
				EventBus.$emit('NECK_COLOR_CHANGED', value);
			}
		},
		mounted() {
			this.$nextTick(() => { this.initUniformColors() });
		}
	}
</script>