<template>
	<div>
		<h6 class="sidebar-dropdown-title">ESCUDO E MARCA</h6>
		<div class="sidebar-dropdown-content d-flex mb-4 no-gutters">
			<div class="col">
				<input ref="shieldImageInput" 
					@change="upload($event, 'shield', 'shieldImageInput')" 
					class="d-none" 
					type="file" 
					id="shield-image">

				<label for="shield-image" class="px-1 btn font-weight-bold btn-lg btn-block btn-primary">
					<i class="fas fa-plus fa-fw mr-1"></i> ESCUDO
				</label>
			</div>

			<div class="mx-2"></div>

			<div class="col">
				<input ref="brandImageInput" 
					@change="upload($event, 'brand', 'brandImageInput')" 
					class="d-none" 
					type="file" 
					id="brand-image">

				<label for="brand-image" class="px-1 btn font-weight-bold btn-lg btn-block btn-primary">
					<i class="fas fa-plus fa-fw mr-1"></i> MARCA
				</label>
			</div>
		</div>

		<h6 class="sidebar-dropdown-title">EXTRAS</h6>
		<div class="sidebar-dropdown-content">
			<input ref="extraImageInput" 
				@input="upload($event, null, 'extraImageInput')" 
				class="d-none" 
				type="file" 
				id="extra-image">
			<label for="extra-image" class="px-1 btn font-weight-bold btn-lg btn-block btn-primary">
				<i class="fas fa-plus fa-fw mr-1"></i> IMAGEM
			</label>
		</div>
	</div>
</template>

<script>
	import Attach from '../../util/Attach';
	export default {
		methods: {
			upload(event, attachClass = null, fileInputRef) {
				let input = event.target;

				if (input.files && input.files[0]) {
					let vm = this;
					let file = input.files[0];

					var image = new Image();
					var objectURL = URL.createObjectURL(file);

					image.onload = function() {
						let reader = new FileReader();

						URL.revokeObjectURL(objectURL);

						reader.onload = (e) => {
							vm.$store.commit('appendAttach', new Attach({
								active: true,
								name: file.name,
								image: e.target.result,
								size: file.size,
								type: file.type,
								width: this.width,
								height: this.height,
								isFront: vm.$store.state.isFront,
								classname: attachClass ?? 'image'
							}));

							vm.$store.commit('update', 
								{ attachCount: vm.$store.state.attachCount + 1 }
							)
						}

						reader.readAsDataURL(input.files[0]);
						vm.$refs[fileInputRef].value = '';
					}

					image.src = objectURL;
				}
			}
		}
	}
</script>