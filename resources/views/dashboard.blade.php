<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="admin-name" content="{{ session('admin_name') }}" />
    <title>Dashboard • RSMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full" style="font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <admin-header @toggle-sidebar="sidebarOpen = !sidebarOpen"></admin-header>
      <div class="flex flex-1 min-h-0">
        <admin-sidebar :open="sidebarOpen" active="dashboard"></admin-sidebar>
        <main class="flex-1 min-w-0 p-4 md:p-6">
          <div class="max-w-screen-xl mx-auto space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
              <h1 class="text-2xl font-semibold">Dashboard</h1>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
              <div class="rounded-xl p-4 text-white bg-gradient-to-br from-[#0B6B3A] to-[#0AA74A] shadow-sm flex flex-col justify-between">
                <div class="text-xs font-semibold tracking-wide text-white/80">PUBLICATIONS</div>
                <div class="mt-2 flex items-end justify-between">
                  <div class="text-3xl font-semibold">{{ number_format($kpis['publications']) }}</div>
                </div>
              </div>
              <div class="rounded-xl p-4 bg-white shadow-sm border border-[#ecebea] flex flex-col justify-between">
                <div class="text-xs font-semibold tracking-wide text-[#6b6a67]">BLOG POSTS</div>
                <div class="mt-2 flex items-end justify-between">
                  <div class="text-3xl font-semibold text-[#0B6B3A]">{{ number_format($kpis['posts']) }}</div>
                </div>
              </div>
              <div class="rounded-xl p-4 bg-white shadow-sm border border-[#ecebea] flex flex-col justify-between">
                <div class="text-xs font-semibold tracking-wide text-[#6b6a67]">EVENTS</div>
                <div class="mt-2 flex items-end justify-between">
                  <div class="text-3xl font-semibold text-[#0B6B3A]">{{ number_format($kpis['events']) }}</div>
                </div>
              </div>
              <div class="rounded-xl p-4 bg-[#0A5F34] text-white shadow-sm flex flex-col justify-between">
                <div class="text-xs font-semibold tracking-wide text-white/80">ADMINS</div>
                <div class="mt-2 flex items-end justify-between">
                  <div class="text-3xl font-semibold">{{ number_format($kpis['admins']) }}</div>
                </div>
              </div>
            </div>
            <div class="rounded-lg border border-[#ecebea] bg-white p-4 sm:p-6">
              <div class="text-sm text-[#6b6a67]">Quick actions</div>
              <div class="mt-3 flex flex-col sm:flex-row flex-wrap gap-3">
                <a href="/admin/events/create" class="w-full sm:w-auto text-center px-4 py-2 rounded bg-[#0AA74A] text-white hover:bg-[#089543]">Add Event</a>
                <a href="/admin/publications/folders/create" class="w-full sm:w-auto text-center px-4 py-2 rounded border hover:bg-[#f7f7f6]">New Publication Folder</a>
                <a href="/admin/blog/categories/create" class="w-full sm:w-auto text-center px-4 py-2 rounded border hover:bg-[#f7f7f6]">New Blog Category</a>
                <a href="/admin/blog/create" class="w-full sm:w-auto text-center px-4 py-2 rounded border hover:bg-[#f7f7f6]">New Blog Post</a>
              </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
              <!-- Compact calendar -->
              <div class="lg:col-span-2 rounded-2xl border border-[#ecebea] bg-white p-4 sm:p-6 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                  <h2 class="text-sm font-semibold text-[#4a4946] tracking-wide">CALENDAR</h2>
                  <a href="/calendar" class="text-sm text-[#0B6B3A] hover:underline">Open full calendar →</a>
                </div>
                <div class="mt-2">
                  <full-calendar />
                </div>
              </div>
              <!-- Recent events & latest results -->
              <div class="space-y-4">
                <div class="rounded-lg border border-[#ecebea] bg-white p-4">
                  <h2 class="text-sm font-semibold text-[#6b6a67] mb-3">RECENT EVENTS</h2>
                  <ul class="space-y-3 max-h-64 overflow-y-auto">
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
                <div class="rounded-lg border border-[#ecebea] bg-white p-4">
                  <h2 class="text-sm font-semibold text-[#6b6a67] mb-1">LATEST RESULTS PUBLISHED</h2>
                  <p class="text-xs text-[#8a8986] mb-3">Most recent result documents uploaded to the system.</p>
                  <ul class="space-y-2 max-h-64 overflow-y-auto">
                    @forelse($latestResults as $doc)
                      <li class="border border-[#f0efed] rounded px-3 py-2">
                        <div class="flex items-center justify-between gap-2">
                          <div class="text-xs font-semibold text-[#0B6B3A]">
                            {{ $doc->exam }} {{ $doc->year }}
                            @if($doc->type_code)
                              · {{ $doc->type_code }}
                            @endif
                          </div>
                          <div class="text-[10px] uppercase tracking-wide text-[#8a8986]">
                            {{ optional($doc->created_at)->format('M d, Y') }}
                          </div>
                        </div>
                        <div class="mt-1 text-xs text-[#4a4946] truncate">
                          @php
                            $location = collect([$doc->region_name, $doc->district_name, $doc->school_name])->filter()->implode(' • ');
                          @endphp
                          {{ $location ?: 'National' }}
                        </div>
                        @if($doc->type_name)
                          <div class="mt-0.5 text-[11px] text-[#8a8986] truncate">{{ $doc->type_name }}</div>
                        @endif
                      </li>
                    @empty
                      <li class="text-sm text-[#6b6a67]">No results uploaded yet.</li>
                    @endforelse
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
      <admin-footer></admin-footer>
    </div>
    <script>
      window.sidebarOpen = false
    </script>
  </body>
</html>
