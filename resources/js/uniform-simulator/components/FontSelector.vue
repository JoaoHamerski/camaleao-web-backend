<template>
	<!-- @slide="$emit('font-changed', fonts[$event.currentSlide.index], $event) -->
	<vueper-slides ref="vueper"
		@slide="$emit('font-changed', fonts[$event.currentSlide.index], $event.currentSlide.index)" 
		class="font-selector-slides no-shadow"
		:fractions="true"
		:bullets="false"
		transition-speed="150"
		:touchable="false">

		<template #arrow-left>
			<i class="fas fa-arrow-left text-dark py-5"></i>
		</template>

		<template #arrow-right>
			<i class="fas fa-arrow-right text-dark py-5"></i>
		</template>

		<vueper-slide
			v-for="(font, i) in fonts"
			:key="i">
			<template #content>
				<div class="d-flex flex-column h-100 justify-content-center align-items-center">
					<div class="font-selector" :class="font.class">
						<slot></slot>
					</div>
					<div class="sidebar-content-label text-secondary">{{ font.name }}</div>
				</div>
			</template>
		</vueper-slide>
	</vueper-slides>
</template>

<script>
	import { VueperSlides, VueperSlide } from 'vueperslides'
  	import 'vueperslides/dist/vueperslides.css'

  	export default {
  		components: { VueperSlides, VueperSlide },
  		data: () => {
  			return {
	  			fonts: [
		          { name: 'Arsenal 2019/2020', class: 'arsenal-19-20' },
		          { name: 'Nike Brasil 2018', class: 'nike-brasil-2018'},
		          { name: 'Real Madrid 2021', class: 'real-madrid-2021'},
		          { name: 'Adidas 2020', class: 'adidas-2020'},
		        ]
  			}
  		}
  	}
</script>
