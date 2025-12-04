<template>
  <section class="max-w-6xl mx-auto px-4 py-6">
    <div class="mb-4">
      <h1 class="text-xl font-semibold">{{ exam.toUpperCase() }} {{ year }}</h1>
      <div class="text-xs text-gray-600">Region: <strong>{{ regionLabel }}</strong> · Council: <strong>{{ districtLabel }}</strong></div>
    </div>

    <div class="bg-white border rounded p-4 mb-4">
      <div class="flex flex-col gap-3 mb-3">
        <div class="flex items-center justify-between gap-3">
          <div class="font-medium text-sm">All Schools in {{ districtLabel }}</div>
          <div class="flex items-center gap-3 text-xs">
            <input v-model.trim="q" type="text" placeholder="Search by name..." class="border rounded px-2 py-1" />
          </div>
        </div>
        <div class="border-t border-gray-200 pt-2">
          <div class="flex flex-wrap items-center gap-1 text-[11px] uppercase font-semibold tracking-wide">
            <button
              type="button"
              class="px-2.5 py-1 rounded border text-xs"
              :class="activeLetter === '' ? 'bg-[#0B6B3A] text-white border-[#0B6B3A]' : 'bg-gray-50 text-gray-800 border-gray-200 hover:bg-gray-100'"
              @click="setActiveLetter('')"
            >
              All Schools
            </button>
            <button
              v-for="letter in letters"
              :key="letter"
              type="button"
              class="px-2.5 py-1 rounded border text-xs"
              :class="activeLetter === letter ? 'bg-[#0B6B3A] text-white border-[#0B6B3A]' : 'bg-gray-50 text-gray-800 border-gray-200 hover:bg-gray-100'"
              @click="setActiveLetter(letter)"
            >
              {{ letter }}
            </button>
          </div>
        </div>
      </div>
      <div v-if="loading" class="py-6 text-center text-sm text-gray-500">Loading schools...</div>
      <div v-else-if="!filteredSchools.length" class="py-6 text-center text-sm text-gray-500">No schools match the current filters.</div>
      <div v-else id="schoolsGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 md:gap-3">
        <template v-for="s in filteredSchools" :key="s.id">
          <button
            @click="openSchool(s)"
            class="school-item w-full text-xs sm:text-[13px] px-3 py-2 rounded-full border text-center truncate transition-colors"
            :class="s.url
              ? 'bg-green-50 border-green-300 text-green-900 hover:bg-green-100'
              : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50'"
            :title="s.name"
            :data-name="s.name.toLowerCase()"
          >
            {{ s.name }}
          </button>
        </template>
      </div>
    </div>

    <div v-if="!listOnly && Object.keys(types).length" class="space-y-3">
      <details v-for="(group, code) in types" :key="code" class="group bg-white border rounded">
        <summary class="flex items-center justify-between px-3 py-2 cursor-pointer select-none">
          <div class="flex items-center gap-2">
            <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-[#0B6B3A] text-white text-[10px]">{{ indexOf(code) }}</span>
            <span class="font-medium text-sm">{{ group.name }} <span class="text-xs text-gray-500">({{ code }})</span></span>
          </div>
          <svg class="h-4 w-4 text-gray-500 transition-transform group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
        </summary>
        <div class="px-3 pb-3">
          <div class="overflow-x-auto border rounded">
            <table class="min-w-full text-sm">
              <thead>
                <tr class="bg-gray-50 text-left">
                  <th class="px-2 py-2 border-b">#</th>
                  <th class="px-2 py-2 border-b">School</th>
                  <th class="px-2 py-2 border-b">Document</th>
                  <th class="px-2 py-2 border-b">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y">
                <tr v-for="(d, i) in group.docs" :key="d.school_id" class="hover:bg-gray-50">
                  <td class="px-2 py-2 text-gray-600">{{ i+1 }}</td>
                  <td class="px-2 py-2">{{ d.school_name }}</td>
                  <td class="px-2 py-2">
                    <a :href="viewerUrl(d.url)" target="_blank" class="text-[#0B6B3A] hover:underline">View</a>
                  </td>
                  <td class="px-2 py-2">
                    <span v-if="isNew(d.created_at)" class="inline-flex items-center px-2 py-0.5 text-[11px] rounded-full bg-blue-50 text-blue-700 border border-blue-200">NEW</span>
                    <span v-else class="text-[11px] text-gray-500">Updated {{ fromNow(d.created_at) }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </details>
    </div>
    <div v-else-if="!listOnly" class="bg-white border rounded p-5 text-gray-600">No published results yet for this council.</div>
  </section>
  <!-- Modal: School documents -->
  <div v-if="showModal" class="fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/40" @click="closeModal"></div>
    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg shadow-md w-[95vw] max-w-2xl border border-gray-200">
      <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
        <div class="font-semibold text-[#0B6B3A]">{{ modalSchoolName }}</div>
        <button class="text-[#6b6a67] hover:text-[#0B6B3A]" @click="closeModal">✕</button>
      </div>
      <div class="p-4">
        <div v-if="modalLoading" class="text-sm text-gray-500">Loading documents...</div>
        <div v-else-if="!Object.keys(modalTypes).length" class="text-sm text-gray-500">No documents yet for this school.</div>
        <div v-else class="space-y-3">
          <details v-for="(group, code) in modalTypes" :key="code" class="group border rounded">
            <summary class="flex items-center justify-between px-3 py-2 cursor-pointer select-none">
              <div class="font-medium text-sm">{{ group.name }} <span class="text-xs text-gray-500">({{ code }})</span></div>
              <svg class="h-4 w-4 text-gray-500 transition-transform group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
            </summary>
            <div class="px-3 pb-3">
              <ul class="space-y-2 text-sm">
                <li v-for="(d, idx) in group.docs" :key="idx" class="flex items-center justify-between">
                  <div class="text-gray-700">Document {{ idx+1 }}</div>
                  <div class="flex items-center gap-3">
                    <a :href="viewerUrl(d.url)" target="_blank" class="text-[#0B6B3A] hover:underline">View</a>
                  </div>
                </li>
              </ul>
            </div>
          </details>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ResultsDistrict',
  props: {
    exam: { type: String, required: true },
    year: { type: [String, Number], required: true },
    region: { type: String, required: true },
    district: { type: String, required: true },
    regionLabel: { type: String, required: true },
    districtLabel: { type: String, required: true },
    listOnly: { type: Boolean, default: false },
  },
  data(){
    return {
      loading: true,
      q: '',
      schools: [],
      types: {},
      showModal: false,
      modalSchoolId: null,
      modalSchoolName: '',
      modalTypes: {},
      modalLoading: false,
      activeLetter: '',
      letters: ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'],
    };
  },
  computed: {
    filteredSchools(){
      let list = this.schools;
      const v = this.q.trim().toLowerCase();
      if (v){
        list = list.filter(s => (s.name || '').toLowerCase().includes(v));
      }
      if (this.activeLetter){
        const letter = this.activeLetter.toLowerCase();
        list = list.filter(s => (s.name || '').toLowerCase().startsWith(letter));
      }
      return list;
    }
  },
  methods: {
    setActiveLetter(letter){
      this.activeLetter = letter || '';
    },
    indexOf(code){
      return Object.keys(this.types).indexOf(code) + 1;
    },
    isNew(dt){
      const d = new Date(dt);
      const TEN_DAYS = 10*24*60*60*1000;
      return (Date.now() - d.getTime()) < TEN_DAYS;
    },
    fromNow(dt){
      const d = new Date(dt);
      const diff = Math.floor((Date.now() - d.getTime())/1000);
      if (diff < 60) return `${diff}s ago`;
      if (diff < 3600) return `${Math.floor(diff/60)}m ago`;
      if (diff < 86400) return `${Math.floor(diff/3600)}h ago`;
      return `${Math.floor(diff/86400)}d ago`;
    },
    async load(){
      this.loading = true;
      const params = new URLSearchParams({ exam: this.exam, year: this.year, region: this.region, district: this.district });
      const res = await fetch(`/api/results/district-schools?${params.toString()}`);
      if (res.ok){
        const data = await res.json();
        this.schools = data.schools || [];
        this.types = data.types || {};
      }
      this.loading = false;
    },
    async openSchool(s){
      this.modalSchoolId = s.id;
      this.modalSchoolName = s.name;
      this.showModal = true; this.modalLoading = true; this.modalTypes = {};
      const params = new URLSearchParams({ exam: this.exam, year: this.year, region: this.region, district: this.district, school_id: s.id });
      const res = await fetch(`/api/results/school-docs?${params.toString()}`);
      if (res.ok){
        const data = await res.json();
        this.modalTypes = data.types || {};
      }
      this.modalLoading = false;
    },
    closeModal(){ this.showModal = false; }
  },
  mounted(){ this.load(); }
}
</script>
