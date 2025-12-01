<template>
  <aside :class="['bg-[#06492A] text-white w-64 shrink-0 border-r border-black/10', open ? 'block' : 'hidden md:block']">
    <nav class="py-4 space-y-1 text-sm">
      <div class="px-4 mb-3 text-[11px] font-semibold tracking-[0.15em] text-white/60">NAVIGATION</div>

      <a href="/dashboard" class="flex items-center gap-3 px-4 py-2 rounded-r-full hover:bg-white/10" :class="{ 'bg-white/10': active === 'dashboard' }">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M10.25 3.5a.75.75 0 01.75-.75h2a.75.75 0 01.75.75V10a.75.75 0 01-.75.75h-2A.75.75 0 0110.25 10V3.5zM3 10.25A.75.75 0 013.75 9.5H8a.75.75 0 01.75.75V20a.75.75 0 01-.75.75H3.75A.75.75 0 013 20V10.25zM15.25 13A.75.75 0 0116 12.25h4.25a.75.75 0 01.75.75V20a.75.75 0 01-.75.75H16A.75.75 0 0115.25 20v-7z"/></svg>
        <span class="flex-1">Dashboard</span>
      </a>

      <div class="mt-4 px-4 text-[11px] font-semibold tracking-[0.15em] text-white/60">CONTENT</div>

      <a href="/admin/events" class="flex items-center gap-3 px-4 py-2 rounded-r-full hover:bg-white/10" :class="{ 'bg-white/10': active === 'events' }">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 6V3M16 6V3M4 9h16M6 21h12a2 2 0 002-2v-8H4v8a2 2 0 002 2z"/></svg>
        <span class="flex-1">Events</span>
      </a>

      <a href="/admin/publications" class="flex items-center gap-3 px-4 py-2 rounded-r-full hover:bg-white/10" :class="{ 'bg-white/10': active === 'publications' }">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M2 8a2 2 0 012-2h5l2 2h9a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8z"/></svg>
        <span class="flex-1">Publications</span>
      </a>

      <a href="/admin/blog" class="flex items-center gap-3 px-4 py-2 rounded-r-full hover:bg-white/10" :class="{ 'bg-white/10': active === 'blogs' }">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 5h18M3 12h18M3 19h10"/></svg>
        <span class="flex-1">Blog</span>
      </a>

      <details class="group">
        <summary class="flex items-center justify-between px-4 py-2 rounded-r-full hover:bg-white/10 cursor-pointer" :class="{ 'bg-white/10': active === 'results' }">
          <span class="flex items-center gap-3">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19h16M4 15l4-4 3 3 5-5 4 4"/></svg>
            <span class="flex-1">Results</span>
          </span>
          <span class="text-xs font-semibold group-open:hidden">+</span>
          <span class="text-xs font-semibold hidden group-open:inline">-</span>
        </summary>
        <div class="mt-1 ml-11 flex flex-col text-[13px] text-white/80">
          <a href="/admin/results" class="px-3 py-1.5 rounded hover:bg-white/10">All Results</a>
          <a href="/admin/results?tab=year" class="px-3 py-1.5 rounded hover:bg-white/10">Years</a>
          <a href="/admin/results?tab=types" class="px-3 py-1.5 rounded hover:bg-white/10">Result Types</a>
        </div>
      </details>

      <div class="mt-4 px-4 text-[11px] font-semibold tracking-[0.15em] text-white/60">GEOGRAPHY</div>
      <a href="/admin/regions" class="flex items-center gap-3 px-4 py-2 rounded-r-full hover:bg-white/10" :class="{ 'bg-white/10': active === 'regions' }">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 7l9-4 9 4v10l-9 4-9-4V7z"/><path d="M3 7l9 4 9-4"/></svg>
        <span class="flex-1">Regions</span>
      </a>
      <a href="/admin/councils" class="flex items-center gap-3 px-4 py-2 rounded-r-full hover:bg-white/10" :class="{ 'bg-white/10': active === 'councils' }">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 21V8l8-5 8 5v13"/><path d="M9 21v-6h6v6"/></svg>
        <span class="flex-1">Councils</span>
      </a>
      <a href="/admin/schools" class="flex items-center gap-3 px-4 py-2 rounded-r-full hover:bg-white/10" :class="{ 'bg-white/10': active === 'schools' }">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 10l9-5 9 5-9 5-9-5z"/><path d="M21 10v6"/><path d="M3 10v6"/><path d="M7 13v7l5 3 5-3v-7"/></svg>
        <span class="flex-1">Schools</span>
      </a>
      <a href="/admin/notifications" class="flex items-center gap-3 px-4 py-2 rounded-r-full hover:bg-white/10" :class="{ 'bg-white/10': active === 'notifications' }">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22a2 2 0 002-2H10a2 2 0 002 2z"/><path d="M18 16v-5a6 6 0 10-12 0v5l-2 2h16l-2-2z"/></svg>
        <span class="flex-1">Notifications</span>
      </a>

      <a href="/admin/settings" class="flex items-center gap-3 px-4 py-2 rounded-r-full hover:bg-white/10" :class="{ 'bg-white/10': active === 'settings' }">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 8a4 4 0 100 8 4 4 0 000-8z"/><path d="M2 12h2m16 0h2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
        <span class="flex-1">Settings</span>
      </a>
      <div class="mt-4 px-4 text-[11px] font-semibold tracking-[0.15em] text-white/60">ACCOUNT</div>
      <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-r-full hover:bg-white/10">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 12a4 4 0 100-8 4 4 0 000 8z"/><path d="M4 20a8 8 0 0116 0"/></svg>
        <span class="flex-1">Profile</span>
      </a>
    </nav>
  </aside>
</template>
<script setup>
const props = defineProps({ open: { type: Boolean, default: true }, active: { type: String, default: 'dashboard' } })
</script>
