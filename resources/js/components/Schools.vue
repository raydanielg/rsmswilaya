<template>
  <section class="max-w-6xl mx-auto px-4 py-6">
    <nav class="mb-3 text-xs md:text-sm text-gray-600 bg-white border border-[#ecebea] rounded-md px-3 py-2 inline-flex items-center gap-2">
      <a :href="`/results/${exam}`" class="text-[#0B6B3A] hover:underline">{{ exam.toUpperCase() }}</a>
      <span>/</span>
      <a :href="`/results/${exam}/${year}`" class="text-[#0B6B3A] hover:underline">{{ year }}</a>
      <span>/</span>
      <a :href="`/results/${exam}/${year}/${region}`" class="text-[#0B6B3A] hover:underline">{{ regionLabel }}</a>
      <span>/</span>
      <span class="font-medium">{{ districtLabel }}</span>
    </nav>
    <div class="bg-white border rounded p-4 mb-4">
      <div v-if="loading" class="py-6 text-center text-sm text-gray-500">Loading schools...</div>
      <div v-else id="schoolsGrid" class="grid grid-cols-1 md:grid-cols-3 gap-2 md:gap-3">
        <template v-for="s in filteredSchools" :key="s.id">
          <button
            @click="openSchool(s)"
            class="school-item text-left block w-full text-sm md:text-base px-4 py-3 rounded-md shadow-sm transition"
            :class="s.url ? 'bg-green-50 text-green-800 hover:bg-green-100' : 'bg-gray-50 text-gray-700 hover:bg-gray-100'"
            :data-name="s.name.toLowerCase()"
          >
            {{ s.name }}
          </button>
        </template>
      </div>
    </div>

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
                      <a v-if="!isExternal(d.url)" :href="d.url" :download="filenameFromUrl(d.url)" class="text-[#0B6B3A] hover:underline">Download</a>
                      <a v-else :href="d.url" target="_blank" rel="noopener" class="text-[#0B6B3A] hover:underline">Download</a>
                    </div>
                  </li>
                </ul>
              </div>
            </details>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'Schools',
  props: {
    exam: { type: String, required: true },
    year: { type: [String, Number], required: true },
    region: { type: String, required: true },
    district: { type: String, required: true },
    regionLabel: { type: String, required: true },
    districtLabel: { type: String, required: true },
  },
  data(){
    return { loading: true, q: '', schools: [], showModal: false, modalSchoolId: null, modalSchoolName: '', modalTypes: {}, modalLoading: false, debounceId: null };
  },
  computed: {
    filteredSchools(){ return this.schools; },
    totalCount(){ return this.schools.length; },
    publishedCount(){ return this.schools.filter(s => !!s.url).length; }
  },
  methods: {
    async load(){
      this.loading = true;
      const params = new URLSearchParams({ exam: this.exam, year: this.year, region: this.region, district: this.district, q: this.q });
      const res = await fetch(`/api/results/district-schools?${params.toString()}`);
      if (res.ok){
        const data = await res.json();
        this.schools = data.schools || [];
      }
      this.loading = false;
    },
    triggerSearch(){
      clearTimeout(this.debounceId);
      this.debounceId = setTimeout(() => { this.load(); }, 300);
    },
    openSchool(s){
      const region = encodeURIComponent(this.region);
      const district = encodeURIComponent(this.district);
      const school = encodeURIComponent(String(s.name || '').toLowerCase());
      window.location.href = `/results/${this.exam}/${this.year}/${region}/${district}/${school}`;
    },
    closeModal(){ this.showModal = false; }
  },
  watch: { q(){ this.triggerSearch(); } },
  mounted(){ this.load(); }
}
</script>
