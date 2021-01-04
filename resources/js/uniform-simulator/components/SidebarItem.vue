<template>
	<div class="sidebar-item">
		<div class="sidebar-item-content dropright" 
			:class="{'is-active': isActive}"
			data-toggle="dropdown">

			<div class="sidebar-icon">
				<slot name="icon"></slot>
			</div>

			<div class="sidebar-label">
				<slot name="label"></slot>
			</div>
		</div>

		<div ref="dropdown" @click.stop class="dropdown-menu sidebar-dropdown dropright">
		    <slot name="content"></slot>
		</div>
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
			
			$(this.$el).on('shown.bs.dropdown', () => {
				this.isActive = true;
			});

			$(this.$el).on('hidden.bs.dropdown', () => {
				this.isActive = false;
			});
		}
	}
</script>