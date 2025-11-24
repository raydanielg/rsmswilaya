<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="admin-name" content="{{ session('admin_name') }}" />
    <title>Admin Users • RSMS</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <admin-header @toggle-sidebar="sidebarOpen = !sidebarOpen"></admin-header>
      <div class="flex flex-1 min-h-0">
        <admin-sidebar :open="sidebarOpen" active="settings"></admin-sidebar>
        <main class="flex-1 min-w-0 p-4 md:p-6">
          <div class="max-w-screen-xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
              <h1 class="text-2xl font-semibold">Admin Users</h1>
              <a href="{{ route('admin.users.create') }}" class="px-4 py-2 rounded bg-[#0AA74A] text-white hover:bg-[#089543]">Add Admin</a>
            </div>
            <div class="rounded-lg border border-[#ecebea] bg-white overflow-hidden">
              <table class="w-full text-left">
                <thead class="bg-[#f7f7f6] text-[#6b6a67] text-sm">
                  <tr>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Created</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($admins as $a)
                    <tr class="border-t">
                      <td class="px-4 py-2">{{ $a->name }}</td>
                      <td class="px-4 py-2">{{ $a->email }}</td>
                      <td class="px-4 py-2 text-sm text-[#6b6a67]">{{ \Carbon\Carbon::parse($a->created_at)->format('M d, Y') }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </main>
      </div>
      <admin-footer></admin-footer>
    </div>
    <script>window.sidebarOpen = true</script>
  </body>
</html>
