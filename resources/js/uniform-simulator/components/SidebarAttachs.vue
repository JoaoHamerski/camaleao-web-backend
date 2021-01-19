<template>
	<div class="sidebar-attachs" :class="{ 'active' : attachs.length !== 0 }">
		<div class="sidebar-attachs-title mb-2">
			ANEXOS
		</div>

		<div class="sidebar-attachs-content">
			<div class="sidebar-attachs-items">
			<template v-for="attach in attachs">
				<div class="sidebar-attach-item d-flex flex-column">
					<div class="d-flex flex-row align-items-center no-gutters">
						<div class="col-md-3">
							<img class="img-fluid" :src="attach.image">
						</div>

						<div class="col-md-9 small text-center image-label text-secondary px-2 text-wrap">
							{{ attach.name }}
						</div>
					</div>
				</div>
			</template>
			</div>	
		</div>
	</div>
</template>

<script>
	export default {
		data: () => {
			return {
				attachs: []
			}
		},
		mounted() {
			EventBus.$on('ATTACH_UPLOADED', (attach) => {
				window.console.log(attach);
				this.attachs.push(attach);
			});		
		}
	}
</script>