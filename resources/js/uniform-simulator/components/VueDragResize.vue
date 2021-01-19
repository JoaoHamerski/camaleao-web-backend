<script>
	import VueDragResize from 'vue-drag-resize';

	export default {
		name: 'VueDragResize',
		extends: VueDragResize,
		props: {
			h: {
				required: false,
				default: 100
			}
		}
	}
</script>