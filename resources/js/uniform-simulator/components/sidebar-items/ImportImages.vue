<template>
	<div>
		<h6 class="sidebar-dropdown-title">ESCUDO E MARCA</h6>
		<div class="sidebar-dropdown-content d-flex mb-4 no-gutters">
			<div class="col">
				<input @change="upload($event, 'shield')" class="d-none" type="file" id="shield-image">
				<label for="shield-image" class="px-1 btn font-weight-bold btn-lg btn-block btn-primary">
					<i class="fas fa-plus fa-fw mr-1"></i> ESCUDO
				</label>
			</div>
			<div class="mx-2"></div>
			<div class="col">
				<input @change="upload($event, 'brand')" class="d-none" type="file" id="brand-image">
				<label for="brand-image" class="px-1 btn font-weight-bold btn-lg btn-block btn-primary">
					<i class="fas fa-plus fa-fw mr-1"></i> MARCA
				</label>
			</div>
		</div>

		<h6 class="sidebar-dropdown-title">EXTRAS</h6>
		<div class="sidebar-dropdown-content">
			<input @change="upload($event)" class="d-none" type="file" id="extra-image">
			<label for="extra-image" class="px-1 btn font-weight-bold btn-lg btn-block btn-primary">
				<i class="fas fa-plus fa-fw mr-1"></i> IMAGEM
			</label>
		</div>
	</div>
</template>

<script>
	export default {
		mixins: [shirtDefaultStateAfterClose],
		methods: {
			upload(event, attachClass = null) {
				let input = event.target;

				if (input.files && input.files[0]) {
					let reader = new FileReader();
					let file = input.files[0];

					reader.onload = (e) => {
						EventBus.$emit('ATTACH_UPLOADED', {
							name: file.name,
							image: e.target.result,
							size: file.size,
							type: file.type,
							class: attachClass ?? 'image'
						});
					}

					reader.readAsDataURL(input.files[0]);
				}
			}
		}
	}
</script>