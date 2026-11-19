import './bootstrap'

import Alpine from 'alpinejs'
import Collapse from '@alpinejs/collapse'
import Persist from '@alpinejs/persist'

Alpine.plugin(Collapse)
Alpine.plugin(Persist)

window.Alpine = Alpine
Alpine.start()

/**
 * Automatically wrap standalone data tables inside a horizontal-scroll container
 * so wide ERP tables remain aligned and scrollable on phones and tablets.
 */
function wrapResponsiveTables(root = document) {
  const main = root.querySelector ? (root.matches?.('main') ? root : root.querySelector('main')) : document.querySelector('main')
  if (!main) return

  main.querySelectorAll('table').forEach((table) => {
    if (
      table.closest('.table-wrap') ||
      table.closest('.auto-table-wrap') ||
      table.closest('.overflow-x-auto') ||
      table.closest('.overflow-auto') ||
      table.closest('.print-document') ||
      table.classList.contains('no-wrap-table')
    ) {
      return
    }
    const parent = table.parentElement
    if (!parent) return
    const wrapper = document.createElement('div')
    wrapper.className = 'auto-table-wrap'
    parent.insertBefore(wrapper, table)
    wrapper.appendChild(table)
  })
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => wrapResponsiveTables(document))
} else {
  wrapResponsiveTables(document)
}

