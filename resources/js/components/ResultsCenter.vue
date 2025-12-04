<template>
  <section class="bg-gray-50 p-3 sm:p-5">
    <div class="mx-auto max-w-screen-xl px-4 lg:px-12 space-y-5">
      <!-- Step 1: Select CSS (Subject) -->
      <div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden p-4">
        <div class="flex flex-wrap items-center gap-3">
          <div>
            <label class="block text-xs text-gray-600 font-semibold">CSS (Subject)</label>
            <select v-model="filters.exam" @change="refreshAll" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50">
              <option v-for="e in exams" :key="e" :value="e">{{ e }}</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Step 2: Select Year -->
      <div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden p-4">
        <div class="flex flex-wrap items-center gap-3">
          <div>
            <label class="block text-xs text-gray-600 font-semibold">Year</label>
            <select v-model="filters.year" @change="onYearChange" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50">
              <option value="">Select Year</option>
              <option v-for="y in years" :key="y" :value="String(y)">{{ y }}</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Step 3: Schools List with A-Z Filters -->
      <div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden">
        <div class="p-4">
          <!-- Filter Instructions -->
          <div class="text-center mb-4">
            <p class="text-sm font-semibold text-gray-900">CLICK ANY LETTER BELOW TO FILTER SCHOOLS BY ALPHABET</p>
          </div>
          
          <!-- A-Z Filter Buttons -->
          <div class="flex flex-wrap gap-1 mb-6 justify-center">
            <button 
              @click="schoolFilter = ''"
              :class="schoolFilter === '' ? 'bg-[#0B6B3A] text-white' : 'bg-[#E6FF8A] text-gray-900'"
              class="px-3 py-2 rounded text-xs font-bold border border-gray-400 hover:opacity-80">
              ALL CENTRES
            </button>
            <button 
              v-for="letter in alphabet"
              :key="letter"
              @click="schoolFilter = letter"
              :class="schoolFilter === letter ? 'bg-[#0B6B3A] text-white' : 'bg-[#E6FF8A] text-gray-900'"
              class="px-3 py-2 rounded text-xs font-bold border border-gray-400 hover:opacity-80">
              {{ letter }}
            </button>
          </div>

          <!-- Schools List in 3 Columns -->
          <div v-if="loading" class="p-4 text-sm text-gray-500 text-center">Loading schools…</div>
          <div v-else-if="filteredSchools.length === 0" class="p-4 text-sm text-gray-500 text-center">No schools found.</div>
          <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-0 border border-gray-400">
            <div 
              v-for="(school, idx) in filteredSchools" 
              :key="school.id"
              :class="[
                'p-2 text-xs text-gray-900 bg-[#E6FF8A] border-gray-400',
                idx % 3 !== 2 ? 'border-r' : '',
                Math.floor(idx / 3) !== Math.floor((filteredSchools.length - 1) / 3) ? 'border-b' : ''
              ]">
              <div class="font-semibold">{{ school.code || '' }} - {{ school.name }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'

const exams = ref(["SFNA","PSLE","FTNA","CSEE","ACSEE"]) // fallback
const schools = ref([])
const years = ref([])
const loading = ref(true)
const schoolFilter = ref('')
const alphabet = ref('ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split(''))

const filters = ref({ exam: 'CSEE', year: '' })

const filteredSchools = computed(() => {
  if (!schoolFilter.value) return schools.value
  return schools.value.filter(s => s.name.toUpperCase().startsWith(schoolFilter.value))
})

async function loadSchools(){
  loading.value = true
  try{
    const params = new URLSearchParams()
    params.set('exam', filters.value.exam || '')
    const res = await fetch(`/api/results/schools?${params.toString()}`)
    schools.value = res.ok ? await res.json() : []
  } finally { loading.value = false }
}

async function loadYears(){
  if (!filters.value.exam) return
  const res = await fetch(`/api/results/years?exam=${filters.value.exam}`)
  years.value = res.ok ? await res.json() : []
}

async function refreshAll(){
  await loadYears(); await loadSchools()
}

async function onYearChange(){
  // Year selection triggers school list update
  await loadSchools()
}

onMounted(async()=>{
  await refreshAll()
})
</script>
