export default {
	brand: state => {
		return state.attachs.find(el => el.classname == 'brand');
	},
	shield: state => {
		return state.attachs.find(el => el.classname == 'shield');
	},
	extraAttachs: state => {
		return state.attachs.filter(el => ! ['brand', 'shield'].includes(el.classname));
	},
	hasAttach: state => {
		return state.attachs.length !== 0;
	},
	hasActiveAttach: state => {
		return state.attachs.find(el => el.active === true)
	},
	frontAttachs: (state, getters) => {
		return getters.extraAttachs.filter(el => el.isFront === true);
	},
	backAttachs: (state, getters) => {
		return getters.extraAttachs.filter(el => el.isFront === false);
	}
}