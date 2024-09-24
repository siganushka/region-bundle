const siganushkaRegion = () => {
  const change = async (event) => {
    const { siganRegionTarget, siganRegionUrl } = event.target.dataset
    const target = document.getElementById(siganRegionTarget)
    if (target) {
      const regions = []
  
      const placeholder = target.querySelector('option[value=""]')
      if (placeholder) {
        regions.push({ code: '', name: placeholder.textContent })
      }
  
      if (event.target.value) {
        const headers = { Accept: 'application/json' }
        const response = await fetch(`${siganRegionUrl}?parent=${event.target.value}`, { headers })
        regions.push(... await response.json())
      }
  
      const options = regions.map(el => `<option value="${el.code}">${el.name}</option>`)
      target.innerHTML = options.join('')
      target.dispatchEvent(new Event('change'))
    }
  }

  const elements = document.querySelectorAll('[data-sigan-region-target]')
  elements.forEach(element => element.addEventListener('change', change))
}

// Native event
document.addEventListener('DOMContentLoaded', siganushkaRegion)
// Hotwire turbo event
document.addEventListener('turbo:render', siganushkaRegion)
