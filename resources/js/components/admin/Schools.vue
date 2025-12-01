<template>
  <section class="bg-gray-50 p-3 sm:p-5">
    <div class="mx-auto max-w-screen-xl px-4 lg:px-12 space-y-5">
    <!-- Toolbar -->
    <div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden p-4">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-3">
          <h1 class="text-2xl font-semibold">Schools</h1>
          <select v-model="filters.region" @change="onRegionChange" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50">
            <option :value="''">All regions</option>
            <option v-for="r in regions" :key="r.id" :value="String(r.id)">{{ r.name }}</option>
          </select>
          <select v-model="filters.district" @change="fetchSchools" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50">
            <option :value="''">All councils</option>
            <option v-for="d in councils" :key="d.id" :value="String(d.id)">{{ d.name }}</option>
          </select>
          <div class="relative">
            <input v-model="search" type="text" placeholder="Search" class="pl-10 pr-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 w-64 focus:ring-primary-500 focus:border-primary-500" />
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
          </div>
          <span class="inline-flex items-center h-7 px-2 rounded-full bg-primary-50 text-primary-700 text-sm font-semibold">{{ filteredSchools.length }} total</span>
          <span v-if="selectedIds.length" class="inline-flex items-center h-7 px-2 rounded-full bg-amber-50 text-amber-800 text-xs font-medium">
            {{ selectedIds.length }} selected
          </span>
        </div>
        <div class="flex items-center gap-3">
          <button v-if="selectedIds.length" class="px-3 py-2 rounded-lg bg-red-600 text-white text-sm hover:bg-red-700" @click="openBulkDelete">
            Delete selected
          </button>
          <a href="/admin/schools/template" class="px-3 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 text-sm">Download CSV Template</a>
          <button class="px-3 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 text-sm" @click="openBulk = true">Bulk Upload</button>
          <button class="px-4 py-2 rounded-lg text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 text-sm" @click="openAdd = true">Add School</button>
        </div>
      </div>
    </div>

    <div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
          <thead class="text-xs text-gray-700 uppercase bg-gray-50 sticky top-0">
            <tr>
              <th class="px-2 py-2 w-10">
                <input type="checkbox" :checked="allVisibleSelected" @change="toggleSelectAll" />
              </th>
              <th class="px-4 py-2">School</th>
              <th class="px-4 py-2">Code</th>
              <th class="px-4 py-2">Level</th>
              <th class="px-4 py-2">Council</th>
              <th class="px-4 py-2">Region</th>
              <th class="px-4 py-2 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td class="px-4 py-3 text-sm text-gray-500" colspan="7">Loading schools…</td>
            </tr>
            <tr v-for="(s, idx) in filteredSchools" :key="s.id" :class="[idx % 2 ? 'bg-gray-50' : 'bg-white', 'hover:bg-gray-100']">
              <td class="px-2 py-2">
                <input type="checkbox" :value="s.id" v-model="selectedIds" />
              </td>
              <td class="px-4 py-2 text-gray-900 font-medium">{{ s.name }}</td>
              <td class="px-4 py-2 text-sm">{{ s.code || '—' }}</td>
              <td class="px-4 py-2 text-sm capitalize">{{ s.level }}</td>
              <td class="px-4 py-2 text-sm">{{ s.district_name }}</td>
              <td class="px-4 py-2 text-sm">{{ s.region_name }}</td>
              <td class="px-4 py-2 text-right">
                <button class="text-sm px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 mr-2" @click="openEditFor(s)">Edit</button>
                <button class="text-sm px-3 py-1.5 rounded-lg bg-red-600 text-white hover:bg-red-700" @click="openDeleteFor(s)">Delete</button>
              </td>
            </tr>
            <tr v-if="!loading && filteredSchools.length === 0">
              <td class="px-4 py-6 text-sm text-gray-500" colspan="7">
                <div class="flex items-center justify-center gap-2">
                  <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                  <span>No schools found. Adjust filters or add a new school.</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add single school modal -->
    <div v-if="openAdd" class="fixed inset-0 z-50">
      <div class="absolute inset-0 bg-black/40" @click="closeAdd"/>
      <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg shadow-md w-[95vw] max-w-md border border-gray-200">
        <div class="px-4 py-3 border-b border-[#ecebea] flex items-center justify-between">
          <div class="font-semibold text-[#0B6B3A]">Add School</div>
          <button class="text-[#6b6a67] hover:text-[#0B6B3A]" @click="closeAdd">✕</button>
        </div>
        <form @submit.prevent="createSchool" class="p-4 space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Region</label>
            <select v-model="addForm.region_id" @change="loadAddDistricts" required class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50">
              <option value="">-- Select Region --</option>
              <option v-for="r in regions" :key="r.id" :value="String(r.id)">{{ r.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Council</label>
            <select v-model="addForm.district_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50">
              <option value="">-- Select Council --</option>
              <option v-for="d in addDistricts" :key="d.id" :value="String(d.id)">{{ d.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">School name</label>
            <input v-model="addForm.name" type="text" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Kibasila Secondary" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">School code (optional)</label>
            <input v-model="addForm.code" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="S1234" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Level / Exam type</label>
            <select v-model="addForm.level" required class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50">
              <option value="secondary">Secondary (CSEE, ACSEE, FTNA)</option>
              <option value="primary">Primary (PSLE, SFNA)</option>
              <option value="other">Other</option>
            </select>
            <div v-if="errorAdd" class="text-sm text-red-600 mt-1">{{ errorAdd }}</div>
          </div>
          <div class="flex justify-end gap-3">
            <button type="button" class="px-4 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-100" @click="closeAdd">Cancel</button>
            <button :disabled="submittingAdd" class="px-4 py-2 rounded-lg text-white bg-primary-700 hover:bg-primary-800 disabled:opacity-60">{{ submittingAdd ? 'Saving…' : 'Save' }}</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Bulk upload modal -->
    <div v-if="openBulk" class="fixed inset-0 z-50">
      <div class="absolute inset-0 bg-black/40" @click="closeBulk"/>
      <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg shadow-md w-[95vw] max-w-md border border-gray-200">
        <div class="px-4 py-3 border-b border-[#ecebea] flex items-center justify-between">
          <div class="font-semibold text-[#0B6B3A]">Bulk Upload Schools (CSV)</div>
          <button class="text-[#6b6a67] hover:text-[#0B6B3A]" @click="closeBulk">✕</button>
        </div>
        <form @submit.prevent="uploadBulk" class="p-4 space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Region</label>
            <select v-model="bulk.region_id" @change="loadBulkDistricts" required class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50">
              <option value="">-- Select Region --</option>
              <option v-for="r in regions" :key="r.id" :value="String(r.id)">{{ r.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Council</label>
            <select v-model="bulk.district_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50">
              <option value="">-- Select Council --</option>
              <option v-for="d in bulkDistricts" :key="d.id" :value="String(d.id)">{{ d.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">CSV file</label>
            <input ref="fileInput" type="file" accept=".csv,.txt" required />
            <p class="text-xs text-gray-500 mt-1">CSV columns: <strong>name, code, level</strong> (header row supported). Region and council are taken from the selections above.</p>
            <a href="/admin/schools/template" class="text-xs text-primary-700 hover:underline">Download template</a>
            <div v-if="errorBulk" class="text-sm text-red-600 mt-1">{{ errorBulk }}</div>
          </div>

          <div v-if="bulkPreviewRows.length" class="border border-gray-200 rounded-lg p-2 max-h-56 overflow-auto text-xs">
            <div class="font-semibold mb-1">Preview ({{ bulkPreviewRows.length }} row<span v-if="bulkPreviewRows.length !== 1">s</span>)</div>
            <table class="w-full text-left">
              <thead>
                <tr class="text-gray-600">
                  <th class="pr-2 py-1">Name</th>
                  <th class="pr-2 py-1">Code</th>
                  <th class="pr-2 py-1">Level</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, i) in bulkPreviewRows" :key="i" class="border-t border-gray-100">
                  <td class="pr-2 py-0.5">{{ row.name }}</td>
                  <td class="pr-2 py-0.5">{{ row.code || '—' }}</td>
                  <td class="pr-2 py-0.5 capitalize">{{ row.level }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="flex justify-end gap-3">
            <button type="button" class="px-4 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-100" @click="closeBulk">Cancel</button>
            <button type="button" class="px-4 py-2 rounded-lg border border-primary-700 text-primary-700 bg-white hover:bg-primary-50" @click="prepareBulkPreview">Preview</button>
            <button type="submit" :disabled="submittingBulk || !bulkPreviewReady" class="px-4 py-2 rounded-lg text-white bg-primary-700 hover:bg-primary-800 disabled:opacity-60">{{ submittingBulk ? 'Uploading…' : 'Confirm Upload' }}</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Edit school modal -->
    <div v-if="openEdit" class="fixed inset-0 z-50">
      <div class="absolute inset-0 bg-black/40" @click="closeEdit"/>
      <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg shadow-md w-[95vw] max-w-md border border-gray-200">
        <div class="px-4 py-3 border-b border-[#ecebea] flex items-center justify-between">
          <div class="font-semibold text-[#0B6B3A]">Edit School</div>
          <button class="text-[#6b6a67] hover:text-[#0B6B3A]" @click="closeEdit">✕</button>
        </div>
        <form @submit.prevent="updateSchool" class="p-4 space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium mb-1">Region</label>
              <select v-model="editForm.region_id" @change="loadEditDistricts" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50">
                <option v-for="r in regions" :key="r.id" :value="String(r.id)">{{ r.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Council</label>
              <select v-model="editForm.district_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50">
                <option v-for="d in editDistricts" :key="d.id" :value="String(d.id)">{{ d.name }}</option>
              </select>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">School name</label>
            <input v-model="editForm.name" type="text" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">School code (optional)</label>
            <input v-model="editForm.code" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Level / Exam type</label>
            <select v-model="editForm.level" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50">
              <option value="secondary">Secondary (CSEE, ACSEE, FTNA)</option>
              <option value="primary">Primary (PSLE, SFNA)</option>
              <option value="other">Other</option>
            </select>
            <div v-if="errorEdit" class="text-sm text-red-600 mt-1">{{ errorEdit }}</div>
          </div>
          <div class="flex justify-end gap-3">
            <button type="button" class="px-4 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-100" @click="closeEdit">Cancel</button>
            <button :disabled="submittingEdit" class="px-4 py-2 rounded-lg text-white bg-primary-700 hover:bg-primary-800 disabled:opacity-60">{{ submittingEdit ? 'Saving…' : 'Save' }}</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete confirm modal -->
    <div v-if="openDelete" class="fixed inset-0 z-50">
      <div class="absolute inset-0 bg-black/40" @click="closeDelete"/>
      <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg shadow-md w-[95vw] max-w-md border border-gray-200">
        <div class="px-4 py-3 border-b border-[#ecebea] font-semibold text-[#0B6B3A]">Delete School</div>
        <div class="p-4 text-sm text-gray-700">
          <span v-if="bulkDeleteIds.length === 0">
            Are you sure you want to delete <span class="font-semibold">{{ deleteTarget?.name }}</span>? This action cannot be undone.
          </span>
          <span v-else>
            Are you sure you want to delete <span class="font-semibold">{{ bulkDeleteIds.length }}</span> selected school<span v-if="bulkDeleteIds.length !== 1">s</span>? This action cannot be undone.
          </span>
        </div>
        <div class="px-4 py-3 border-t border-[#ecebea] flex justify-end gap-3">
          <button class="px-4 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-100" @click="closeDelete">Cancel</button>
          <button class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700" :disabled="deleting" @click="confirmDelete">{{ deleting ? 'Deleting…' : 'Delete' }}</button>
        </div>
      </div>
    </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'

const regions = ref([])
const councils = ref([])
const schools = ref([])
const filters = ref({ region: '', district: '' })
const loading = ref(true)
const search = ref('')
const selectedIds = ref([])

const openAdd = ref(false)
const addForm = ref({ region_id: '', district_id: '', name: '', code: '', level: 'secondary' })
const addDistricts = ref([])
const submittingAdd = ref(false)
const errorAdd = ref('')

const openBulk = ref(false)
const bulk = ref({ region_id: '', district_id: '' })
const bulkDistricts = ref([])
const fileInput = ref(null)
const submittingBulk = ref(false)
const errorBulk = ref('')
const bulkPreviewRows = ref([])
const bulkPreviewReady = ref(false)

const openEdit = ref(false)
const editForm = ref({ id:'', name:'', code:'', level:'secondary', region_id:'', district_id:'' })
const editDistricts = ref([])
const submittingEdit = ref(false)
const errorEdit = ref('')

const openDelete = ref(false)
const deleteTarget = ref(null)
const deleting = ref(false)
const bulkDeleteIds = ref([])

async function fetchRegions(){
  const res = await fetch('/api/admin/regions', { headers: { 'Accept': 'application/json' } })
  if (res.ok) regions.value = await res.json()
}

async function fetchCouncils(){
  const params = new URLSearchParams()
  if (filters.value.region) params.set('region_id', filters.value.region)
  const res = await fetch(`/api/admin/councils?${params.toString()}`)
  councils.value = res.ok ? await res.json() : []
}

async function fetchSchools(){
  loading.value = true
  try{
    const params = new URLSearchParams()
    if (filters.value.region) params.set('region_id', filters.value.region)
    if (filters.value.district) params.set('district_id', filters.value.district)
    const res = await fetch(`/api/admin/schools?${params.toString()}`)
    schools.value = res.ok ? await res.json() : []
  } finally { loading.value = false }
}

const filteredSchools = computed(()=>{
  const q = search.value.trim().toLowerCase()
  if (!q) return schools.value
  return schools.value.filter(s => (s.name || '').toLowerCase().includes(q) || (s.code||'').toLowerCase().includes(q))
})

const allVisibleSelected = computed(()=>{
  if (!filteredSchools.value.length) return false
  return filteredSchools.value.every(s => selectedIds.value.includes(s.id))
})

async function onRegionChange(){
  filters.value.district = ''
  await fetchCouncils()
  await fetchSchools()
}

function closeAdd(){ openAdd.value = false; addForm.value = { region_id: filters.value.region || '', district_id: filters.value.district || '', name: '', code: '', level: 'secondary' }; addDistricts.value = []; errorAdd.value='' }
function closeBulk(){ openBulk.value = false; bulk.value = { region_id: filters.value.region || '', district_id: filters.value.district || '' }; bulkDistricts.value = []; errorBulk.value=''; bulkPreviewRows.value = []; bulkPreviewReady.value = false; if (fileInput.value) fileInput.value.value = '' }

async function loadAddDistricts(){
  addDistricts.value = []
  if (!addForm.value.region_id) return
  const res = await fetch(`/api/admin/councils?region_id=${addForm.value.region_id}`)
  addDistricts.value = res.ok ? await res.json() : []
}
async function loadBulkDistricts(){
  bulkDistricts.value = []
  if (!bulk.value.region_id) return
  const res = await fetch(`/api/admin/councils?region_id=${bulk.value.region_id}`)
  bulkDistricts.value = res.ok ? await res.json() : []
}

async function loadEditDistricts(){
  editDistricts.value = []
  if (!editForm.value.region_id) return
  const res = await fetch(`/api/admin/councils?region_id=${editForm.value.region_id}`)
  editDistricts.value = res.ok ? await res.json() : []
}

async function createSchool(){
  submittingAdd.value = true; errorAdd.value=''
  try{
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    const res = await fetch('/api/admin/schools', { method:'POST', headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify({
      district_id: addForm.value.district_id,
      name: addForm.value.name,
      code: addForm.value.code || null,
      level: addForm.value.level,
    })})
    if(res.status === 422){ const d = await res.json(); errorAdd.value = d?.message || 'Validation error'; return }
    if(!res.ok) throw new Error('save failed')
    const created = await res.json()
    // if filters match, prepend
    if ((!filters.value.region || String(created.region_id) === String(filters.value.region)) &&
        (!filters.value.district || String(created.district_id) === String(filters.value.district))) {
      schools.value = [created, ...schools.value]
    }
    closeAdd()
  } catch(e){ errorAdd.value='Failed to save' } finally { submittingAdd.value=false }
}

function parseCsvPreview(text){
  const lines = text.split(/\r?\n/).filter(l => l.trim() !== '')
  if (!lines.length) return []
  const headerParts = lines[0].split(',').map(h => h.trim().toLowerCase())
  let hasHeader = headerParts.includes('name') || headerParts.includes('code') || headerParts.includes('level')
  const rows = []
  const startIdx = hasHeader ? 1 : 0
  for (let i = startIdx; i < lines.length; i++) {
    const cols = lines[i].split(',')
    let name = ''
    let code = ''
    let level = 'secondary'
    if (hasHeader) {
      const nameIdx = headerParts.indexOf('name')
      const codeIdx = headerParts.indexOf('code')
      const levelIdx = headerParts.indexOf('level')
      if (nameIdx !== -1) name = (cols[nameIdx] || '').trim()
      if (codeIdx !== -1) code = (cols[codeIdx] || '').trim()
      if (levelIdx !== -1) {
        const lvl = (cols[levelIdx] || '').trim().toLowerCase()
        if (['primary','secondary','other'].includes(lvl)) level = lvl
      }
    } else {
      name = (cols[0] || '').trim()
      code = (cols[1] || '').trim()
      const lvl = (cols[2] || '').trim().toLowerCase()
      if (['primary','secondary','other'].includes(lvl)) level = lvl
    }
    if (!name) continue
    rows.push({ name, code, level })
  }
  return rows
}

async function prepareBulkPreview(){
  errorBulk.value=''; bulkPreviewRows.value = []; bulkPreviewReady.value = false
  const file = fileInput.value?.files?.[0]
  if(!file){ errorBulk.value='Please choose a CSV file'; return }
  try{
    const text = await file.text()
    const rows = parseCsvPreview(text)
    if (!rows.length){ errorBulk.value = 'No valid rows found in CSV'; return }
    bulkPreviewRows.value = rows
    bulkPreviewReady.value = true
  } catch(e){ errorBulk.value='Failed to read CSV file'; }
}

async function uploadBulk(){
  if (!bulkPreviewReady.value){ errorBulk.value = 'Please generate a preview first.'; return }
  submittingBulk.value = true; errorBulk.value=''
  try{
    const file = fileInput.value?.files?.[0]
    if(!file){ errorBulk.value='Please choose a CSV file'; return }
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    const fd = new FormData(); fd.append('district_id', bulk.value.district_id); fd.append('file', file)
    const res = await fetch('/api/admin/schools/bulk', { method:'POST', headers:{ 'X-CSRF-TOKEN': csrf }, body: fd })
    if(res.status === 422){ const d = await res.json(); errorBulk.value = d?.message || 'Validation error'; return }
    if(!res.ok) throw new Error('upload failed')
    await fetchSchools()
    closeBulk()
  } catch(e){ errorBulk.value='Failed to upload'; } finally { submittingBulk.value=false }
}

onMounted(async()=>{
  await fetchRegions()
  await fetchCouncils()
  await fetchSchools()
  // default prefill in dialogs
  addForm.value.region_id = filters.value.region
  addForm.value.district_id = filters.value.district
  bulk.value.region_id = filters.value.region
  bulk.value.district_id = filters.value.district
})

function toggleSelectAll(){
  if (allVisibleSelected.value) {
    // unselect all visible
    const visibleIds = filteredSchools.value.map(s => s.id)
    selectedIds.value = selectedIds.value.filter(id => !visibleIds.includes(id))
  } else {
    const visibleIds = filteredSchools.value.map(s => s.id)
    const set = new Set(selectedIds.value.concat(visibleIds))
    selectedIds.value = Array.from(set)
  }
}

function openEditFor(s){
  editForm.value = {
    id: String(s.id),
    name: s.name,
    code: s.code || '',
    level: s.level || 'secondary',
    region_id: String(s.region_id),
    district_id: String(s.district_id),
  }
  loadEditDistricts()
  openEdit.value = true
}
function closeEdit(){ openEdit.value = false; editForm.value = { id:'', name:'', code:'', level:'secondary', region_id:'', district_id:'' }; errorEdit.value='' }

async function updateSchool(){
  submittingEdit.value = true; errorEdit.value=''
  try{
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    const res = await fetch(`/api/admin/schools/${editForm.value.id}`, { method:'POST', headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify({
      district_id: editForm.value.district_id,
      name: editForm.value.name,
      code: editForm.value.code || null,
      level: editForm.value.level,
    })})
    if(res.status === 422){ const d = await res.json(); errorEdit.value = d?.message || 'Validation error'; return }
    if(!res.ok) throw new Error('update failed')
    const updated = await res.json()
    schools.value = schools.value.map(x => x.id === updated.id ? updated : x)
    closeEdit()
  } catch(e){ errorEdit.value='Failed to update' } finally { submittingEdit.value=false }
}

function openDeleteFor(s){ bulkDeleteIds.value = []; deleteTarget.value = s; openDelete.value = true }
function openBulkDelete(){
  if (!selectedIds.value.length) return
  deleteTarget.value = null
  bulkDeleteIds.value = [...selectedIds.value]
  openDelete.value = true
}
function closeDelete(){ openDelete.value = false; deleteTarget.value = null; bulkDeleteIds.value = [] }
async function confirmDelete(){
  deleting.value = true
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
  try{
    if (bulkDeleteIds.value.length) {
      const ids = [...bulkDeleteIds.value]
      for (const id of ids) {
        const res = await fetch(`/api/admin/schools/${id}`, { method:'DELETE', headers: { 'X-CSRF-TOKEN': csrf } })
        if (res.ok) {
          schools.value = schools.value.filter(x => x.id !== id)
        }
      }
      selectedIds.value = selectedIds.value.filter(id => !bulkDeleteIds.value.includes(id))
    } else if (deleteTarget.value) {
      const res = await fetch(`/api/admin/schools/${deleteTarget.value.id}`, { method:'DELETE', headers: { 'X-CSRF-TOKEN': csrf } })
      if(!res.ok) throw new Error('delete failed')
      schools.value = schools.value.filter(x => x.id !== deleteTarget.value.id)
      selectedIds.value = selectedIds.value.filter(id => id !== deleteTarget.value.id)
    }
    closeDelete()
  } catch(e){ /* could show toast */ } finally { deleting.value = false }
}
</script>
