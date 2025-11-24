<template>
  <div class="min-h-screen bg-[#F7F7F6] text-[#1b1b18] flex flex-col">
    <header class="bg-[#0B6B3A] text-white">
      <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
        <div class="font-semibold tracking-wide">RSMS</div>
        <div class="text-xs opacity-80">System Status</div>
      </div>
    </header>

    <main class="flex-1 flex items-center justify-center px-4 py-10">
      <div class="w-full max-w-xl bg-white border rounded-lg shadow-sm p-6 text-center">
        <div class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-amber-100 text-amber-700 border border-amber-200 mb-3">
          ⚙️
        </div>
        <h1 class="text-2xl font-semibold mb-2">We’re doing maintenance</h1>
        <p class="text-sm text-gray-600 mb-6">Our website is under scheduled maintenance to improve your experience. We’ll be back shortly.</p>

        <div class="grid grid-cols-4 gap-3 mb-6">
          <div class="bg-[#F7F7F6] rounded-md border p-3">
            <div class="text-2xl font-bold">{{ timeLeft.days }}</div>
            <div class="text-xs text-gray-600">Days</div>
          </div>
          <div class="bg-[#F7F7F6] rounded-md border p-3">
            <div class="text-2xl font-bold">{{ timeLeft.hours }}</div>
            <div class="text-xs text-gray-600">Hours</div>
          </div>
          <div class="bg-[#F7F7F6] rounded-md border p-3">
            <div class="text-2xl font-bold">{{ timeLeft.minutes }}</div>
            <div class="text-xs text-gray-600">Minutes</div>
          </div>
          <div class="bg-[#F7F7F6] rounded-md border p-3">
            <div class="text-2xl font-bold">{{ timeLeft.seconds }}</div>
            <div class="text-xs text-gray-600">Seconds</div>
          </div>
        </div>

        <div class="text-xs text-gray-600">Planned end: <strong>{{ endDisplay }}</strong></div>
      </div>
    </main>

    <footer class="border-t bg-white text-xs text-gray-500">
      <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
        <span>© {{ new Date().getFullYear() }} RSMS</span>
        <span>Built with ❤</span>
      </div>
    </footer>
  </div>
</template>

<script>
export default {
  name: 'Maintenance',
  props: {
    // ISO date string like '2025-12-31T23:59:59Z'
    endsAt: { type: String, default: '' }
  },
  data(){
    return { now: Date.now(), timer: null };
  },
  computed: {
    endTs(){
      const el = this.endsAt || document.querySelector('meta[name="maintenance-ends"]')?.getAttribute('content') || '';
      const t = new Date(el).getTime();
      return isNaN(t) ? Date.now() + 2*60*60*1000 : t; // default +2h
    },
    diff(){
      return Math.max(0, this.endTs - this.now);
    },
    timeLeft(){
      const s = Math.floor(this.diff/1000);
      const days = Math.floor(s/86400);
      const hours = Math.floor((s%86400)/3600);
      const minutes = Math.floor((s%3600)/60);
      const seconds = s%60;
      return { days, hours, minutes, seconds };
    },
    endDisplay(){
      try { return new Date(this.endTs).toLocaleString(); } catch { return ''; }
    }
  },
  mounted(){
    this.timer = setInterval(()=>{ this.now = Date.now(); }, 1000);
  },
  unmounted(){ clearInterval(this.timer); }
}
</script>
