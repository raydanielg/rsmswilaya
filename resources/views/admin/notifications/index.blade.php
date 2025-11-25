<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Notifications 9 RSMS</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <admin-header @toggle-sidebar="sidebarOpen = !sidebarOpen"></admin-header>
      <div class="flex flex-1 min-h-0">
        <admin-sidebar :open="sidebarOpen" active="notifications"></admin-sidebar>
        <main class="flex-1 min-w-0 p-6">
          <div class="max-w-screen-lg mx-auto">
            @if(session('status'))
              <div class="p-3 rounded bg-green-50 border border-green-200 text-green-800 text-sm mb-4">{{ session('status') }}</div>
            @endif
            <h1 class="text-2xl font-semibold mb-4">Notifications</h1>
            <form method="POST" action="{{ route('admin.notifications.store') }}" class="space-y-6 mb-8">
              @csrf
              <div class="bg-white border rounded-md p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium mb-1">Title</label>
                  <input type="text" name="title" class="w-full border rounded px-3 py-2" required />
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Start time</label>
                  <input type="datetime-local" name="starts_at" class="w-full border rounded px-3 py-2" />
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">End time</label>
                  <input type="datetime-local" name="ends_at" class="w-full border rounded px-3 py-2" />
                </div>
                <div class="md:col-span-2">
                  <label class="block text-sm font-medium mb-1">Body</label>
                  <textarea name="body" rows="3" class="w-full border rounded px-3 py-2" required></textarea>
                </div>
              </div>
              <div class="flex justify-end gap-3">
                <button class="px-4 py-2 rounded bg-[#0B6B3A] text-white hover:bg-[#095a31]">Publish Notification</button>
              </div>
            </form>

            <div class="bg-white border rounded-md">
              <div class="px-4 py-3 border-b"><div class="font-medium">Existing Notifications</div></div>
              <div class="p-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                  <thead>
                    <tr class="text-left border-b">
                      <th class="py-2 pr-4">Title</th>
                      <th class="py-2 pr-4">Starts</th>
                      <th class="py-2 pr-4">Ends</th>
                      <th class="py-2 pr-4">Created</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($notifications as $n)
                      <tr class="border-b last:border-0">
                        <td class="py-2 pr-4">{{ $n->title }}</td>
                        <td class="py-2 pr-4">{{ $n->starts_at }}</td>
                        <td class="py-2 pr-4">{{ $n->ends_at }}</td>
                        <td class="py-2 pr-4">{{ $n->created_at }}</td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="4" class="py-4 text-gray-500">No notifications yet.</td>
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
  </body>
</html>
