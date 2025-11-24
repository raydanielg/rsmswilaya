<template>
  <section class="w-full bg-white text-[#1b1b18]">
    <div class="w-full px-4 md:px-8 py-12">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- EVENTS -->
        <div class="lg:col-span-5">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 class="text-2xl font-semibold tracking-wide">EVENTS</h3>
              <div class="h-0.5 w-16 bg-[#0B6B3A] mt-2"></div>
            </div>
            <button class="hidden md:inline-flex items-center px-4 py-1.5 rounded bg-[#1fb64f] text-white text-sm font-medium shadow hover:bg-[#19a244]">All events</button>
          </div>
          <div class="bg-white border border-[#ecebea] rounded">
            <div class="grid grid-cols-3 gap-2 px-3 py-2 text-xs uppercase text-[#6b6a67] bg-[#f7f7f6]">
              <div class="col-span-2 inline-flex items-center gap-2">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 5h16M4 12h16M4 19h16"/></svg>
                <span>Title</span>
              </div>
              <div class="inline-flex items-center gap-2">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                <span>Date</span>
              </div>
            </div>
            <ul>
              <li v-for="(e, idx) in events" :key="idx" class="grid grid-cols-3 gap-2 px-3 py-3 border-t text-sm">
                <div class="col-span-2 text-[#1b1b18] font-medium inline-flex items-start gap-2">
                  <svg class="mt-[2px] h-4 w-4 text-[#0B6B3A]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20l9-5-9-5-9 5 9 5z"/><path d="M12 12l9-5-9-5-9 5 9 5z"/></svg>
                  <span class="leading-5">{{ e.title }}</span>
                </div>
                <div class="text-[#4f4e4b] inline-flex items-center gap-2">
                  <svg class="h-4 w-4 text-[#6b6a67]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                  <span>{{ formatRange(e.start, e.end) }}</span>
                </div>
              </li>
              <li v-if="!events.length" class="px-3 py-4 text-sm text-[#6b6a67]">No events yet.</li>
            </ul>
          </div>
          <div class="mt-6 md:hidden">
            <button class="inline-flex items-center px-4 py-1.5 rounded bg-[#1fb64f] text-white text-sm font-medium shadow hover:bg-[#19a244]">All events</button>
          </div>
        </div>

        <!-- NEWS -->
        <div class="lg:col-span-7">
          <div class="mb-4">
            <h3 class="text-2xl font-semibold tracking-wide">NEWS</h3>
            <div class="h-0.5 w-16 bg-[#0B6B3A] mt-2"></div>
          </div>
          <ul class="divide-y divide-[#ecebea]">
            <li v-for="(n, idx) in news" :key="idx" class="flex items-start gap-4 py-4">
              <div class="w-16 text-center shrink-0">
                <div class="text-2xl font-semibold text-[#6b6a67]">{{ day(n.date) }}</div>
                <div class="text-xs uppercase text-[#6b6a67]">{{ shortDow(n.date) }}</div>
                <div class="text-xs uppercase text-[#6b6a67]">{{ shortMon(n.date) }}</div>
              </div>
              <div class="flex-1">
                <a :href="`/blogs/${n.slug}`" class="block hover:underline text-[#1b1b18]">{{ n.title }}</a>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const events = ref([])
const news = ref([])

const dow = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']
const mons = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']

function formatRange(s, e){
  const sd = new Date(s); const ed = e ? new Date(e) : sd;
  const sdStr = `${mons[sd.getMonth()]} ${sd.getDate()}, ${sd.getFullYear()}`
  const edStr = `${mons[ed.getMonth()]} ${ed.getDate()}, ${ed.getFullYear()}`
  return sdStr === edStr ? sdStr : `${sdStr} - ${edStr}`
}
function day(d){ return new Date(d).getDate() }
function shortDow(d){ return dow[new Date(d).getDay()] }
function shortMon(d){ return mons[new Date(d).getMonth()] }

async function loadEvents(){
  const today = new Date();
  const past = new Date(today); past.setDate(past.getDate() - 90);
  const future = new Date(today); future.setDate(future.getDate() + 180);
  const qs = new URLSearchParams({ start: past.toISOString().slice(0,10), end: future.toISOString().slice(0,10) })
  const res = await fetch(`/api/events?${qs}`)
  if (res.ok){
    const data = await res.json()
    events.value = (data||[]).map(ev=> ({ title: ev.title, start: ev.start, end: ev.end }))
  }
}

async function loadNews(){
  const res = await fetch('/api/news')
  if (res.ok){
    const data = await res.json()
    news.value = (data||[]).map(n=> ({ title: n.title, slug: n.slug, date: n.date }))
  }
}

onMounted(()=>{ loadEvents(); loadNews() })
</script>

<style scoped>
</style>
