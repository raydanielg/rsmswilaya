<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="admin-name" content="{{ session('admin_name') }}" />
    <title>{{ $region->name }} • Region • RSMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full" style="font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <admin-header @toggle-sidebar="sidebarOpen = !sidebarOpen"></admin-header>
      <div class="flex flex-1 min-h-0">
        <admin-sidebar :open="sidebarOpen" active="regions"></admin-sidebar>
        <main class="flex-1 min-w-0 p-6 md:p-8">
          <div class="max-w-5xl mx-auto space-y-6">
            <div class="bg-white rounded-2xl border border-[#e3e2df] shadow-sm px-5 md:px-6 pt-4 pb-3 flex items-center justify-between gap-3 flex-wrap">
              <div>
                <h1 class="text-xl md:text-2xl font-semibold">{{ $region->name }}</h1>
                <p class="text-sm text-[#6b6a67] mt-1">Region overview and list of schools under this region.</p>
              </div>
              <div class="flex items-center gap-2">
                <a href="{{ route('admin.regions.index') }}" class="px-3 py-2 rounded-full border border-[#d7d6d4] bg-white hover:bg-[#f7f7f6] text-xs md:text-sm">Back to regions</a>
                <a href="{{ route('admin.regions.edit', $region->id) }}" class="px-4 py-2 rounded-full bg-[#0B6B3A] text-white text-xs md:text-sm hover:bg-[#095a31]">Edit region</a>
              </div>
            </div>

            <div class="bg-white rounded-2xl border border-[#e3e2df] shadow-sm p-5 md:p-6">
              <div class="flex items-center justify-between mb-3">
                <div>
                  <div class="text-sm font-semibold text-[#1b1b18]">Schools in {{ $region->name }}</div>
                  <p class="text-xs text-[#8a8986]">All schools grouped by councils under this region.</p>
                </div>
              </div>
              <div class="border border-[#f0efed] rounded-lg overflow-x-auto">
                <table class="min-w-full text-xs md:text-sm bg-white">
                  <thead>
                    <tr class="text-left bg-[#f7f7f6] text-[#6b6a67] text-[11px] uppercase tracking-wide">
                      <th class="px-3 py-2 border-b border-[#ecebea]">School</th>
                      <th class="px-3 py-2 border-b border-[#ecebea]">Code</th>
                      <th class="px-3 py-2 border-b border-[#ecebea]">Council</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-[#f3f2f0]">
                    @forelse($schools as $s)
                      <tr>
                        <td class="px-3 py-2 align-top text-sm font-medium text-[#1b1b18]">{{ $s->school_name }}</td>
                        <td class="px-3 py-2 align-top text-xs text-[#4a4946]">{{ $s->code ?? '—' }}</td>
                        <td class="px-3 py-2 align-top text-xs text-[#4a4946]">{{ $s->district_name }}</td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="3" class="px-3 py-4 text-sm text-gray-500">No schools found for this region.</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </main>
      </div>
      <admin-footer></admin-footer>
    </div>
    <script>window.sidebarOpen = false</script>
  </body>
</html>
