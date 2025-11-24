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
        </div>
        <div class="flex items-center gap-3">
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
              <th class="px-4 py-2">School</th>
              <th class="px-4 py-2">Code</th>
              <th class="px-4 py-2">Council</th>
              <th class="px-4 py-2">Region</th>
              <th class="px-4 py-2 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td class="px-4 py-3 text-sm text-gray-500" colspan="5">Loading schools…</td>
            </tr>
            <tr v-for="(s, idx) in filteredSchools" :key="s.id" :class="[idx % 2 ? 'bg-gray-50' : 'bg-white', 'hover:bg-gray-100']">
              <td class="px-4 py-2 text-gray-900 font-medium">{{ s.name }}</td>
              <td class="px-4 py-2 text-sm">{{ s.code || '—' }}</td>
              <td class="px-4 py-2 text-sm">{{ s.district_name }}</td>
              <td class="px-4 py-2 text-sm">{{ s.region_name }}</td>
              <td class="px-4 py-2 text-right">
                <button class="text-sm px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 mr-2" @click="openEditFor(s)">Edit</button>
                <button class="text-sm px-3 py-1.5 rounded-lg bg-red-600 text-white hover:bg-red-700" @click="openDeleteFor(s)">Delete</button>
              </td>
            </tr>
            <tr v-if="!loading && filteredSchools.length === 0">
              <td class="px-4 py-6 text-sm text-gray-500" colspan="5">
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
            <p class="text-xs text-gray-500 mt-1">CSV columns: name, code (header row supported)</p>
            <a href="/admin/schools/template" class="text-xs text-primary-700 hover:underline">Download template</a>
            <div v-if="errorBulk" class="text-sm text-red-600 mt-1">{{ errorBulk }}</div>
          </div>
          <div class="flex justify-end gap-3">
            <button type="button" class="px-4 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-100" @click="closeBulk">Cancel</button>
            <button :disabled="submittingBulk" class="px-4 py-2 rounded-lg text-white bg-primary-700 hover:bg-primary-800 disabled:opacity-60">{{ submittingBulk ? 'Uploading…' : 'Upload' }}</button>
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
        <div class="p-4 text-sm text-gray-700">Are you sure you want to delete <span class="font-semibold">{{ deleteTarget?.name }}</span>? This action cannot be undone.</div>
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

const openAdd = ref(false)
const addForm = ref({ region_id: '', district_id: '', name: '', code: '' })
const addDistricts = ref([])
const submittingAdd = ref(false)
const errorAdd = ref('')

const openBulk = ref(false)
const bulk = ref({ region_id: '', district_id: '' })
const bulkDistricts = ref([])
const fileInput = ref(null)
const submittingBulk = ref(false)
const errorBulk = ref('')

const openEdit = ref(false)
const editForm = ref({ id:'', name:'', code:'', region_id:'', district_id:'' })
const editDistricts = ref([])
const submittingEdit = ref(false)
const errorEdit = ref('')

const openDelete = ref(false)
const deleteTarget = ref(null)
const deleting = ref(false)

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

async function onRegionChange(){
  filters.value.district = ''
  await fetchCouncils()
  await fetchSchools()
}

function closeAdd(){ openAdd.value = false; addForm.value = { region_id: filters.value.region || '', district_id: filters.value.district || '', name: '', code: '' }; addDistricts.value = []; errorAdd.value='' }
function closeBulk(){ openBulk.value = false; bulk.value = { region_id: filters.value.region || '', district_id: filters.value.district || '' }; bulkDistricts.value = []; errorBulk.value='' }

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

async function uploadBulk(){
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
  } catch(e){ errorBulk.value='Failed to upload' } finally { submittingBulk.value=false }
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

function openEditFor(s){
  editForm.value = {
    id: String(s.id),
    name: s.name,
    code: s.code || '',
    region_id: String(s.region_id),
    district_id: String(s.district_id),
  }
  loadEditDistricts()
  openEdit.value = true
}
function closeEdit(){ openEdit.value = false; editForm.value = { id:'', name:'', code:'', region_id:'', district_id:'' }; errorEdit.value='' }

async function updateSchool(){
  submittingEdit.value = true; errorEdit.value=''
  try{
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    const res = await fetch(`/api/admin/schools/${editForm.value.id}`, { method:'POST', headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify({
      district_id: editForm.value.district_id,
      name: editForm.value.name,
      code: editForm.value.code || null,
    })})
    if(res.status === 422){ const d = await res.json(); errorEdit.value = d?.message || 'Validation error'; return }
    if(!res.ok) throw new Error('update failed')
    const updated = await res.json()
    schools.value = schools.value.map(x => x.id === updated.id ? updated : x)
    closeEdit()
  } catch(e){ errorEdit.value='Failed to update' } finally { submittingEdit.value=false }
}

function openDeleteFor(s){ deleteTarget.value = s; openDelete.value = true }
function closeDelete(){ openDelete.value = false; deleteTarget.value = null }
async function confirmDelete(){
  if (!deleteTarget.value) return
  deleting.value = true
  try{
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    const res = await fetch(`/api/admin/schools/${deleteTarget.value.id}`, { method:'DELETE', headers: { 'X-CSRF-TOKEN': csrf } })
    if(!res.ok) throw new Error('delete failed')
    schools.value = schools.value.filter(x => x.id !== deleteTarget.value.id)
    closeDelete()
  } catch(e){ /* could show toast */ } finally { deleting.value = false }
}
</script>
