<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="admin-name" content="{{ session('admin_name') }}" />
    <title>Edit Region • RSMS</title>
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
          <div class="max-w-xl mx-auto space-y-6">
            <div class="bg-white rounded-2xl border border-[#e3e2df] shadow-sm px-5 md:px-6 pt-4 pb-3">
              <div class="flex items-center justify-between gap-3 flex-wrap">
                <div>
                  <h1 class="text-xl md:text-2xl font-semibold">Edit region</h1>
                  <p class="text-sm text-[#6b6a67] mt-1">Update the name for this region. Councils and schools under it will remain attached.</p>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-2xl border border-[#e3e2df] shadow-sm p-5 md:p-6">
              <form method="POST" action="{{ route('admin.regions.update', $region->id) }}" class="space-y-4">
                @csrf
                <div>
                  <label class="block text-xs font-medium mb-1 text-[#4a4946]">Region name</label>
                  <input type="text" name="name" value="{{ old('name', $region->name) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]/60" />
                  @error('name')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                  @enderror
                </div>
                <div class="flex justify-between items-center gap-3">
                  <a href="{{ route('admin.regions.index') }}" class="text-sm text-[#6b6a67] hover:text-[#0B6B3A]">Cancel</a>
                  <button class="px-4 py-2 rounded-full bg-[#0B6B3A] text-white text-sm font-medium hover:bg-[#095a31]">Save changes</button>
                </div>
              </form>
            </div>
          </div>
        </main>
      </div>
      <admin-footer></admin-footer>
    </div>
    <script>window.sidebarOpen = false</script>
  </body>
</html>
