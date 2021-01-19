<template>
	<div class="form__field">
		<div class="form__input">
			<v-swatches
				:value="value"
				@input="$emit('input', $event)" 
				:close-on-select="false"
				popover-x="left" 
				popover-y="bottom" 
				shapes="circles"
				swatch-size="30"
				:trigger-style="{ width: '28px', height: '28px', border: '2px solid #e6e6e6' }">
			</v-swatches>
		</div>
	</div>
</template>

<script>
	import VSwatches from 'vue-swatches'

	export default {
		components: { VSwatches },
		props: {
			value: ''
		}
	}
</script>