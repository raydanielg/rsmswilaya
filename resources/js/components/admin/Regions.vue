<template>
  <div class="max-w-screen-xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl border border-[#e3e2df] shadow-sm px-5 md:px-6 pt-4 pb-3 flex items-center justify-between gap-3 flex-wrap">
      <div>
        <h1 class="text-xl md:text-2xl font-semibold">Regions</h1>
        <p class="text-sm text-[#6b6a67] mt-1">Manage regions used to group councils and schools across RSMS.</p>
      </div>
      <div class="flex items-center gap-2">
        <button class="px-3 py-2 rounded-full border border-[#d7d6d4] bg-white hover:bg-[#f7f7f6] text-xs md:text-sm" @click="openBulk = true">Bulk upload locations</button>
        <button class="px-4 py-2 rounded-full bg-[#0AA74A] text-white text-xs md:text-sm hover:bg-[#089543]" @click="openModal = true">Add Region</button>
      </div>
    </div>

    <div v-if="loading" class="text-sm text-[#6b6a67]">Loading regions…</div>
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      <div v-for="r in regions" :key="r.id" class="group relative rounded-lg border border-[#ecebea] bg-white p-4 shadow-sm hover:shadow transition">
        <div class="flex items-center justify-between">
          <div class="text-base font-semibold text-[#0B6B3A]">{{ r.name }}</div>
          <span class="inline-flex items-center justify-center h-7 min-w-[2.5rem] px-2 rounded-full bg-[#0AA74A] text-white text-sm font-semibold">{{ formatCount(r.schools_count) }}</span>
        </div>
        <div class="mt-2 text-xs text-[#6b6a67]">Region</div>
        <div class="absolute inset-x-0 bottom-2 flex items-center justify-end gap-2 px-4 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition">
          <a :href="`/admin/regions/${r.id}`" class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-full border border-[#dad9d6] bg-white hover:bg-[#f7f7f6]">
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 8v4l2 2"/></svg>
            <span>View</span>
          </a>
          <button type="button" class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-full bg-[#0B6B3A] text-white hover:bg-[#095a31]" @click="openEditFor(r)">
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16.5 3.5l4 4L8 20H4v-4z"/><path d="M15 5l4 4"/></svg>
            <span>Edit</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Add Region Modal -->
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

    <!-- Bulk upload modal -->
    <div v-if="openBulk" class="fixed inset-0 z-50">
      <div class="absolute inset-0 bg-black/40" @click="closeBulk" />
      <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-md shadow-lg w-[95vw] max-w-md border border-[#ecebea]">
        <div class="px-4 py-3 border-b border-[#ecebea] flex items-center justify-between">
          <div class="font-semibold text-[#0B6B3A]">Bulk upload locations</div>
          <button class="text-[#6b6a67] hover:text-[#0B6B3A]" @click="closeBulk">✕</button>
        </div>
        <form @submit.prevent="uploadBulk" class="p-4 space-y-4">
          <div class="text-sm text-[#6b6a67]">
            <p class="mb-1">Upload an Excel file with columns for <strong>Region</strong> and <strong>Council</strong> (or District).</p>
            <p class="text-xs">Accepted headers: <code>Region</code> / <code>Region_Name</code> and <code>Council</code>, <code>District</code>, <code>Council_Name</code> or <code>District_Name</code>.</p>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Excel file</label>
            <input ref="bulkFile" type="file" accept=".xlsx,.xls,.csv" required />
            <div v-if="bulkError" class="text-sm text-red-600 mt-1">{{ bulkError }}</div>
          </div>
          <div v-if="bulkResult" class="text-xs text-[#6b6a67] space-y-1 border border-dashed border-[#e0dfdd] rounded p-2">
            <div>Inserted regions: <strong>{{ bulkResult.inserted_regions }}</strong></div>
            <div>Inserted councils: <strong>{{ bulkResult.inserted_councils }}</strong></div>
            <div>Skipped: <strong>{{ bulkResult.skipped }}</strong></div>
            <div v-if="bulkResult.errors && bulkResult.errors.length" class="mt-1">
              <div class="font-semibold">Errors:</div>
              <ul class="list-disc list-inside">
                <li v-for="(e, idx) in bulkResult.errors" :key="idx">{{ e }}</li>
              </ul>
            </div>
          </div>
          <div class="flex justify-end gap-3">
            <button type="button" class="px-4 py-2 rounded border" @click="closeBulk">Cancel</button>
            <button :disabled="bulkSubmitting" class="px-4 py-2 rounded bg-[#0B6B3A] text-white hover:bg-[#095a31] disabled:opacity-60">{{ bulkSubmitting ? 'Uploading…' : 'Upload' }}</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Edit Region Modal -->
    <div v-if="openEdit" class="fixed inset-0 z-50">
      <div class="absolute inset-0 bg-black/40" @click="closeEdit" />
      <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-md shadow-lg w-[95vw] max-w-md border border-[#ecebea]">
        <div class="px-4 py-3 border-b border-[#ecebea] flex items-center justify-between">
          <div class="font-semibold text-[#0B6B3A]">Edit Region</div>
          <button class="text-[#6b6a67] hover:text-[#0B6B3A]" @click="closeEdit">✕</button>
        </div>
        <form v-if="editRegion" :action="`/admin/regions/${editRegion.id}`" method="POST" class="p-4 space-y-4">
          <input type="hidden" name="_token" :value="csrfToken" />
          <div>
            <label class="block text-sm font-medium mb-1">Region name</label>
            <input v-model="editForm.name" type="text" name="name" required class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" />
            <div v-if="editError" class="text-sm text-red-600 mt-1">{{ editError }}</div>
          </div>
          <div class="flex justify-end gap-3">
            <button type="button" class="px-4 py-2 rounded border" @click="closeEdit">Cancel</button>
            <button type="submit" class="px-4 py-2 rounded bg-[#0B6B3A] text-white hover:bg-[#095a31]">Save</button>
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

const openEdit = ref(false)
const editRegion = ref(null)
const editForm = ref({ name: '' })
const editError = ref('')
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''

const openBulk = ref(false)
const bulkFile = ref(null)
const bulkSubmitting = ref(false)
const bulkError = ref('')
const bulkResult = ref(null)

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

function closeBulk(){
  openBulk.value = false
  bulkSubmitting.value = false
  bulkError.value = ''
  bulkResult.value = null
  if (bulkFile.value) bulkFile.value.value = ''
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

async function uploadBulk(){
  bulkSubmitting.value = true
  bulkError.value = ''
  bulkResult.value = null
  try {
    const file = bulkFile.value?.files?.[0]
    if (!file) { bulkError.value = 'Please choose a file'; return }
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    const fd = new FormData()
    fd.append('file', file)
    const res = await fetch('/api/admin/regions/bulk-locations', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrf },
      body: fd,
    })
    const data = await res.json()
    if (!res.ok) {
      bulkError.value = data?.message || 'Upload failed'
      return
    }
    bulkResult.value = data
    await fetchRegions()
  } catch (e) {
    console.error(e)
    bulkError.value = 'Failed to upload locations'
  } finally {
    bulkSubmitting.value = false
  }
}

function openEditFor(region){
  editRegion.value = region
  editForm.value = { name: region.name }
  editError.value = ''
  openEdit.value = true
}

function closeEdit(){
  openEdit.value = false
  editRegion.value = null
  editForm.value = { name: '' }
  editError.value = ''
}

onMounted(fetchRegions)
</script>
