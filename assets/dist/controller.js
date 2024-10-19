import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static values = {
    cascader: String,
    url: String,
  }

  connect() {
    this.element.addEventListener('change', this.change.bind(this))
  }

  disconnect() {
    this.element.removeEventListener('change', this.change.bind(this))
  }

  change(event) {
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
