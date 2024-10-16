document.addEventListener('DOMContentLoaded', () => {
  const change = async (event) => {
    console.log('change...', event.target.dataset)
    const { regionCascaderValue, regionUrlValue } = event.target.dataset
    const cascaderEl = document.getElementById(regionCascaderValue)
    if (!cascaderEl) return

    fetch(`${regionUrlValue}?parent=${event.target.value}`,{
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

  const elements = document.querySelectorAll('[data-controller="region"]')
  elements.forEach(element => element.addEventListener('change', change))
})
