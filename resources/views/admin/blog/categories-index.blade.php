<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="admin-name" content="{{ session('admin_name') }}" />
    <title>Blog Categories • RSMS</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <admin-header @toggle-sidebar="sidebarOpen = !sidebarOpen"></admin-header>
      <div class="flex flex-1 min-h-0">
        <admin-sidebar :open="sidebarOpen" active="blogs"></admin-sidebar>
        <main class="flex-1 min-w-0 p-6 md:p-8">
          <div class="max-w-screen-xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
              <h1 class="text-2xl font-semibold">Blog Categories</h1>
              <button type="button" id="openCatModal" class="px-3 py-2 rounded-lg text-white bg-primary-700 hover:bg-primary-800 text-sm">New Category</button>
            </div>

            <div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden">
              <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                  <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                      <th class="px-4 py-2">Name</th>
                      <th class="px-4 py-2">Slug</th>
                      <th class="px-4 py-2 text-right">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($categories as $c)
                      <tr class="hover:bg-gray-100">
                        <td class="px-4 py-2 text-gray-900 font-medium">{{ $c->name }}</td>
                        <td class="px-4 py-2 text-sm">{{ $c->slug }}</td>
                        <td class="px-4 py-2 text-right">
                          <button class="text-sm px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 mr-2" data-edit-id="{{ $c->id }}" data-edit-name="{{ $c->name }}">Edit</button>
                          <form method="POST" action="{{ route('admin.blog.categories.delete', $c->id) }}" class="inline">
                            @csrf
                            <button class="text-sm px-3 py-1.5 rounded-lg bg-red-600 text-white hover:bg-red-700" onclick="return confirm('Delete this category? Posts will be uncategorized.')">Delete</button>
                          </form>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td class="px-4 py-6 text-sm text-gray-500" colspan="3">No categories yet.</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Add Category Modal -->
          <div id="catModal" class="hidden fixed inset-0 z-50">
            <div class="absolute inset-0 bg-black/40" id="catBackdrop"></div>
            <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg shadow-md w-[95vw] max-w-md border border-gray-200">
              <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                <div class="font-semibold text-[#0B6B3A]">New Category</div>
                <button type="button" id="closeCatModal" class="text-[#6b6a67] hover:text-[#0B6B3A]">✕</button>
              </div>
              <form method="POST" action="{{ route('admin.blog.categories.store') }}" class="p-4 space-y-4">
                @csrf
                <div>
                  <label class="block text-sm font-medium mb-1">Name</label>
                  <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Announcements" />
                </div>
                <div class="flex justify-end gap-3">
                  <button type="button" id="cancelCatModal" class="px-4 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-100">Cancel</button>
                  <button class="px-4 py-2 rounded-lg text-white bg-primary-700 hover:bg-primary-800">Save</button>
                </div>
              </form>
            </div>
          </div>

          <!-- Edit Category Modal -->
          <div id="editCatModal" class="hidden fixed inset-0 z-50">
            <div class="absolute inset-0 bg-black/40" id="editCatBackdrop"></div>
            <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg shadow-md w-[95vw] max-w-md border border-gray-200">
              <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                <div class="font-semibold text-[#0B6B3A]">Edit Category</div>
                <button type="button" id="closeEditCatModal" class="text-[#6b6a67] hover:text-[#0B6B3A]">✕</button>
              </div>
              <form id="editCatForm" method="POST" class="p-4 space-y-4">
                @csrf
                <div>
                  <label class="block text-sm font-medium mb-1">Name</label>
                  <input id="editCatName" type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                </div>
                <div class="flex justify-end gap-3">
                  <button type="button" id="cancelEditCatModal" class="px-4 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-100">Cancel</button>
                  <button class="px-4 py-2 rounded-lg text-white bg-primary-700 hover:bg-primary-800">Save</button>
                </div>
              </form>
            </div>
          </div>
        </main>
      </div>
      <admin-footer></admin-footer>
    </div>
    <script>
      document.addEventListener('DOMContentLoaded', function(){
        function wire(modalId, openBtnId, closeBtnId, cancelBtnId, backdropId){
          const m = document.getElementById(modalId);
          const o = document.getElementById(openBtnId);
          const c = document.getElementById(closeBtnId);
          const x = document.getElementById(cancelBtnId);
          const b = document.getElementById(backdropId);
          if (!m) return;
          const open = ()=> m.classList.remove('hidden');
          const close = ()=> m.classList.add('hidden');
          o && o.addEventListener('click', open);
          c && c.addEventListener('click', close);
          x && x.addEventListener('click', close);
          b && b.addEventListener('click', close);
          document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') close(); });
        }
        wire('catModal','openCatModal','closeCatModal','cancelCatModal','catBackdrop');
        // Edit buttons
        const editModal = document.getElementById('editCatModal');
        const editName = document.getElementById('editCatName');
        const editBackdrop = document.getElementById('editCatBackdrop');
        const closeEdit = document.getElementById('closeEditCatModal');
        const cancelEdit = document.getElementById('cancelEditCatModal');
        const form = document.getElementById('editCatForm');
        function hideEdit(){ editModal.classList.add('hidden'); }
        function showEdit(){ editModal.classList.remove('hidden'); }
        document.querySelectorAll('[data-edit-id]').forEach(btn => {
          btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-edit-id');
            const name = btn.getAttribute('data-edit-name');
            editName.value = name;
            form.action = `/admin/blog/categories/${id}`;
            showEdit();
          });
        });
        closeEdit?.addEventListener('click', hideEdit);
        cancelEdit?.addEventListener('click', hideEdit);
        editBackdrop?.addEventListener('click', hideEdit);
        document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') hideEdit(); });
      });
    </script>
  </body>
</html>
