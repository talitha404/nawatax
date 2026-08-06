import Alpine from 'alpinejs'
import { exportPdf } from './pdfExport'

window.Alpine = Alpine
window.exportPdf = exportPdf

Alpine.start()
