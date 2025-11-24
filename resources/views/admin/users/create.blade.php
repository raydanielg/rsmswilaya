<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="admin-name" content="{{ session('admin_name') }}" />
    <title>Add Admin • RSMS</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <admin-header @toggle-sidebar="sidebarOpen = !sidebarOpen"></admin-header>
      <div class="flex flex-1 min-h-0">
        <admin-sidebar :open="sidebarOpen" active="settings"></admin-sidebar>
        <main class="flex-1 min-w-0 p-4 md:p-6">
          <div class="max-w-screen-sm mx-auto">
            <h1 class="text-2xl font-semibold mb-4">Add Admin</h1>
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
                  <input type="password" name="password" required class="w-full border border-[#e5e4e2] rounded px-3 py-2" />
                  @error('password')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Confirm Password</label>
                  <input type="password" name="password_confirmation" required class="w-full border border-[#e5e4e2] rounded px-3 py-2" />
                </div>
              </div>
              <div class="flex gap-3">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded border">Cancel</a>
                <button class="px-4 py-2 rounded bg-[#0B6B3A] text-white hover:bg-[#095a31]">Save</button>
              </div>
            </form>
          </div>
        </main>
      </div>
      <admin-footer></admin-footer>
    </div>
    <script>window.sidebarOpen = true</script>
  </body>
</html>
