<template>
  <div class="bg-white rounded-md border border-[#ecebea] overflow-hidden">
    <div class="flex items-center justify-between p-4 border-b border-[#ecebea]">
      <div class="font-semibold">{{ headerLabel }}</div>
      <div class="flex gap-2">
        <button class="px-3 py-1 rounded border hover:bg-[#f7f7f6]" @click="goPrev">←</button>
        <button class="px-3 py-1 rounded border hover:bg-[#f7f7f6]" @click="goToday">Today</button>
        <button class="px-3 py-1 rounded border hover:bg-[#f7f7f6]" @click="goNext">→</button>
      </div>
    </div>
    <div class="relative">
      <FullCalendar ref="calendarRef" :options="calendarOptions" />
      <div v-if="loading" class="absolute inset-0 bg-white/50 backdrop-blur-[1px] flex items-center justify-center">
        <svg class="animate-spin h-6 w-6 text-[#0B6B3A]" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
        </svg>
      </div>
    </div>

    <!-- Event details modal -->
    <div v-if="showModal" class="fixed inset-0 z-50">
      <div class="absolute inset-0 bg-black/40" @click="closeModal" />
      <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-md shadow-lg w-[95vw] max-w-md border border-[#ecebea]">
        <div class="px-4 py-3 border-b border-[#ecebea] flex items-center justify-between">
          <div class="font-semibold text-[#0B6B3A]">{{ selectedEvent?.title }}</div>
          <button class="text-[#6b6a67] hover:text-[#0B6B3A]" @click="closeModal">✕</button>
        </div>
        <div class="p-4 space-y-2">
          <div class="text-sm text-[#6b6a67]">
            <span class="font-medium text-[#1b1b18]">Date: </span>
            {{ selectedEventDateRange }}
          </div>
          <div v-if="selectedEvent?.extendedProps?.location" class="text-sm text-[#6b6a67]">
            <span class="font-medium text-[#1b1b18]">Location: </span>{{ selectedEvent.extendedProps.location }}
          </div>
          <div v-if="selectedEvent?.extendedProps?.description" class="text-sm text-[#1b1b18] whitespace-pre-wrap">{{ selectedEvent.extendedProps.description }}</div>
        </div>
        <div class="px-4 py-3 border-t border-[#ecebea] flex justify-end">
          <button class="px-4 py-2 rounded bg-[#0B6B3A] text-white hover:bg-[#095a31]" @click="closeModal">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'

const calendarRef = ref(null)
const headerLabel = ref('')
const loading = ref(false)
const showModal = ref(false)
const selectedEvent = ref(null)

const calendarOptions = ref({
  plugins: [dayGridPlugin, interactionPlugin],
  initialView: 'dayGridMonth',
  headerToolbar: false,
  height: 'auto',
  firstDay: 1,
  events: fetchEvents,
  eventClick(info) {
    selectedEvent.value = {
      title: info.event.title,
      start: info.event.start,
      end: info.event.end || info.event.start,
      extendedProps: info.event.extendedProps || {},
    }
    showModal.value = true
  },
  datesSet(arg) {
    headerLabel.value = new Date(arg.start.setDate(arg.start.getDate() + 14)).toLocaleString('en-US', { month: 'long', year: 'numeric' })
  },
  eventClassNames(arg) {
    const base = ['!bg-[#0AA74A]','!border-[#0AA74A]','!text-white','rounded','px-1','text-xs']
    return base
  },
  dayCellClassNames(arg) {
    return arg.isToday ? ['!bg-[#E6FF8A]'] : []
  },
  loading(isLoading) { loading.value = isLoading },
  dayMaxEventRows: 3,
  expandRows: true,
})

function fetchEvents(fetchInfo, successCallback, failureCallback) {
  const params = new URLSearchParams({ start: fetchInfo.startStr, end: fetchInfo.endStr })
  fetch(`/api/events?${params}`)
    .then(r => r.json())
    .then(data => successCallback(data))
    .catch(err => failureCallback(err))
}

function goPrev() {
  const api = calendarRef.value?.getApi?.()
  if (api) api.prev()
}
function goNext() {
  const api = calendarRef.value?.getApi?.()
  if (api) api.next()
}
function goToday() {
  const api = calendarRef.value?.getApi?.()
  if (api) api.today()
}

const selectedEventDateRange = computed(() => {
  if (!selectedEvent.value) return ''
  const s = new Date(selectedEvent.value.start)
  const e = new Date(selectedEvent.value.end)
  const fmt = (d) => d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: '2-digit' })
  const sStr = fmt(s)
  const eStr = fmt(e)
  return sStr === eStr ? sStr : `${sStr} - ${eStr}`
})

function onEsc(e) {
  if (e.key === 'Escape') closeModal()
}
function closeModal() {
  showModal.value = false
}

onMounted(() => document.addEventListener('keydown', onEsc))
onBeforeUnmount(() => document.removeEventListener('keydown', onEsc))
</script>
