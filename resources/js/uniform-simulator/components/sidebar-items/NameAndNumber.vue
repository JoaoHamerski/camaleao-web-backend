<template>
	<div>
		<div>
			<h6 class="text-center font-weight-bold text-camaleao border-bottom border-camaleao border-2 pb-2">
				NOME
			</h6>

			<div class="sidebar-dropdown-content">
				<div class="no-gutters">
					<div class="col">
						<input type="text" v-model="name"
							class="form-control md-form-control form-control-sm w-100 text-center text-uppercase" 
							placeholder="Nome na camisa...">	
					</div>
				</div>

				<div class="mt-3 d-flex flex-row align-items-center justify-content-around">
					<div class="d-flex flex-column align-items-center">
						<color v-model="nameColor"></color> 
						<div class="label font-weight-bold text-secondary">COR</div>
					</div>
					<div class="d-flex flex-column align-items-center">
						FONTE
					</div>
				</div>
			</div>
		</div>

		<div class="mt-4">
			<h6 class="text-center font-weight-bold text-camaleao border-bottom border-camaleao border-2 pb-2">
				NÚMERO
			</h6>

			<div class="sidebar-dropdown-content">
				<div class="no-gutters">
					<div class="col">
						<input type="text" v-model="number"
							class="form-control md-form-control form-control-sm w-100 text-center text-uppercase" 
							placeholder="Número na camisa...">	
					</div>
				</div>

				<div class="mt-3 d-flex flex-row align-items-center justify-content-around">
					<div class="d-flex flex-column align-items-center">
						<color v-model="numberColor"></color> 
						<div class="label font-weight-bold text-secondary">COR</div>
					</div>
					<div class="d-flex flex-column align-items-center">
						FONTE
					</div>
				</div>
			</div>
		</div>
		</div>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				name: '',
				number: '',
				nameColor: '#FFFFFF',
				numberColor: '#FFFFFF'
			}
		},
		watch: {
			name: function(value) {
				EventBus.$emit('SHIRT_NAME_CHANGED', value.length === 0 ? 'JOGADOR' : value);
			},
			number: function(value) {
				EventBus.$emit('SHIRT_NUMBER_CHANGED', value.length === 0 ? '10' : value);
			},
			nameColor: function(value) {
				EventBus.$emit('SHIRT_NAME_COLOR_CHANGED', value);
			},
			numberColor: function(value) {
				EventBus.$emit('SHIRT_NUMBER_COLOR_CHANGED', value);
			}
		}
	}
</script>