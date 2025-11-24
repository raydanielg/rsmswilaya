<template>
  <div class="max-w-screen-xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Regions</h1>
      <button class="px-4 py-2 rounded bg-[#0AA74A] text-white hover:bg-[#089543]" @click="openModal = true">Add Region</button>
    </div>
    <div class="border-t border-dashed border-[#d7d6d4]"></div>

    <div v-if="loading" class="text-sm text-[#6b6a67]">Loading regions…</div>
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      <div v-for="r in regions" :key="r.id" class="group relative rounded-lg border border-[#ecebea] bg-white p-4 shadow-sm hover:shadow transition">
        <div class="flex items-center justify-between">
          <div class="text-base font-semibold text-[#0B6B3A]">{{ r.name }}</div>
          <span class="inline-flex items-center justify-center h-7 min-w-[2.5rem] px-2 rounded-full bg-[#0AA74A] text-white text-sm font-semibold">{{ formatCount(r.schools_count) }}</span>
        </div>
        <div class="mt-2 text-xs text-[#6b6a67]">Region</div>
        <div class="absolute inset-x-0 bottom-2 flex items-center justify-end gap-2 px-4 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition">
          <a :href="`/admin/regions/${r.id}`" class="text-sm px-3 py-1.5 rounded border hover:bg-[#f7f7f6]">Show</a>
          <a :href="`/admin/regions/${r.id}/edit`" class="text-sm px-3 py-1.5 rounded bg-[#0B6B3A] text-white hover:bg-[#095a31]">Edit</a>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="openModal" class="fixed inset-0 z-50">
      <div class="absolute inset-0 bg-black/40" @click="closeModal" />
      <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-md shadow-lg w-[95vw] max-w-md border border-[#ecebea]">
        <div class="px-4 py-3 border-b border-[#ecebea] flex items-center justify-between">
          <div class="font-semibold text-[#0B6B3A]">Add Region</div>
          <button class="text-[#6b6a67] hover:text-[#0B6B3A]" @click="closeModal">✕</button>
        </div>
        <form @submit.prevent="createRegion" class="p-4 space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Region name</label>
            <input v-model="form.name" type="text" required class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" placeholder="Dar es Salaam" />
            <div v-if="error" class="text-sm text-red-600 mt-1">{{ error }}</div>
          </div>
          <div class="flex justify-end gap-3">
            <button type="button" class="px-4 py-2 rounded border" @click="closeModal">Cancel</button>
            <button :disabled="submitting" class="px-4 py-2 rounded bg-[#0B6B3A] text-white hover:bg-[#095a31] disabled:opacity-60">{{ submitting ? 'Saving…' : 'Save' }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const regions = ref([])
const loading = ref(true)
const openModal = ref(false)
const form = ref({ name: '' })
const error = ref('')
const submitting = ref(false)

function formatCount(n){
  const x = Number(n||0)
  return x.toLocaleString()
}

async function fetchRegions(){
  loading.value = true
  try {
    const res = await fetch('/api/admin/regions', { headers: { 'Accept': 'application/json' } })
    if(!res.ok) throw new Error('Failed to load regions')
    regions.value = await res.json()
  } catch(e){
    console.error(e)
  } finally { loading.value = false }
}

function closeModal(){
  openModal.value = false
  form.value.name = ''
  error.value = ''
}

async function createRegion(){
  submitting.value = true
  error.value = ''
  try {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    const res = await fetch('/api/admin/regions', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
      body: JSON.stringify({ name: form.value.name })
    })
    if(res.status === 422){
      const data = await res.json();
      error.value = (data?.message) || 'Validation error'
      return
    }
    if(!res.ok) throw new Error('Failed to save')
    const newRegion = await res.json()
    regions.value = [...regions.value, newRegion].sort((a,b)=>a.name.localeCompare(b.name))
    closeModal()
  } catch(e){
    console.error(e)
    error.value = 'Failed to save region'
  } finally { submitting.value = false }
}

onMounted(fetchRegions)
</script>
