<script setup>
import { onMounted, ref, watch } from 'vue'
import * as pdfjsLib from 'pdfjs-dist'
import pdfWorkerSrc from 'pdfjs-dist/build/pdf.worker?url'

pdfjsLib.GlobalWorkerOptions.workerSrc = pdfWorkerSrc

const props = defineProps({
  src: {
    type: String,
    required: true
  }
})

const containerRef = ref(null)
const loading = ref(true)
const error = ref(null)
const pageCount = ref(0)

async function renderPdf(url) {
  loading.value = true
  error.value = null
  pageCount.value = 0

  const container = containerRef.value
  if (!container) return
  container.innerHTML = ''

  try {
    const loadingTask = pdfjsLib.getDocument(url)
    const pdf = await loadingTask.promise
    pageCount.value = pdf.numPages

    for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
      const page = await pdf.getPage(pageNum)
      const viewport = page.getViewport({ scale: 1.3 })

      const pageWrapper = document.createElement('div')
      pageWrapper.className = 'bg-white shadow rounded mb-4 overflow-hidden'

      const canvas = document.createElement('canvas')
      canvas.className = 'block mx-auto'
      const context = canvas.getContext('2d')
      canvas.height = viewport.height
      canvas.width = viewport.width

      pageWrapper.appendChild(canvas)
      container.appendChild(pageWrapper)

      await page.render({ canvasContext: context, viewport }).promise
    }
  } catch (e) {
    console.error(e)
    error.value = 'Failed to load document.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  if (props.src) {
    renderPdf(props.src)
  }
})

watch(
  () => props.src,
  (val) => {
    if (val) {
      renderPdf(val)
    }
  }
)
</script>

<template>
  <div class="min-h-screen bg-[#0b0b0b] text-white flex flex-col">
    <header class="fixed top-0 inset-x-0 z-50 bg-[#0B6B3A] text-white border-b border-black/10">
      <div class="h-12 px-3 md:px-6 flex items-center justify-between gap-3">
        <div class="flex items-center gap-2">
          <button type="button" onclick="history.back()" class="inline-flex items-center justify-center h-8 w-8 rounded hover:bg-white/10" title="Back" aria-label="Back">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
          </button>
          <div class="truncate max-w-[55vw] md:max-w-[60vw]">
            <div class="text-sm md:text-base font-medium truncate">PDF Document</div>
            <div class="text-[11px] md:text-xs text-white/80 truncate">Rendered with PDF.js</div>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <a :href="src" target="_blank" rel="noopener" class="hidden sm:inline-flex text-xs px-3 py-1.5 rounded bg-[#0AA74A] hover:bg-[#089543]">Open original</a>
          <a :href="src" :download="'document.pdf'" class="inline-flex text-xs px-3 py-1.5 rounded border border-white/20 hover:bg-white/10">Download</a>
        </div>
      </div>
    </header>

    <main class="pt-12 flex-1 overflow-auto">
      <div class="max-w-5xl mx-auto px-3 md:px-6 py-4">
        <div v-if="loading" class="text-sm text-gray-200">Loading document…</div>
        <div v-else-if="error" class="text-sm text-red-400">{{ error }}</div>
        <div v-else>
          <p class="text-xs text-gray-300 mb-3" v-if="pageCount">{{ pageCount }} page(s)</p>
          <div ref="containerRef" class="flex flex-col items-stretch"></div>
        </div>
      </div>
    </main>
  </div>
</template>
