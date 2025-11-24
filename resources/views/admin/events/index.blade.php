<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="admin-name" content="{{ session('admin_name') }}" />
    <title>Events • RSMS</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <admin-header @toggle-sidebar="sidebarOpen = !sidebarOpen"></admin-header>
      <div class="flex flex-1 min-h-0">
        <admin-sidebar :open="sidebarOpen" active="events"></admin-sidebar>
        <main class="flex-1 min-w-0 p-6 md:p-8">
          <div class="max-w-screen-xl mx-auto space-y-8">
            <div class="flex items-center justify-between">
              <h1 class="text-2xl font-semibold">Events</h1>
              <a href="{{ route('admin.events.create') }}" class="px-4 py-2 rounded bg-[#0AA74A] text-white hover:bg-[#089543]">Add Event</a>
            </div>
            <div class="rounded-lg border border-[#ecebea] bg-white overflow-hidden shadow-sm">
              <table class="w-full text-left">
                <thead class="bg-[#f7f7f6] text-[#6b6a67] text-sm">
                  <tr>
                    <th class="px-4 py-2">Title</th>
                    <th class="px-4 py-2">Dates</th>
                    <th class="px-4 py-2">Location</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($events as $ev)
                    <tr class="border-t hover:bg-[#fafafa]">
                      <td class="px-4 py-2 text-[#0B6B3A] font-medium">{{ $ev->title }}</td>
                      <td class="px-4 py-2 text-sm text-[#6b6a67]">{{ \Carbon\Carbon::parse($ev->start_date)->format('M d, Y') }} @if($ev->end_date) - {{ \Carbon\Carbon::parse($ev->end_date)->format('M d, Y') }} @endif</td>
                      <td class="px-4 py-2 text-sm">{{ $ev->location }}</td>
                    </tr>
                  @endforeach
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
    <script>window.sidebarOpen = true</script>
  </body>
</html>
