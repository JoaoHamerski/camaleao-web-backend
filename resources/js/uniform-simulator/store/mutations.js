export default {
	update: (state, payload) => {
		let keys = Object.keys(payload);

		keys.forEach(key => { state[key] = payload[key] });
	},
	updateAttach: (state, payload) => {
		let keys = Object.keys(payload),
			attachIndex = payload.index;

		if (keys.includes('id')) {
			attachIndex = state.attachs.findIndex(el => el.id == payload.id);
		}

		keys.forEach((key, index) => {
			if (key != 'id' && key != 'index') {
				state.attachs[attachIndex][key] = payload[key] 
			}
		});
	},
	deleteAttach: (state, payload) => {
		state.attachs.splice(payload.index, 1);
	},
	appendAttach: (state, payload) => {
		let extraAttachs = ['brand', 'shield'];

		if (extraAttachs.includes(payload.classname)) {
			let index = state.attachs.findIndex(element => element.classname == payload.classname);

			if (index != -1) {
				payload['id'] = state.attachs[index].id;
				state.attachs.splice(index, 1, payload);

				return;
			}
		}

		payload['id'] = state.attachCount;
		state.attachs.push(payload);
	}
}