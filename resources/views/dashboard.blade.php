<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="admin-name" content="{{ session('admin_name') }}" />
    <title>Dashboard • RSMS</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <admin-header @toggle-sidebar="sidebarOpen = !sidebarOpen"></admin-header>
      <div class="flex flex-1 min-h-0">
        <admin-sidebar :open="sidebarOpen" active="dashboard"></admin-sidebar>
        <main class="flex-1 min-w-0 p-4 md:p-6">
          <div class="max-w-screen-xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
              <h1 class="text-2xl font-semibold">Dashboard</h1>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
              <div class="rounded-lg p-4 text-white bg-gradient-to-br from-[#0B6B3A] to-[#0AA74A] shadow-sm">
                <div class="text-sm opacity-90">Publications</div>
                <div class="mt-1 text-3xl font-semibold">{{ number_format($kpis['publications']) }}</div>
              </div>
              <div class="rounded-lg p-4 text-[#1b1b18] bg-[#E6FF8A] shadow-sm">
                <div class="text-sm opacity-80">Blog Posts</div>
                <div class="mt-1 text-3xl font-semibold text-[#0B6B3A]">{{ number_format($kpis['posts']) }}</div>
              </div>
              <div class="rounded-lg p-4 text-white bg-[#0AA74A] shadow-sm">
                <div class="text-sm opacity-90">Events</div>
                <div class="mt-1 text-3xl font-semibold">{{ number_format($kpis['events']) }}</div>
              </div>
              <div class="rounded-lg p-4 text-white bg-[#0A5F34] shadow-sm">
                <div class="text-sm opacity-90">Admins</div>
                <div class="mt-1 text-3xl font-semibold">{{ number_format($kpis['admins']) }}</div>
              </div>
            </div>
            <div class="rounded-lg border border-[#ecebea] bg-white p-6">
              <div class="text-sm text-[#6b6a67]">Quick actions</div>
              <div class="mt-3 flex flex-wrap gap-3">
                <a href="/admin/events/create" class="px-4 py-2 rounded bg-[#0AA74A] text-white hover:bg-[#089543]">Add Event</a>
                <a href="/admin/publications/folders/create" class="px-4 py-2 rounded border hover:bg-[#f7f7f6]">New Publication Folder</a>
                <a href="/admin/blog/categories/create" class="px-4 py-2 rounded border hover:bg-[#f7f7f6]">New Blog Category</a>
                <a href="/admin/blog/create" class="px-4 py-2 rounded border hover:bg-[#f7f7f6]">New Blog Post</a>
              </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
              <!-- Compact calendar -->
              <div class="lg:col-span-2 rounded-lg border border-[#ecebea] bg-white p-4">
                <div class="flex items-center justify-between mb-3">
                  <h2 class="text-sm font-semibold text-[#6b6a67]">CALENDAR</h2>
                  <a href="/calendar" class="text-sm text-[#0B6B3A] hover:underline">Open full calendar →</a>
                </div>
                <full-calendar />
              </div>
              <!-- Recent events -->
              <div class="rounded-lg border border-[#ecebea] bg-white p-4">
                <h2 class="text-sm font-semibold text-[#6b6a67] mb-3">RECENT EVENTS</h2>
                <ul class="space-y-3">
                  @forelse($recentEvents as $ev)
                    <li class="border border-[#f0efed] rounded p-3">
                      <div class="text-xs text-[#6b6a67]">{{ \Carbon\Carbon::parse($ev->start_date)->format('M d, Y') }} @if($ev->end_date) - {{ \Carbon\Carbon::parse($ev->end_date)->format('M d, Y') }} @endif</div>
                      <div class="font-medium text-[#0B6B3A]">{{ $ev->title }}</div>
                      @if($ev->location)
                        <div class="text-xs text-[#6b6a67]">{{ $ev->location }}</div>
                      @endif
                    </li>
                  @empty
                    <li class="text-sm text-[#6b6a67]">No events yet.</li>
                  @endforelse
                </ul>
              </div>
            </div>
          </div>
        </main>
      </div>
      <admin-footer></admin-footer>
    </div>
    <script>
      window.sidebarOpen = true
    </script>
  </body>
</html>
