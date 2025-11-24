<template>
  <section class="bg-white">
    <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">
      <div class="max-w-screen-md mb-8 lg:mb-16">
        <h2 class="mb-4 text-3xl md:text-4xl tracking-tight font-extrabold text-[#1b1b18]">Mfumo uliobuniwa kwa mahitaji ya RSMS</h2>
        <p class="text-[#6b6a67] sm:text-lg">RSMS inaangazia mitihani ya mikoa na wilaya, ngazi zote kuanzia msingi hadi sekondari, na upatikanaji wa taarifa kupitia dashboards na programu ya simu.</p>
      </div>
      <div class="space-y-8 md:grid md:grid-cols-2 lg:grid-cols-3 md:gap-12 md:space-y-0">
        <div v-for="(f, i) in features" :key="i">
          <div class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-primary-100 lg:h-12 lg:w-12">
            <svg v-if="f.icon==='gov'" class="w-5 h-5 text-primary-600 lg:w-6 lg:h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 10h18M5 10V8l7-4 7 4v2M6 10v8M10 10v8M14 10v8M18 10v8M4 18h16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            <svg v-else-if="f.icon==='levels'" class="w-5 h-5 text-primary-600 lg:w-6 lg:h-6" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M4 17h6v3H4v-3Zm5-7h6v10H9V10Zm5-5h6v15h-6V5Z"/></svg>
            <svg v-else-if="f.icon==='mobile'" class="w-5 h-5 text-primary-600 lg:w-6 lg:h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="7" y="2" width="10" height="20" rx="2" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="18" r="1" fill="currentColor"/></svg>
            <svg v-else-if="f.icon==='analytics'" class="w-5 h-5 text-primary-600 lg:w-6 lg:h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 20V10m5 10V6m5 14V12m5 8V4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            <svg v-else-if="f.icon==='register'" class="w-5 h-5 text-primary-600 lg:w-6 lg:h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 5h10l4 4v10a2 2 0 0 1-2 2H4V5Z" stroke="currentColor" stroke-width="1.5"/><path d="M8 13h8M8 9h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            <svg v-else-if="f.icon==='guide'" class="w-5 h-5 text-primary-600 lg:w-6 lg:h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 4h9a3 3 0 0 1 3 3v13H9a3 3 0 0 0-3 3V4Z" stroke="currentColor" stroke-width="1.5"/><path d="M10 8h6M10 12h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
          </div>
          <h3 class="mb-2 text-xl font-bold text-[#1b1b18]">{{ f.title }}</h3>
          <p class="text-[#6b6a67]">{{ f.description }}</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
const features = [
  {
    title: 'Mitihani ya Mkoa & Wilaya',
    description: 'Mfumo unasaidia kusimamia mitihani ya ndani ya mikoa na wilaya (region & district exams).',
    icon: 'gov'
  },
  {
    title: 'Ngazi: Msingi hadi Sekondari',
    description: 'Unashughulikia ngazi zote kuanzia Shule ya Msingi hadi Sekondari bila kukosea.',
    icon: 'levels'
  },
  {
    title: 'Mobile App ipo',
    description: 'Programu ya simu kuwezesha wanafunzi, wazazi na walimu kupata taarifa kwa haraka.',
    icon: 'mobile'
  },
  {
    title: 'Matokeo ya Shule & Taarifa',
    description: 'Dashboards za shule kuona matokeo, takwimu na mwenendo (analytics) kwa wakati.',
    icon: 'analytics'
  },
  {
    title: 'Usajili wa Watahiniwa',
    description: 'Usajili wa watahiniwa wa shule na binafsi kwa mitihani ya ndani ya mkoa/wilaya.',
    icon: 'register'
  },
  {
    title: 'Mwongozo & Miundo ya Mitihani',
    description: 'Miongozo ya maandalizi na formats za mitihani kwa masomo yote.',
    icon: 'guide'
  }
]
</script>

<style scoped>
</style>
