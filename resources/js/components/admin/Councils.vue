<template>
  <div class="max-w-screen-xl mx-auto space-y-8">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-3">
        <h1 class="text-2xl font-semibold">Councils</h1>
        <select v-model="filters.region" @change="fetchCouncils" class="border border-[#e5e4e2] rounded px-3 py-2 text-sm">
          <option :value="''">All regions</option>
          <option v-for="r in regions" :key="r.id" :value="String(r.id)">{{ r.name }}</option>
        </select>
      </div>
      <div class="flex items-center gap-3">
        <a href="/admin/councils/template" class="px-3 py-2 rounded border hover:bg-[#f7f7f6] text-sm">Download CSV Template</a>
        <button class="px-3 py-2 rounded border hover:bg-[#f7f7f6] text-sm" @click="openBulk = true">Bulk Upload</button>
        <button class="px-4 py-2 rounded bg-[#0AA74A] text-white hover:bg-[#089543]" @click="openAdd = true">Add Council</button>
      </div>
    </div>

    <div class="border-t border-dashed border-[#d7d6d4]"></div>

    <div v-if="loading" class="text-sm text-[#6b6a67]">Loading councils…</div>
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      <div v-for="c in councils" :key="c.id" class="group relative rounded-lg border border-[#ecebea] bg-white p-4 shadow-sm hover:shadow transition cursor-pointer" @click="openEditFor(c)">
        <div class="flex items-center justify-between">
          <div class="text-base font-semibold text-[#0B6B3A]">{{ c.name }}</div>
          <span class="inline-flex items-center justify-center h-7 min-w-[2.5rem] px-2 rounded-full bg-[#0AA74A] text-white text-sm font-semibold">{{ formatCount(c.schools_count) }}</span>
        </div>
        <div class="mt-2 text-xs text-[#6b6a67]">Council</div>
      </div>
    </div>

    <!-- Add single council modal -->
    <div v-if="openAdd" class="fixed inset-0 z-50">
      <div class="absolute inset-0 bg-black/40" @click="closeAdd"/>
      <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-md shadow-lg w-[95vw] max-w-md border border-[#ecebea]">
        <div class="px-4 py-3 border-b border-[#ecebea] flex items-center justify-between">
          <div class="font-semibold text-[#0B6B3A]">Add Council</div>
          <button class="text-[#6b6a67] hover:text-[#0B6B3A]" @click="closeAdd">✕</button>
        </div>
        <form @submit.prevent="createCouncil" class="p-4 space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Region</label>
            <select v-model="form.region_id" required class="w-full border border-[#e5e4e2] rounded px-3 py-2">
              <option value="">-- Select Region --</option>
              <option v-for="r in regions" :key="r.id" :value="String(r.id)">{{ r.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Council name</label>
            <input v-model="form.name" type="text" required class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" placeholder="Ilala" />
            <div v-if="errorAdd" class="text-sm text-red-600 mt-1">{{ errorAdd }}</div>
          </div>
          <div class="flex justify-end gap-3">
            <button type="button" class="px-4 py-2 rounded border" @click="closeAdd">Cancel</button>
            <button :disabled="submittingAdd" class="px-4 py-2 rounded bg-[#0B6B3A] text-white hover:bg-[#095a31] disabled:opacity-60">{{ submittingAdd ? 'Saving…' : 'Save' }}</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Bulk upload modal -->
    <div v-if="openBulk" class="fixed inset-0 z-50">
      <div class="absolute inset-0 bg-black/40" @click="closeBulk"/>
      <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-md shadow-lg w-[95vw] max-w-md border border-[#ecebea]">
        <div class="px-4 py-3 border-b border-[#ecebea] flex items-center justify-between">
          <div class="font-semibold text-[#0B6B3A]">Bulk Upload Councils (CSV)</div>
          <button class="text-[#6b6a67] hover:text-[#0B6B3A]" @click="closeBulk">✕</button>
        </div>
        <form @submit.prevent="uploadBulk" class="p-4 space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Region</label>
            <select v-model="bulk.region_id" required class="w-full border border-[#e5e4e2] rounded px-3 py-2">
              <option value="">-- Select Region --</option>
              <option v-for="r in regions" :key="r.id" :value="String(r.id)">{{ r.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">CSV file</label>
            <input ref="fileInput" type="file" accept=".csv,.txt" required />
            <p class="text-xs text-[#6b6a67] mt-1">One council name per line, or a CSV with header "name"</p>
            <a href="/admin/councils/template" class="text-xs text-[#0B6B3A] hover:underline">Download template</a>
            <div v-if="errorBulk" class="text-sm text-red-600 mt-1">{{ errorBulk }}</div>
          </div>
          <div class="flex justify-end gap-3">
            <button type="button" class="px-4 py-2 rounded border" @click="closeBulk">Cancel</button>
            <button :disabled="submittingBulk" class="px-4 py-2 rounded bg-[#0B6B3A] text-white hover:bg-[#095a31] disabled:opacity-60">{{ submittingBulk ? 'Uploading…' : 'Upload' }}</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Edit council modal -->
    <div v-if="openEdit" class="fixed inset-0 z-50">
      <div class="absolute inset-0 bg-black/40" @click="closeEdit"/>
      <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-md shadow-lg w-[95vw] max-w-md border border-[#ecebea]">
        <div class="px-4 py-3 border-b border-[#ecebea] flex items-center justify-between">
          <div class="font-semibold text-[#0B6B3A]">Edit Council</div>
          <button class="text-[#6b6a67] hover:text-[#0B6B3A]" @click="closeEdit">✕</button>
        </div>
        <form @submit.prevent="updateCouncil" class="p-4 space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Council name</label>
            <input v-model="editForm.name" type="text" required class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" />
            <div v-if="errorEdit" class="text-sm text-red-600 mt-1">{{ errorEdit }}</div>
          </div>
          <div class="flex justify-end gap-3">
            <button type="button" class="px-4 py-2 rounded border" @click="closeEdit">Cancel</button>
            <button :disabled="submittingEdit" class="px-4 py-2 rounded bg-[#0B6B3A] text-white hover:bg-[#095a31] disabled:opacity-60">{{ submittingEdit ? 'Saving…' : 'Save' }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const regions = ref([])
const councils = ref([])
const loading = ref(true)
const filters = ref({ region: '' })

const openAdd = ref(false)
const form = ref({ region_id: '', name: '' })
const errorAdd = ref('')
const submittingAdd = ref(false)

const openBulk = ref(false)
const bulk = ref({ region_id: '' })
const fileInput = ref(null)
const errorBulk = ref('')
const submittingBulk = ref(false)

const openEdit = ref(false)
const editForm = ref({ id: '', name: '' })
const submittingEdit = ref(false)
const errorEdit = ref('')

function formatCount(n){ return Number(n||0).toLocaleString() }

async function updateCouncil(){
  submittingEdit.value = true; errorEdit.value = ''
  try {
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    const res = await fetch(`/api/admin/councils/${editForm.value.id}`, {
      method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
      body: JSON.stringify({ name: editForm.value.name })
    })
    if(res.status === 422){ const d = await res.json(); errorEdit.value = d?.message || 'Validation error'; return }
    if(!res.ok) throw new Error('update failed')
    const updated = await res.json()
    councils.value = councils.value.map(x => x.id === Number(updated.id) ? { ...x, name: updated.name } : x).sort((a,b)=> a.name.localeCompare(b.name))
    closeEdit()
  } catch(e){ console.error(e); errorEdit.value='Failed to update' } finally { submittingEdit.value=false }
}

async function fetchRegions(){
  const res = await fetch('/api/admin/regions', { headers: { 'Accept': 'application/json' } })
  if (res.ok) regions.value = await res.json()
}

async function fetchCouncils(){
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.value.region) params.set('region_id', filters.value.region)
    const res = await fetch(`/api/admin/councils?${params.toString()}`)
    if(!res.ok) throw new Error('Failed')
    councils.value = await res.json()
  } catch(e){ console.error(e) } finally { loading.value = false }
}

function closeAdd(){ openAdd.value = false; form.value = { region_id: filters.value.region || '', name: '' }; errorAdd.value=''}
function closeBulk(){ openBulk.value = false; bulk.value = { region_id: filters.value.region || '' }; errorBulk.value='' }

function openEditFor(c){
  editForm.value = { id: String(c.id), name: c.name }
  openEdit.value = true
}
function closeEdit(){ openEdit.value = false; editForm.value = { id: '', name: '' }; errorEdit.value='' }

async function createCouncil(){
  submittingAdd.value = true; errorAdd.value = ''
  try {
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    const res = await fetch('/api/admin/councils', {
      method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
      body: JSON.stringify({ region_id: form.value.region_id, name: form.value.name })
    })
    if(res.status === 422){ const d = await res.json(); errorAdd.value = d?.message || 'Validation error'; return }
    if(!res.ok) throw new Error('save failed')
    const created = await res.json()
    if (!filters.value.region || String(created.region_id) === String(filters.value.region)) {
      councils.value = [...councils.value, created].sort((a,b)=> a.name.localeCompare(b.name))
    }
    closeAdd()
  } catch(e){ errorAdd.value='Failed to save' } finally { submittingAdd.value=false }
}

async function uploadBulk(){
  submittingBulk.value = true; errorBulk.value = ''
  try {
    const file = fileInput.value?.files?.[0]
    if (!file) { errorBulk.value = 'Please choose a CSV file'; return }
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    const fd = new FormData(); fd.append('region_id', bulk.value.region_id); fd.append('file', file)
    const res = await fetch('/api/admin/councils/bulk', { method:'POST', headers: { 'X-CSRF-TOKEN': csrf }, body: fd })
    if(res.status === 422){ const d = await res.json(); errorBulk.value = d?.message || 'Validation error'; return }
    if(!res.ok) throw new Error('upload failed')
    // refresh list
    await fetchCouncils()
    closeBulk()
  } catch(e){ console.error(e); errorBulk.value='Failed to upload' } finally { submittingBulk.value=false }
}

onMounted(async()=>{
  await fetchRegions()
  // default selected region if any
  form.value.region_id = filters.value.region
  bulk.value.region_id = filters.value.region
  await fetchCouncils()
})
</script>
