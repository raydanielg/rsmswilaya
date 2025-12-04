<template>
  <section class="bg-gray-50 p-3 sm:p-5">
    <div class="mx-auto max-w-screen-xl px-4 lg:px-12 space-y-5">
      <!-- Filters bar -->
      <div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden p-4">
        <div class="flex flex-wrap items-center gap-3">
          <div>
            <label class="block text-xs text-gray-600">Exam</label>
            <select v-model="filters.exam" @change="refreshAll" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50">
              <option v-for="e in exams" :key="e" :value="e">{{ e }}</option>
            </select>
          </div>
          <div>
            <label class="block text-xs text-gray-600">Region</label>
            <select v-model="filters.region_id" @change="onRegion" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50">
              <option value="">All</option>
              <option v-for="r in regions" :key="r.id" :value="String(r.id)">{{ r.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-xs text-gray-600">Council</label>
            <select v-model="filters.district_id" @change="onDistrict" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50" :disabled="!districts.length">
              <option value="">All</option>
              <option v-for="d in districts" :key="d.id" :value="String(d.id)">{{ d.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-xs text-gray-600">School</label>
            <select v-model="filters.school_id" @change="refreshList" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50" :disabled="!schools.length">
              <option value="">All</option>
              <option v-for="s in schools" :key="s.id" :value="String(s.id)">{{ s.name }}</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Years accordion -->
      <div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden">
        <div v-if="loading" class="p-4 text-sm text-gray-500">Loading results…</div>
        <div v-else>
          <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
              <tr>
                <th class="px-4 py-2">Year</th>
                <th class="px-4 py-2">Documents</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="year in years" :key="year" class="hover:bg-gray-100">
                <td class="px-4 py-3 text-gray-900 font-medium">
                  <button class="flex items-center gap-2" @click="toggle(year)">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path :d="openYears[year] ? 'M19 9l-7 7-7-7' : 'M9 5l7 7-7 7'"/></svg>
                    <span>{{ year }}</span>
                  </button>
                </td>
                <td class="px-4 py-3 text-sm">{{ (list[year] || []).length }}</td>
              </tr>
              <tr v-for="year in years" :key="year+'-panel'" v-show="openYears[year]">
                <td colspan="2" class="px-4 pb-4">
                  <ul class="space-y-2">
                    <li v-for="doc in (list[year] || [])" :key="doc.url" class="flex items-center justify-between border border-gray-200 rounded-md p-3">
                      <div class="text-gray-900 flex items-center gap-2">
                        <span class="font-medium">{{ doc.title }}</span>
                        <span v-if="doc.type_name" class="text-xs text-gray-500">· {{ doc.type_name }}</span>
                        <span v-if="isMidTermNew(doc)" class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#E6FF8A] text-[#0B6B3A] border border-[#d8f76d]">NEW</span>
                      </div>
                      <div class="flex items-center gap-2">
                        <a :href="viewerUrl(doc.url)" target="_blank" class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-100">View</a>
                      </div>
                    </li>
                    <li v-if="!(list[year]||[]).length" class="text-sm text-gray-500">No documents for this year.</li>
                  </ul>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const exams = ref(["SFNA","PSLE","FTNA","CSEE","ACSEE"]) // fallback
const regions = ref([])
const districts = ref([])
const schools = ref([])
const years = ref([])
const list = ref({})
const loading = ref(true)
const openYears = ref({})

const filters = ref({ exam: 'CSEE', region_id: '', district_id: '', school_id: '' })

function toggle(year){ openYears.value[year] = !openYears.value[year] }

async function loadFilters(){
  const params = new URLSearchParams()
  if (filters.value.region_id) params.set('region_id', filters.value.region_id)
  if (filters.value.district_id) params.set('district_id', filters.value.district_id)
  const res = await fetch(`/api/results/filters?${params.toString()}`)
  if (res.ok){
    const d = await res.json()
    exams.value = d.exams || exams.value
    regions.value = d.regions || []
    districts.value = d.districts || []
    schools.value = d.schools || []
  }
}

async function loadYears(){
  if (!filters.value.exam) return
  const res = await fetch(`/api/results/years?exam=${filters.value.exam}`)
  years.value = res.ok ? await res.json() : []
}

async function loadList(){
  loading.value = true
  try{
    const params = new URLSearchParams()
    params.set('exam', filters.value.exam || '')
    if (filters.value.region_id) params.set('region_id', filters.value.region_id)
    if (filters.value.district_id) params.set('district_id', filters.value.district_id)
    if (filters.value.school_id) params.set('school_id', filters.value.school_id)
    const res = await fetch(`/api/results/list?${params.toString()}`)
    list.value = res.ok ? await res.json() : {}
  } finally { loading.value = false }
}

async function refreshAll(){
  await loadYears(); await loadList()
}
async function onRegion(){
  filters.value.district_id=''; filters.value.school_id=''
  await loadFilters(); await loadList()
}
async function onDistrict(){
  filters.value.school_id=''
  await loadFilters(); await loadList()
}
async function refreshList(){ await loadList() }

onMounted(async()=>{
  await loadFilters(); await refreshAll()
})

function isMidTermNew(doc){
  try{
    const name = String(doc.type_name || '').toLowerCase()
    const isMid = name.includes('mid') && name.includes('term')
    if (!isMid) return false
    const created = new Date(doc.created_at)
    if (isNaN(created.getTime())) return false
    const now = new Date()
    const diffDays = (now - created) / (1000*60*60*24)
    return diffDays <= 14
  } catch { return false }
}

function viewerUrl(u){
  try{
    const src = encodeURIComponent(String(u || ''))
    return `/view/pdf?src=${src}`
  } catch { return String(u || '#') }
}

function isExternal(u){
  try{
    const url = new URL(String(u), window.location.origin)
    return url.origin !== window.location.origin
  } catch { return false }
}

function filenameFromUrl(u){
  try{
    const url = new URL(String(u), window.location.origin)
    const parts = url.pathname.split('/')
    const last = parts[parts.length-1] || 'document.pdf'
    return last
  } catch { return 'document.pdf' }
}
</script>
