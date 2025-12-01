<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="admin-name" content="{{ session('admin_name') }}" />
    <title>Admin Users • RSMS</title>
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
          <div class="max-w-screen-xl mx-auto space-y-6">
            <div class="bg-white rounded-2xl border border-[#e3e2df] shadow-sm px-5 md:px-6 pt-4 pb-3 flex items-center justify-between gap-3 flex-wrap">
              <div>
                <h1 class="text-xl md:text-2xl font-semibold">Admin users</h1>
                <p class="text-sm text-[#6b6a67] mt-1">Manage who can sign in to the RSMS admin dashboard.</p>
              </div>
              <button type="button" id="openAddAdminModal" class="px-4 py-2 rounded-lg bg-[#0AA74A] text-white hover:bg-[#089543] text-sm">+ Add admin</button>
            </div>

            <div class="rounded-2xl border border-[#ecebea] bg-white overflow-hidden shadow-sm">
              <table class="w-full text-left text-sm">
                <thead class="bg-[#f7f7f6] text-[#6b6a67] uppercase tracking-wide text-[11px]">
                  <tr>
                    <th class="px-4 py-2 font-medium">Name</th>
                    <th class="px-4 py-2 font-medium">Email</th>
                    <th class="px-4 py-2 font-medium">Created</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-[#f0efed]">
                  @forelse($admins as $a)
                    <tr class="hover:bg-[#fafafa] transition-colors">
                      <td class="px-4 py-2.5 align-top">
                        <div class="font-medium text-[#0B6B3A]">{{ $a->name }}</div>
                      </td>
                      <td class="px-4 py-2.5 align-top text-xs text-[#4a4946]">{{ $a->email }}</td>
                      <td class="px-4 py-2.5 align-top text-xs text-[#6b6a67]">{{ \Carbon\Carbon::parse($a->created_at)->format('M d, Y') }}</td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="3" class="px-4 py-6 text-center text-sm text-[#6b6a67]">No admin users have been created yet.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>

          <!-- Add Admin Modal -->
          <div id="addAdminModal" class="hidden fixed inset-0 z-50">
            <div class="absolute inset-0 bg-black/40" id="addAdminBackdrop"></div>
            <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-lg w-[95vw] max-w-lg border border-[#e3e2df]">
              <div class="px-5 py-3 border-b border-[#ecebea] flex items-center justify-between gap-3">
                <div>
                  <div class="text-sm font-semibold text-[#4a4946]">Add admin</div>
                  <div class="text-xs text-[#8a8986] mt-0.5">Create a new admin account and set their login password.</div>
                </div>
                <button type="button" id="closeAddAdminModal" class="text-[#6b6a67] hover:text-[#0B6B3A] text-lg leading-none">✕</button>
              </div>
              <form method="POST" action="{{ route('admin.users.store') }}" class="p-5 space-y-4">
                @csrf
                <div>
                  <label class="block text-sm font-medium mb-1">Name</label>
                  <input type="text" name="name" required class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" />
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Email</label>
                  <input type="email" name="email" required class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium mb-1">Password</label>
                    <input type="password" name="password" required class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" />
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" />
                  </div>
                </div>
                <div class="flex justify-end gap-3 mt-2">
                  <button type="button" id="cancelAddAdminModal" class="px-4 py-2 rounded border border-[#e5e4e2] bg-white hover:bg-[#f7f7f6]">Cancel</button>
                  <button class="px-4 py-2 rounded bg-[#0B6B3A] text-white hover:bg-[#095a31]">Save</button>
                </div>
              </form>
            </div>
          </div>
        </main>
      </div>
      <admin-footer></admin-footer>
    </div>
    <script>
      window.sidebarOpen = false;
      document.addEventListener('DOMContentLoaded', function(){
        const modal = document.getElementById('addAdminModal');
        const openBtn = document.getElementById('openAddAdminModal');
        const closeBtn = document.getElementById('closeAddAdminModal');
        const cancelBtn = document.getElementById('cancelAddAdminModal');
        const backdrop = document.getElementById('addAdminBackdrop');
        if (!modal) return;
        const open = () => modal.classList.remove('hidden');
        const close = () => modal.classList.add('hidden');
        openBtn && openBtn.addEventListener('click', open);
        closeBtn && closeBtn.addEventListener('click', close);
        cancelBtn && cancelBtn.addEventListener('click', close);
        backdrop && backdrop.addEventListener('click', close);
        document.addEventListener('keydown', (e) => {
          if (e.key === 'Escape') close();
        });
      });
    </script>
  </body>
</html>
