<template>
	<vue-drag-resize-rotate ref="drr" 
		class="attach"
		:x="x"
		:y="y"
		:w="w"
		:h="h"
		:selected="attach.active"
		:aspect-ratio="true"
		@select="onActivated"
		@deselect="onDeactivated"
		@drag="onDrag"
		@resize="onResize"
		@rotate="onRotate">
		<slot></slot>
		<div @click="onDelete" class="attach-delete text-center" v-show="attach.active">
			<i class="fas fa-trash-alt"></i>
		</div>
	</vue-drag-resize-rotate>
</template>

<script>
	import VueDragResizeRotate from '@minogin/vue-drag-resize-rotate';

	export default {
		components: { VueDragResizeRotate },
		props: {
			attachId: null,
			x: { default: 0 }, 
			y: { default: 0 },
			w: { default: 100},
			h: { default: 100 }
		},
		computed: {
			index: function() {
				return this.attachs.findIndex(el => el.id == this.attachId);
			},
			attachs: function() {
				return this.$store.state.attachs;
			},
			attach: function() {
				return this.$store.state.attachs[this.index];
			}
		},
		methods: {
			onActivated() {
				// Desseleciona todos anexos manualmente pois a biblioteca não 
				// desseleciona automaticamente quando clica de item em item,
				// apenas quando clica fora do item
				this.attachs.forEach((attach, index) => {
					this.$store.commit('updateAttach', {
						id: attach.id, 
						active: false,
					});
				});

				this.$store.commit('updateAttach', { index: this.index, active: true });
			},
			onDrag() {
				this.$store.commit('updateAttach', { 
					index: this.index,
					x: this.$refs.drr.cx,
					y: this.$refs.drr.cy
				});
			},	
			onResize() {
				this.$store.commit('updateAttach', {
					index: this.index,
					width: this.$refs.drr.width,
					height: this.$refs.drr.height
				});
			},
			onRotate() {
				let angle = this.$helpers.roundInStep(this.$refs.drr.rotation, 10);

				this.$store.commit('updateAttach', {
					index: this.index,
					angle: angle
				});

				this.$refs.drr.rotation = angle;
			},
			onDeactivated() {
				this.$store.commit('updateAttach', { index: this.index, active: false });
			},
			onDelete() {
				this.$store.commit('deleteAttach', { index: this.index });
			}
		}
	}
</script>
