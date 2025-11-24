<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="admin-name" content="{{ session('admin_name') }}" />
    <title>My Profile • RSMS Admin</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <admin-header @toggle-sidebar="sidebarOpen = !sidebarOpen"></admin-header>
      <div class="flex flex-1 min-h-0">
        <admin-sidebar :open="sidebarOpen" active="settings"></admin-sidebar>
        <main class="flex-1 min-w-0 p-6">
          <div class="max-w-screen-md mx-auto bg-white rounded-md border border-[#ecebea] p-6">
            <div class="flex items-center justify-between mb-4">
              <h1 class="text-2xl font-semibold">My Profile</h1>
              <a href="/admin" class="text-sm text-[#0B6B3A] hover:underline">← Back to admin</a>
            </div>
            @if(session('status'))
              <div class="p-3 rounded bg-green-50 border border-green-200 text-green-800 text-sm mb-4">{{ session('status') }}</div>
            @endif
            <form method="POST" action="{{ route('admin.profile.save') }}" class="space-y-5">
              @csrf
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium mb-1">Display name</label>
                  <input type="text" name="name" class="w-full border rounded px-3 py-2" value="{{ old('name', $profile['name']) }}" />
                  @error('name')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Email (optional)</label>
                  <input type="email" name="email" class="w-full border rounded px-3 py-2" value="{{ old('email', $profile['email']) }}" />
                  @error('email')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>
              </div>

              <div class="border-t pt-4">
                <div class="font-medium mb-2">Change password</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium mb-1">Current password</label>
                    <input type="password" name="current_password" class="w-full border rounded px-3 py-2" />
                    @error('current_password')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-1">New password</label>
                    <input type="password" name="password" class="w-full border rounded px-3 py-2" />
                    @error('password')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-1">Confirm new password</label>
                    <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2" />
                  </div>
                </div>
              </div>

              <div class="flex justify-end gap-3">
                <a href="/admin" class="px-4 py-2 rounded border">Cancel</a>
                <button class="px-4 py-2 rounded bg-[#0B6B3A] text-white hover:bg-[#095a31]">Save Changes</button>
              </div>
            </form>
          </div>
        </main>
      </div>
      <admin-footer></admin-footer>
    </div>
  </body>
</html>
