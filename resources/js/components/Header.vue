<template>
  <header class="w-full bg-[#0B6B3A] text-white relative z-50">
    <div class="w-full grid grid-cols-3 items-center px-4 md:px-8 py-3">
      <div class="flex items-center">
        <img src="/assets/images/emblem.png" alt="Left Logo" class="h-10 w-auto" />
      </div>
      <div class="flex items-center justify-center">
        <h1 class="text-sm sm:text-base font-semibold tracking-wide text-[#E6FF8A] uppercase text-center">
          Regional Examination Results - Tanzania
        </h1>
      </div>
      <div class="flex items-center justify-end">
        <img src="/assets/images/logo%20(1).png" alt="Right Logo" class="h-10 w-auto" />
      </div>
    </div>
    <nav class="bg-[#0A5F34]">
      <div class="w-full px-4 md:px-8">
        <ul class="flex flex-wrap items-center justify-center gap-6 py-3 text-sm uppercase">
          <li><a class="hover:text-[#E6FF8A]" href="/">Home</a></li>
          <li><a class="hover:text-[#E6FF8A]" href="/about">About us</a></li>

          <li class="relative">
            <button class="inline-flex items-center hover:text-[#E6FF8A]" @click="toggleExam" @keydown.escape="openExam = false" aria-haspopup="menu" :aria-expanded="openExam ? 'true' : 'false'">EXAM TYPES</button>
            <ul v-show="openExam" class="absolute top-full left-0 mt-2 bg-white text-[#0B6B3A] rounded shadow-lg min-w-[220px] overflow-hidden z-50">
              <li><a class="block px-4 py-2 hover:bg-[#E6FF8A] hover:text-[#0B6B3A]" href="/exams/acsee">ACSEE</a></li>
              <li><a class="block px-4 py-2 hover:bg-[#E6FF8A] hover:text-[#0B6B3A]" href="/exams/csee">CSEE</a></li>
              <li><a class="block px-4 py-2 hover:bg-[#E6FF8A] hover:text-[#0B6B3A]" href="/exams/psle">PSLE</a></li>
              <li><a class="block px-4 py-2 hover:bg-[#E6FF8A] hover:text-[#0B6B3A]" href="/exams/ftna">FTNA</a></li>
              <li><a class="block px-4 py-2 hover:bg-[#E6FF8A] hover:text-[#0B6B3A]" href="/exams/sfna">SFNA</a></li>
            </ul>
          </li>


          <li class="relative">
            <button class="inline-flex items-center hover:text-[#E6FF8A]" @click="toggleResults" @keydown.escape="openResults = false" aria-haspopup="menu" :aria-expanded="openResults ? 'true' : 'false'">RESULTS</button>
            <ul v-show="openResults" class="absolute top-full left-0 mt-2 bg-white text-[#0B6B3A] rounded shadow-lg min-w-[220px] overflow-hidden z-50">
              <li><a class="block px-4 py-2 hover:bg-[#E6FF8A] hover:text-[#0B6B3A]" href="/results/acsee">ACSEE</a></li>
              <li><a class="block px-4 py-2 hover:bg-[#E6FF8A] hover:text-[#0B6B3A]" href="/results/csee">CSEE</a></li>
              <li><a class="block px-4 py-2 hover:bg-[#E6FF8A] hover:text-[#0B6B3A]" href="/results/psle">PSLE</a></li>
              <li><a class="block px-4 py-2 hover:bg-[#E6FF8A] hover:text-[#0B6B3A]" href="/results/ftna">FTNA</a></li>
              <li><a class="block px-4 py-2 hover:bg-[#E6FF8A] hover:text-[#0B6B3A]" href="/results/sfna">SFNA</a></li>
            </ul>
          </li>

          <li><a class="hover:text-[#E6FF8A]" href="/calendar">CALENDAR</a></li>
          <li><a class="hover:text-[#E6FF8A]" href="/publications">PUBLICATION</a></li>
          <li><a class="hover:text-[#E6FF8A]" href="/blogs">BLOGS</a></li>
          <li><a class="hover:text-[#E6FF8A]" href="/faq">FAQ</a></li>
          <li><a class="hover:text-[#E6FF8A]" href="/contact">CONTACTS</a></li>
        </ul>
      </div>
    </nav>
  </header>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
const openExam = ref(false)
const openResults = ref(false)

function toggleExam() {
  openExam.value = !openExam.value
}

function toggleResults() {
  openResults.value = !openResults.value
}

function handleClickOutside(e) {
  // Close if clicking outside any open dropdown
  const headerEl = document.querySelector('header')
  if (headerEl && !headerEl.contains(e.target)) {
    openExam.value = false
    openResults.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  // Load site settings for header (title and favicon)
  ;(async () => {
    try {
      const res = await fetch('/api/status')
      if (!res.ok) return
      const data = await res.json()
      if (data.favicon_url) {
        let link = document.querySelector('link[rel="icon"]') || document.createElement('link')
        link.rel = 'icon'
        link.href = data.favicon_url
        document.head.appendChild(link)
      }
    } catch {}
  })()
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
/* minimal visual polish; relies on Tailwind */
</style>
