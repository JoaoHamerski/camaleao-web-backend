import Errors from './Errors'

class Form {
  constructor (data) {
    this.isLoading = false
    this.originalData = data
    this.errors = new Errors()

    for (const field in data) {
      this[field] = data[field]
    }
  }

  reset () {
    for (const field in this.originalData) {
      this[field] = ''
    }

    this.errors.clear('*')
  }

  data () {
    const data = {}

    for(const property in this.originalData) {
      data[property] = this[property]
    }

    return data
  }

  submit (method, url, headers = null) {
    let axiosConfig = {
      method, url, data: this.data()
    }

    if (headers) {
      axiosConfig = {...axiosConfig, headers}
    }

    return new Promise((resolve, reject) => {
      axios.request(axiosConfig)
        .then(response => {
          this.onSuccess()

          resolve(response.data)
        })
        .catch(error => {
          this.onFail(error.response.data.errors)

          reject(error.response)
        })
    })
  }

  onSuccess () {

  }

  onFail (error) {
    this.errors.record(error)
  }
}

export default Form
