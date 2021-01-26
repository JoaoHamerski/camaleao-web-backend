<template>
	<li @click="activate"
		class="sidebar-attach-item list-group-item list-group-item-action d-flex flex-column" 
		:class="{ 'list-group-item-primary' : attach.active }">
		<div class="d-flex flex-row align-items-center no-gutters">
			<div class="col-md-3 text-center">
				<img class="img-fluid attach-item-image" :src="attach.image">
			</div>

			<div class="col-md-9 d-flex flex-column px-2 text-wrap">
				<slot name="label"></slot>
				<slot name="attach-name"></slot>
			</div>
		</div>
	</li>
</template>

<script>
	export default {
		props: {
			attach: null
		},
		computed: {
			index() {
				return this.$store.state.attachs.findIndex(el => el.id === this.attach.id);
			}
		},
		methods: {
			activate() {
				this.$store.commit('updateAttach', { index: this.index, active: true });
			}
		}
	}
</script>