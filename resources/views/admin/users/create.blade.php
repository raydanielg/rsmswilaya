<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="admin-name" content="{{ session('admin_name') }}" />
    <title>Add Admin • RSMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full" style="font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <admin-header @toggle-sidebar="sidebarOpen = !sidebarOpen"></admin-header>
      <div class="flex flex-1 min-h-0">
        <admin-sidebar :open="sidebarOpen" active="settings"></admin-sidebar>
        <main class="flex-1 min-w-0 p-4 md:p-8">
          <div class="max-w-screen-sm mx-auto space-y-5">
            <div class="bg-white rounded-2xl border border-[#e3e2df] shadow-sm px-5 md:px-6 pt-4 pb-3 flex items-center justify-between gap-3 flex-wrap">
              <div>
                <h1 class="text-xl md:text-2xl font-semibold">Add admin</h1>
                <p class="text-sm text-[#6b6a67] mt-1">Create a new admin account and set their login password.</p>
              </div>
              <a href="{{ route('admin.users.index') }}" class="text-sm text-[#0B6B3A] hover:underline">← Back to admin users</a>
            </div>

            <div class="bg-white rounded-2xl border border-[#e3e2df] shadow-sm p-5 md:p-6">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
              @csrf
              <div>
                <label class="block text-sm font-medium mb-1">Name</label>
                <input type="text" name="name" required class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" />
                @error('name')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" required class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" />
                @error('email')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium mb-1">Password</label>
                  <input type="password" name="password" required class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" />
                  @error('password')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Confirm Password</label>
                  <input type="password" name="password_confirmation" required class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" />
                </div>
              </div>
              <div class="flex gap-3 justify-end">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded border border-[#e5e4e2] bg-white hover:bg-[#f7f7f6]">Cancel</a>
                <button class="px-4 py-2 rounded bg-[#0B6B3A] text-white hover:bg-[#095a31]">Save</button>
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
