import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static values = {
    url: String,
    cascader: String,
  }

  change(event) {
    const cascaderEl = document.getElementById(this.cascaderValue)
    if (!cascaderEl) return

    fetch(`${this.urlValue}?parent=${event.target.value}`, {
      headers: { Accept: 'application/json' },
    }).then(async response => {
      const json = await response.json()
      return response.ok
        ? Promise.resolve(json)
        : Promise.reject(json.detail || response.statusText)
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
