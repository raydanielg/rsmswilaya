<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="admin-name" content="{{ session('admin_name') }}" />
    <title>Events • RSMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full" style="font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <admin-header @toggle-sidebar="sidebarOpen = !sidebarOpen"></admin-header>
      <div class="flex flex-1 min-h-0">
        <admin-sidebar :open="sidebarOpen" active="events"></admin-sidebar>
        <main class="flex-1 min-w-0 p-4 md:p-8">
          <div class="max-w-screen-xl mx-auto space-y-6">
            <div class="bg-white rounded-2xl border border-[#e3e2df] shadow-sm px-5 md:px-6 pt-4 pb-3 flex items-center justify-between gap-3 flex-wrap">
              <div>
                <h1 class="text-xl md:text-2xl font-semibold">Events</h1>
                <p class="text-sm text-[#6b6a67] mt-1">Manage upcoming and past events shown on the public site and calendar.</p>
              </div>
              <a href="{{ route('admin.events.create') }}" class="px-4 py-2 rounded-lg bg-[#0AA74A] text-white hover:bg-[#089543] text-sm">+ Add event</a>
            </div>

            <div class="rounded-2xl border border-[#ecebea] bg-white overflow-hidden shadow-sm">
              <table class="w-full text-left text-sm">
                <thead class="bg-[#f7f7f6] text-[#6b6a67] uppercase tracking-wide text-[11px]">
                  <tr>
                    <th class="px-4 py-2 font-medium">Title</th>
                    <th class="px-4 py-2 font-medium">Dates</th>
                    <th class="px-4 py-2 font-medium">Location</th>
                    <th class="px-4 py-2 font-medium text-right">Actions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-[#f0efed]">
                  @forelse($events as $ev)
                    <tr class="hover:bg-[#fafafa] transition-colors">
                      <td class="px-4 py-2.5 align-top">
                        <div class="font-medium text-[#0B6B3A]">{{ $ev->title }}</div>
                      </td>
                      <td class="px-4 py-2.5 align-top">
                        <div class="inline-flex items-center rounded-full bg-[#f0fdf4] text-[#166534] px-3 py-1 text-[11px] font-medium">
                          {{ \Carbon\Carbon::parse($ev->start_date)->format('M d, Y') }}
                          @if($ev->end_date)
                            <span class="mx-1">–</span>
                            {{ \Carbon\Carbon::parse($ev->end_date)->format('M d, Y') }}
                          @endif
                        </div>
                      </td>
                      <td class="px-4 py-2.5 align-top text-xs text-[#4a4946]">
                        {{ $ev->location ?: '—' }}
                      </td>
                      <td class="px-4 py-2.5 align-top text-right">
                        <div class="inline-flex items-center gap-1.5">
                          <a href="{{ route('admin.events.edit', $ev->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full border border-[#e5e4e2] bg-white text-[#4a4946] hover:bg-[#f3f3f2]" title="Edit event">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                              <path d="M13.586 3.586a2 2 0 0 1 2.828 2.828l-8.25 8.25a2 2 0 0 1-.878.518l-3.25.93a.75.75 0 0 1-.92-.92l.93-3.25a2 2 0 0 1 .518-.878l8.25-8.25Z" />
                            </svg>
                          </a>
                          <form method="POST" action="{{ route('admin.events.delete', $ev->id) }}" onsubmit="return confirm('Delete this event?');" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-50 text-red-600 hover:bg-red-100" title="Delete event">
                              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                <path fill-rule="evenodd" d="M8.75 3A1.75 1.75 0 0 0 7.06 4.25H4.5a.75.75 0 0 0 0 1.5h.29l.71 8.39A2.25 2.25 0 0 0 7.75 16.5h4.5a2.25 2.25 0 0 0 2.25-2.36l.71-8.39h.29a.75.75 0 0 0 0-1.5h-2.56A1.75 1.75 0 0 0 11.25 3h-2.5Zm.75 3.75a.75.75 0 0 0-1.5 0l.25 6a.75.75 0 0 0 1.5 0l-.25-6Zm3 0a.75.75 0 0 0-1.5 0l-.25 6a.75.75 0 0 0 1.5 0l.25-6Z" clip-rule="evenodd" />
                              </svg>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="4" class="px-4 py-6 text-center text-sm text-[#6b6a67]">No events have been created yet.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
            <div class="text-sm text-[#6b6a67]">
              {{ $events->links() }}
            </div>
          </div>
        </main>
      </div>
      <admin-footer></admin-footer>
    </div>
    <script>window.sidebarOpen = false</script>
  </body>
</html>
