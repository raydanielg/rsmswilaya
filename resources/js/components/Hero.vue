<template>
  <section
    class="relative w-full text-white"
    :style="bgStyle"
  >
    <div class="absolute inset-0 bg-gradient-to-r from-[#0a5f34]/85 via-[#0b6b3a]/70 to-[#0aa74a]/60"></div>
    <div class="absolute inset-0" style="backdrop-filter: saturate(110%);"></div>

    <div class="relative w-full px-4 md:px-8 py-16 lg:py-24">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
        <div class="lg:col-span-7">
          <h1 class="text-4xl md:text-6xl font-extrabold leading-tight">
            RSMS — Mfumo bunifu wa matokeo na mitihani
          </h1>
          <p class="mt-4 max-w-2xl text-white/90 text-lg md:text-xl">
            Unaunganisha mikoa na wilaya kusimamia mitihani ya ndani, kutoka Shule ya Msingi hadi Sekondari.
            Dashboards za takwimu, usajili rahisi na programu ya simu kwa upatikanaji wa taarifa kwa wakati.
          </p>
          <div class="mt-8 flex flex-wrap gap-3">
            <a href="/about" class="inline-flex items-center px-6 py-3 rounded-md bg-white text-[#0b6b3a] font-semibold hover:bg-[#E6FF8A]">
              About Us
            </a>
            <a href="/#results" class="inline-flex items-center px-6 py-3 rounded-md bg-transparent border border-white/80 hover:bg-white/10">
              Results
            </a>
          </div>
          <div class="mt-4">
            <a href="#" class="inline-flex items-center gap-2 px-6 py-3 rounded-md bg-[#2a2a2a]/80 text-white hover:bg-[#333]">
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 3h10a2 2 0 0 1 2 2v14l-5-3-5 3V5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.5"/></svg>
              Download Mobile App
            </a>
          </div>
        </div>

        <div class="lg:col-span-5 hidden lg:block"></div>
      </div>
    </div>
  </section>
</template>

<script setup>
const bgStyle = {
  backgroundImage:
    `linear-gradient(0deg, rgba(0,0,0,0.0) 0%, rgba(0,0,0,0.0) 100%),
     url('/assets/images/PXL_20250502_075435578.RAW-01.COVER.jpg'),
     url('/assets/images/PXL_20250502_075450907.RAW-01.COVER.jpg'),
     url('/assets/images/WhatsApp Image 2025-05-01 at 23.00.37_5f963f9d.jpg'),
     url('/assets/images/PXL_20250429_092433483.MP.jpg')`,
  backgroundSize: 'cover, cover, cover, cover, cover',
  backgroundPosition: 'center, center, center, center, center',
  backgroundAttachment: 'fixed, fixed, fixed, fixed, fixed',
  mixBlendMode: 'normal',
}
</script>

<style scoped>
/* Ensure minimum height so hero fills viewport nicely */
section { min-height: 70vh; }
</style>
