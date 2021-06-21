import Vue from 'vue'

class Errors {
	constructor() {
		this.errors = { };
	}

	get(field) {
		if (this.errors[field]) {
			return this.errors[field][0];
		}
	}

	has(field) {
		return this.errors.hasOwnProperty(field);
	}

	any() {
		return this.count() > 0;
	}

	count() {
		return Object.keys(this.errors).length;
	}

	clear(field = null) {
		if (field == '*') {
			this.errors = {};
			
			return;
		}

		if (field) {
			Vue.delete(this.errors, field);

			return;	
		}
	}

	record(errors) {
		this.errors = errors;
	}
}

export default Errors;