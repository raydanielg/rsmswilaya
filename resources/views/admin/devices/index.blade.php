<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Devices • RSMS</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <admin-header @toggle-sidebar="sidebarOpen = !sidebarOpen"></admin-header>
      <div class="flex flex-1 min-h-0">
        <admin-sidebar :open="sidebarOpen" active="settings"></admin-sidebar>
        <main class="flex-1 min-w-0 p-6">
          <div class="max-w-screen-xl mx-auto">
            @if(session('status'))
              <div class="p-3 rounded bg-green-50 border border-green-200 text-green-800 text-sm mb-4">{{ session('status') }}</div>
            @endif
            <div class="flex items-center justify-between mb-4">
              <h1 class="text-2xl font-semibold">Devices</h1>
              <form method="GET" action="{{ route('admin.devices.index') }}" class="flex items-center gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search device id / name / model" class="border rounded px-3 py-1.5 text-sm" />
                <button class="px-3 py-1.5 rounded border text-sm bg-white hover:bg-gray-50">Search</button>
              </form>
            </div>

            <div class="bg-white border rounded-md overflow-x-auto">
              <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b">
                  <tr class="text-left">
                    <th class="px-3 py-2">Device ID</th>
                    <th class="px-3 py-2">Name</th>
                    <th class="px-3 py-2">Platform</th>
                    <th class="px-3 py-2">Model</th>
                    <th class="px-3 py-2">App / OS</th>
                    <th class="px-3 py-2">Flags</th>
                    <th class="px-3 py-2">Last seen</th>
                    <th class="px-3 py-2">Note</th>
                    <th class="px-3 py-2 text-right">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($devices as $device)
                    <tr class="border-b align-top">
                      <td class="px-3 py-2 font-mono text-xs break-all">{{ $device->device_id }}</td>
                      <td class="px-3 py-2">{{ $device->name }}</td>
                      <td class="px-3 py-2">{{ $device->platform }}</td>
                      <td class="px-3 py-2">{{ $device->model }}</td>
                      <td class="px-3 py-2">
                        <div>App: {{ $device->app_version ?: '—' }}</div>
                        <div class="text-xs text-gray-500">OS: {{ $device->os_version ?: '—' }}</div>
                      </td>
                      <td class="px-3 py-2">
                        <div class="flex flex-col gap-1 text-xs">
                          <span class="inline-flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full {{ $device->is_blocked ? 'bg-red-500' : 'bg-gray-300' }}"></span>
                            Blocked
                          </span>
                          <span class="inline-flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full {{ $device->maintenance_required ? 'bg-amber-500' : 'bg-gray-300' }}"></span>
                            Maintenance
                          </span>
                          <span class="inline-flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full {{ $device->update_required ? 'bg-blue-500' : 'bg-gray-300' }}"></span>
                            Update
                          </span>
                        </div>
                      </td>
                      <td class="px-3 py-2 text-xs text-gray-600">
                        {{ $device->last_seen_at ? $device->last_seen_at : '—' }}
                      </td>
                      <td class="px-3 py-2 text-xs max-w-xs">
                        {{ $device->note }}
                      </td>
                      <td class="px-3 py-2 text-right">
                        <form method="POST" action="{{ route('admin.devices.update', $device->id) }}" class="inline-block text-xs space-y-1">
                          @csrf
                          <label class="flex items-center gap-1">
                            <input type="checkbox" name="is_blocked" value="1" @checked($device->is_blocked) />
                            <span>Blocked</span>
                          </label>
                          <label class="flex items-center gap-1">
                            <input type="checkbox" name="maintenance_required" value="1" @checked($device->maintenance_required) />
                            <span>Maintenance</span>
                          </label>
                          <label class="flex items-center gap-1">
                            <input type="checkbox" name="update_required" value="1" @checked($device->update_required) />
                            <span>Update</span>
                          </label>
                          <textarea name="note" rows="2" class="mt-1 w-52 border rounded px-2 py-1" placeholder="Admin note...">{{ $device->note }}</textarea>
                          <div class="mt-1 flex justify-end">
                            <button class="px-3 py-1 rounded bg-[#0B6B3A] text-white hover:bg-[#095a31]">Save</button>
                          </div>
                        </form>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="9" class="px-3 py-6 text-center text-sm text-gray-500">No devices registered yet.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </main>
      </div>
      <admin-footer></admin-footer>
    </div>
  </body>
</html>
