<template>
	<div class="sidebar-item dropright">
		<div class="sidebar-item-content no-select" 
			:class="{'is-active': isActive}"
			data-toggle="dropdown">

			<div class="sidebar-icon">
				<slot name="icon"></slot>
			</div>

			<div class="sidebar-label">
				<slot name="label"></slot>
			</div>
		</div>

		<transition enter-active-class="animate__animated animate__zoomIn animate__evenFaster">
			<div v-show="isActive"
				ref="dropdown" 
				@click.stop 
				class="dropdown-menu dropdown-sidebar-menu">
			    <slot name="content"></slot>
			</div>
		</transition>
	</div>
</template>

<script>
	export default {
		props: {
			width: {
				default: '400px'
			}
		},
		data() {
			return {
				isActive: false
			}
		},
		mounted() {
			this.$refs.dropdown.style.width = this.width;
			
			$(this.$el).on('shown.bs.dropdown', () => { this.isActive = true });
			$(this.$el).on('hidden.bs.dropdown', () => { this.isActive = false });
		}
	}
</script>

<style>
	
</style>