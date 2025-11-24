<template>
  <footer class="w-full bg-[#1b1b1b] text-[#EDEDEC] pt-10">
    <div class="w-full px-4 md:px-8">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 pb-10">
        <!-- Brand -->
        <div>
          <h4 class="text-xl font-semibold tracking-wide text-white">RSMS — Results Management System</h4>
          <p class="mt-3 text-sm text-[#bdbdbb]">
            Regional & District examinations management. From Primary to Secondary levels
            with school dashboards and mobile access.
          </p>
          <div class="mt-4 space-y-2 text-sm">
            <p>P.O. Box 000, Dar es Salaam</p>
            <p>P.O. Box 000, Dodoma</p>
            <p>P.O. Box 000, Zanzibar</p>
            <p>
              <span class="text-white font-medium">Phone:</span> 0712 000 000
              <span class="ml-4 text-white font-medium">Email:</span> info@rsms.go.tz
            </p>
          </div>
        </div>

        <!-- Useful Links -->
        <div>
          <h5 class="text-lg font-semibold text-white">Useful Links</h5>
          <ul class="mt-3 space-y-2 text-sm">
            <li><a class="inline-flex items-center gap-2 hover:text-[#E6FF8A]" href="#"><span class="text-[#19a244]">›</span> MoEST</a></li>
            <li><a class="inline-flex items-center gap-2 hover:text-[#E6FF8A]" href="#"><span class="text-[#19a244]">›</span> MoE-Zanzibar</a></li>
            <li><a class="inline-flex items-center gap-2 hover:text-[#E6FF8A]" href="#"><span class="text-[#19a244]">›</span> PO-RALG</a></li>
            <li><a class="inline-flex items-center gap-2 hover:text-[#E6FF8A]" href="#"><span class="text-[#19a244]">›</span> National Website</a></li>
          </ul>
        </div>

        <!-- Systems -->
        <div>
          <h5 class="text-lg font-semibold text-white">Systems</h5>
          <ul class="mt-3 space-y-2 text-sm">
            <li><a class="inline-flex items-center gap-2 hover:text-[#E6FF8A]" href="#"><span class="text-[#19a244]">›</span> Staff mail</a></li>
            <li><a class="inline-flex items-center gap-2 hover:text-[#E6FF8A]" href="#"><span class="text-[#19a244]">›</span> PReM</a></li>
            <li><a class="inline-flex items-center gap-2 hover:text-[#E6FF8A]" href="#"><span class="text-[#19a244]">›</span> PReMS</a></li>
            <li><a class="inline-flex items-center gap-2 hover:text-[#E6FF8A]" href="#"><span class="text-[#19a244]">›</span> eServices</a></li>
            <li><a class="inline-flex items-center gap-2 hover:text-[#E6FF8A]" href="#"><span class="text-[#19a244]">›</span> Teachers' Registration</a></li>
            <li><a class="inline-flex items-center gap-2 hover:text-[#E6FF8A]" href="#"><span class="text-[#19a244]">›</span> ORES</a></li>
          </ul>
        </div>

        <!-- Working hours -->
        <div>
          <h5 class="text-lg font-semibold text-white">Working hours</h5>
          <ul class="mt-3 text-sm space-y-2 text-[#bdbdbb]">
            <li>Monday to Friday: 07:30 - 16:30</li>
            <li>Saturday to Sunday: Closed</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Bottom bar -->
    <div class="w-full bg-[#171717]">
      <div class="w-full px-4 md:px-8 py-4 flex items-center justify-between gap-4">
        <p class="text-xs text-[#bdbdbb]">© Copyright {{ new Date().getFullYear() }} RSMS. All Rights Reserved</p>
        <div class="flex items-center gap-2">
          <!-- Simple social icons -->
          <a href="#" class="h-8 w-8 rounded bg-[#2a2a2a] flex items-center justify-center hover:bg-[#333]" aria-label="Instagram">
            <svg class="h-4 w-4 text-[#EDEDEC]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="3" width="18" height="18" rx="5" stroke="currentColor"/><circle cx="12" cy="12" r="4" stroke="currentColor"/><circle cx="17" cy="7" r="1" fill="currentColor"/></svg>
          </a>
          <a href="#" class="h-8 w-8 rounded bg-[#2a2a2a] flex items-center justify-center hover:bg-[#333]" aria-label="Twitter">
            <svg class="h-4 w-4 text-[#EDEDEC]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M22 5.8c-.7.3-1.5.6-2.3.7.8-.5 1.4-1.2 1.7-2.2-.8.5-1.7.9-2.6 1.1A4 4 0 0 0 12 8.7c0 .3 0 .6.1.9-3.3-.2-6.2-1.7-8.1-4-.4.7-.6 1.4-.6 2.3 0 1.5.8 2.8 2 3.6-.6 0-1.3-.2-1.8-.5 0 2.2 1.6 4 3.7 4.5-.4.1-.8.2-1.2.2-.3 0-.6 0-.9-.1.6 1.8 2.3 3.1 4.3 3.2A8.1 8.1 0 0 1 2 19.4 11.4 11.4 0 0 0 8.3 21c7 0 10.9-5.8 10.9-10.9v-.5c.8-.6 1.5-1.3 2-2.1Z" fill="currentColor"/></svg>
          </a>
          <a href="#" class="h-8 w-8 rounded bg-[#2a2a2a] flex items-center justify-center hover:bg-[#333]" aria-label="Facebook">
            <svg class="h-4 w-4 text-[#EDEDEC]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 9h3V6h-3c-1.7 0-3 1.3-3 3v2H8v3h3v7h3v-7h2.5l.5-3H14V9Z" fill="currentColor"/></svg>
          </a>
          <a href="#" class="h-8 w-8 rounded bg-[#2a2a2a] flex items-center justify-center hover:bg-[#333]" aria-label="YouTube">
            <svg class="h-4 w-4 text-[#EDEDEC]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M22 12s0-3.3-.4-4.9c-.2-.8-.8-1.5-1.6-1.6C17.5 5 12 5 12 5s-5.5 0-8 .5c-.8.1-1.4.8-1.6 1.6C2 8.7 2 12 2 12s0 3.3.4 4.9c.2.8.8 1.5 1.6 1.6 2.5.5 8 .5 8 .5s5.5 0 8-.5c.8-.1 1.4-.8 1.6-1.6.4-1.6.4-4.9.4-4.9Zm-11 3.1V8.9l5.2 3.1L11 15.1Z" fill="currentColor"/></svg>
          </a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
</script>

<style scoped>
</style>
