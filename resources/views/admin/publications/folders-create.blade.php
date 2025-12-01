<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="admin-name" content="{{ session('admin_name') }}" />
    <title>RSMS • Create Publication Folder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full" style="font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <admin-header @toggle-sidebar="sidebarOpen = !sidebarOpen"></admin-header>
      <div class="flex flex-1 min-h-0">
        <admin-sidebar :open="sidebarOpen" active="publications"></admin-sidebar>
        <main class="flex-1 min-w-0 p-4 md:p-8">
        <div class="max-w-screen-md mx-auto space-y-5">
          <div class="bg-white rounded-2xl border border-[#e3e2df] shadow-sm px-5 md:px-6 pt-4 pb-3 flex items-center justify-between gap-3 flex-wrap">
            <div>
              <h1 class="text-xl md:text-2xl font-semibold">Create publication folder</h1>
              <p class="text-sm text-[#6b6a67] mt-1">Organise exam formats and documents into logical folders.</p>
            </div>
            <a href="/publications" class="text-sm text-[#0B6B3A] hover:underline">← Back to publications</a>
          </div>

          <div class="bg-white rounded-2xl border border-[#e3e2df] shadow-sm p-5 md:p-6">
          <form method="POST" action="{{ route('admin.pub.folders.store') }}" class="space-y-4">
            @csrf
            <div>
              <label class="block text-sm font-medium mb-1">Folder name</label>
              <input type="text" name="name" required class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" placeholder="Approved Exam Formats" />
              @error('name')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="flex gap-3 justify-end">
              <a href="/publications" class="px-4 py-2 rounded border">Cancel</a>
              <button class="px-4 py-2 rounded bg-[#0B6B3A] text-white hover:bg-[#095a31]">Create</button>
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
