<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Settings • RSMS</title>
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
          <div class="max-w-screen-lg mx-auto space-y-6">
            @if(session('status'))
              <div class="p-3 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm">{{ session('status') }}</div>
            @endif

            <div class="bg-white rounded-2xl border border-[#e3e2df] shadow-sm px-5 md:px-6 pt-4 pb-3 flex items-center justify-between gap-3 flex-wrap">
              <div>
                <h1 class="text-xl md:text-2xl font-semibold">Site settings</h1>
                <p class="text-sm text-[#6b6a67] mt-1">Control the name, branding and maintenance mode for RSMS.</p>
              </div>
            </div>

            <form method="POST" action="{{ route('admin.settings.save') }}" enctype="multipart/form-data" class="space-y-5">
              @csrf
              <div class="bg-white rounded-2xl border border-[#e3e2df] shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-[#ecebea]"><div class="text-sm font-semibold text-[#4a4946]">General</div></div>
                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium mb-1">Site name</label>
                    <input type="text" name="site_name" class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" value="{{ $settings['site_name'] }}" />
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-1">Favicon</label>
                    <input type="file" name="favicon" accept=".png,.ico" />
                    <div class="text-xs text-gray-500 mt-1">or URL</div>
                    <input type="url" name="favicon_url" class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" placeholder="https://.../favicon.png" />
                    @if(!empty($settings['favicon']))
                      <div class="mt-2 text-xs text-gray-600">Current: {{ $settings['favicon'] }}</div>
                    @endif
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-1">Hero image</label>
                    <input type="file" name="hero" accept="image/*" />
                    <div class="text-xs text-gray-500 mt-1">or URL</div>
                    <input type="url" name="hero_url" class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" placeholder="https://.../hero.jpg" />
                    @if(!empty($settings['hero']))
                      <div class="mt-2 text-xs text-gray-600">Current: {{ $settings['hero'] }}</div>
                    @endif
                  </div>
                </div>
              </div>

              <div class="bg-white rounded-2xl border border-[#e3e2df] shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-[#ecebea]"><div class="text-sm font-semibold text-[#4a4946]">Maintenance</div></div>
                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="flex items-center gap-2">
                    <input type="checkbox" id="maint_enabled" name="maint_enabled" value="1" @checked($settings['maint_enabled']) />
                    <label for="maint_enabled" class="text-sm">Enable maintenance mode</label>
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-1">Planned end (ISO or local datetime)</label>
                    <input type="text" name="maint_ends" class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" placeholder="2025-12-31T23:59:59Z" value="{{ $settings['maint_ends'] }}" />
                  </div>
                  <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Message</label>
                    <textarea name="maint_message" rows="2" class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" placeholder="We are improving our systems...">{{ $settings['maint_message'] }}</textarea>
                  </div>
                </div>
              </div>

              <div class="bg-white rounded-2xl border border-[#e3e2df] shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-[#ecebea]"><div class="text-sm font-semibold text-[#4a4946]">Admins & backup</div></div>
                <div class="p-5 flex flex-wrap gap-3 text-sm">
                  <a href="/admin/users" class="px-3 py-2 rounded-lg border border-[#e5e4e2] hover:bg-[#f7f7f6]">Manage Admins</a>
                  <a href="{{ route('admin.devices.index') }}" class="px-3 py-2 rounded-lg border border-[#e5e4e2] hover:bg-[#f7f7f6]">Manage Devices</a>
                  <button type="button" class="px-3 py-2 rounded-lg border border-dashed border-[#e5e4e2] text-[#8a8986] cursor-not-allowed" disabled>Download DB Backup (coming soon)</button>
                </div>
              </div>

              <div class="flex justify-end gap-3">
                <a href="/admin" class="px-4 py-2 rounded border border-[#e5e4e2] bg-white hover:bg-[#f7f7f6]">Cancel</a>
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
