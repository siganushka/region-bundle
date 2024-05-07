document.addEventListener('DOMContentLoaded', () => {
  const change = async (event) => {
    const { siganRegionTarget, siganRegionUrl } = event.target.dataset
    const target = document.getElementById(siganRegionTarget)
    if (target) {
      let regions = []

      const placeholder = target.querySelector('option[value=""]')
      if (placeholder) {
        regions.push({ code: '', name: placeholder.textContent })
      }

      if (event.target.value) {
        const response = await fetch(`${siganRegionUrl}?parent=${event.target.value}`, {
          headers: { Accept: 'application/json' }
        })
        regions.push(...await response.json())
      }

      const options = regions.map(el => `<option value="${el.code}">${el.name}</option>`)
      target.innerHTML = options.join('')
      target.dispatchEvent(new Event('change'))
    }
  }

  const elements = document.querySelectorAll('[data-sigan-region-target]')
  elements.forEach(element => {
    element.addEventListener('change', change)
    // element.dispatchEvent(new Event('change'))
  })
})
