<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Settings • RSMS</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <admin-header @toggle-sidebar="sidebarOpen = !sidebarOpen"></admin-header>
      <div class="flex flex-1 min-h-0">
        <admin-sidebar :open="sidebarOpen" active="settings"></admin-sidebar>
        <main class="flex-1 min-w-0 p-6">
          <div class="max-w-screen-lg mx-auto">
            @if(session('status'))
              <div class="p-3 rounded bg-green-50 border border-green-200 text-green-800 text-sm mb-4">{{ session('status') }}</div>
            @endif
            <h1 class="text-2xl font-semibold mb-4">Site Settings</h1>
            <form method="POST" action="{{ route('admin.settings.save') }}" enctype="multipart/form-data" class="space-y-6">
              @csrf
              <div class="bg-white border rounded-md">
                <div class="px-4 py-3 border-b"><div class="font-medium">General</div></div>
                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium mb-1">Site name</label>
                    <input type="text" name="site_name" class="w-full border rounded px-3 py-2" value="{{ $settings['site_name'] }}" />
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-1">Favicon</label>
                    <input type="file" name="favicon" accept=".png,.ico" />
                    <div class="text-xs text-gray-500 mt-1">or URL</div>
                    <input type="url" name="favicon_url" class="w-full border rounded px-3 py-2" placeholder="https://.../favicon.png" />
                    @if(!empty($settings['favicon']))
                      <div class="mt-2 text-xs text-gray-600">Current: {{ $settings['favicon'] }}</div>
                    @endif
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-1">Hero image</label>
                    <input type="file" name="hero" accept="image/*" />
                    <div class="text-xs text-gray-500 mt-1">or URL</div>
                    <input type="url" name="hero_url" class="w-full border rounded px-3 py-2" placeholder="https://.../hero.jpg" />
                    @if(!empty($settings['hero']))
                      <div class="mt-2 text-xs text-gray-600">Current: {{ $settings['hero'] }}</div>
                    @endif
                  </div>
                </div>
              </div>

              <div class="bg-white border rounded-md">
                <div class="px-4 py-3 border-b"><div class="font-medium">Maintenance</div></div>
                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="flex items-center gap-2">
                    <input type="checkbox" id="maint_enabled" name="maint_enabled" value="1" @checked($settings['maint_enabled']) />
                    <label for="maint_enabled" class="text-sm">Enable maintenance mode</label>
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-1">Planned end (ISO or local datetime)</label>
                    <input type="text" name="maint_ends" class="w-full border rounded px-3 py-2" placeholder="2025-12-31T23:59:59Z" value="{{ $settings['maint_ends'] }}" />
                  </div>
                  <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Message</label>
                    <textarea name="maint_message" rows="2" class="w-full border rounded px-3 py-2" placeholder="We are improving our systems...">{{ $settings['maint_message'] }}</textarea>
                  </div>
                </div>
              </div>

              <div class="bg-white border rounded-md">
                <div class="px-4 py-3 border-b"><div class="font-medium">Admins & Backup</div></div>
                <div class="p-4 flex flex-wrap gap-3 text-sm">
                  <a href="/admin/users" class="px-3 py-2 rounded border hover:bg-gray-50">Manage Admins</a>
                  <a href="{{ route('admin.devices.index') }}" class="px-3 py-2 rounded border hover:bg-gray-50">Manage Devices</a>
                  <button type="button" class="px-3 py-2 rounded border hover:bg-gray-50" disabled>Download DB Backup (coming soon)</button>
                </div>
              </div>

              <div class="flex justify-end gap-3">
                <a href="/admin" class="px-4 py-2 rounded border">Cancel</a>
                <button class="px-4 py-2 rounded bg-[#0B6B3A] text-white hover:bg-[#095a31]">Save Settings</button>
              </div>
            </form>
          </div>
        </main>
      </div>
      <admin-footer></admin-footer>
    </div>
  </body>
</html>
