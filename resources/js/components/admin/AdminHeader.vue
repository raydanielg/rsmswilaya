<template>
  <header class="bg-[#0A5F34] text-white border-b border-black/10">
    <div class="px-4 md:px-6 h-14 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <button class="md:hidden inline-flex items-center justify-center h-9 w-9 rounded hover:bg-white/10" @click="$emit('toggle-sidebar')" aria-label="Toggle sidebar">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <a href="/dashboard" class="flex items-center font-semibold">
          <span>RSMS Admin</span>
        </a>
      </div>
      <div class="flex items-center gap-3 relative">
        <a href="/" class="hidden md:inline-flex text-sm px-3 py-1.5 rounded bg-[#0AA74A] hover:bg-[#089543]">View site</a>
        <button class="inline-flex items-center gap-2 text-sm px-3 py-1.5 rounded border border-white/20 hover:bg-white/10" @click="toggleUser" @keydown.escape="openUser=false" aria-haspopup="menu" :aria-expanded="openUser ? 'true' : 'false'">
          <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/20">{{ initials }}</span>
          <span class="hidden sm:inline">{{ adminName }}</span>
          <svg class="h-3 w-3" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5"/></svg>
        </button>
        <ul v-show="openUser" class="absolute right-0 top-full mt-2 bg-white text-[#0B6B3A] rounded shadow-lg min-w-[180px] overflow-hidden z-50">
          <li><a class="block px-4 py-2 hover:bg-[#E6FF8A]" href="#">Profile</a></li>
          <li><a class="block px-4 py-2 hover:bg-[#E6FF8A]" href="#">Settings</a></li>
          <li>
            <form method="POST" action="/logout">
              <input type="hidden" name="_token" :value="csrf" />
              <button type="submit" class="w-full text-left block px-4 py-2 hover:bg-[#E6FF8A]">Logout</button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </header>
</template>
<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
const adminName = (document.querySelector('meta[name="admin-name"]')?.getAttribute('content')) || 'Administrator'
const initials = computed(() => adminName.split(' ').map(p=>p[0]).join('').slice(0,2).toUpperCase())
const openUser = ref(false)
function toggleUser(){ openUser.value = !openUser.value }
function onClickOutside(e){
  const headerEl = document.querySelector('header')
  if (headerEl && !headerEl.contains(e.target)) openUser.value = false
}
onMounted(()=> document.addEventListener('click', onClickOutside))
onBeforeUnmount(()=> document.removeEventListener('click', onClickOutside))
</script>
