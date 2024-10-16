import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static values = {
    url: String,
    cascader: String,
  }

  connect() {
    this.element.addEventListener('change', this.handleChange.bind(this))
  }

  disconnect() {
    this.element.removeEventListener('change', this.handleChange.bind(this))
  }

  handleChange(event) {
    const cascaderEl = document.getElementById(this.cascaderValue)
    if (!cascaderEl) return

    fetch(`${this.urlValue}?parent=${event.target.value}`,{
      headers: { Accept: 'application/json' },
    }).then(response => {
      return response.ok
        ? response.json()
        : Promise.reject(`${response.status} ${response.statusText}`)
    }).then(res => {
      const options = res.map(el => `<option value="${el.code}">${el.name}</option>`)

      const placeholder = cascaderEl.querySelector('option[value=""]')
      if (placeholder) {
        options.unshift(placeholder.outerHTML)
      }

      cascaderEl.innerHTML = options.join('')
      cascaderEl.dispatchEvent(new Event('change'))
    }).catch(err => alert(err))
  }
}
