import Errors from './Errors';

class Form {
	constructor (data) {
		this.isLoading = false;
		this.originalData = data;
		this.errors = new Errors();

		for (let field in data) {
			this[field] = data[field];
		}
	}

	reset() {
		for (let field in this.originalData) {
			this[field] = '';
		}

		this.errors.clear();
	}

	data() {
		let data = {};

		for(let property in this.originalData) {
			data[property] = this[property];
		}

		return data;
	}

	submit(method, url) {
		return new Promise((resolve, reject) => {
			axios.request({
				method: method,
				url: url,
				data: this.data()
			})
			.then(response => {
				this.onSuccess();

				resolve(response.data);
			})
			.catch(error => {
				this.onFail(error.response.data.errors);

				reject(error.response);
			})
		});
	}

	onSuccess() {

	}

	onFail(error) {
		this.errors.record(error);
	}
}

export default Form;