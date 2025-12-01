<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="admin-name" content="{{ session('admin_name') }}" />
    <title>Publications • RSMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full" style="font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <admin-header @toggle-sidebar="sidebarOpen = !sidebarOpen"></admin-header>
      <div class="flex flex-1 min-h-0">
        <admin-sidebar :open="sidebarOpen" active="publications"></admin-sidebar>
        <main class="flex-1 min-w-0 p-6 md:p-8">
          <div class="max-w-screen-xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
              <h1 class="text-2xl font-semibold">Publications</h1>
              <div class="flex gap-2">
                <button type="button" id="openFolderModal" class="px-3 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 text-sm">New Folder</button>
                <button type="button" id="openDocModal" class="px-3 py-2 rounded-lg text-white bg-primary-700 hover:bg-primary-800 text-sm">Add Document</button>
              </div>
            </div>
            @if(session('success'))
              <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
              </div>
            @endif
            <div class="border-t border-dashed border-[#d7d6d4]"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
              @forelse($folders as $f)
                <a href="{{ route('admin.pub.folder', $f->id) }}" class="group rounded-lg border border-[#ecebea] bg-white p-4 shadow-sm hover:shadow transition">
                  <div class="flex items-center justify-between">
                    <div class="text-base font-semibold text-[#0B6B3A]">{{ $f->name }}</div>
                    <span class="inline-flex items-center justify-center h-7 min-w-[2.5rem] px-2 rounded-full bg-primary-50 text-primary-700 text-sm font-semibold">{{ number_format($f->docs_count) }}</span>
                  </div>
                  <div class="mt-2 text-xs text-[#6b6a67]">Folder</div>
                </a>
              @empty
                <div class="text-sm text-[#6b6a67]">No folders yet. Create one to start adding publications.</div>
              @endforelse
            </div>
          </div>
          <!-- New Folder Modal -->
          <div id="folderModal" class="hidden fixed inset-0 z-50">
            <div class="absolute inset-0 bg-black/40" id="folderBackdrop"></div>
            <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg shadow-md w-[95vw] max-w-md border border-gray-200">
              <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                <div class="font-semibold text-[#0B6B3A]">Create Folder</div>
                <button type="button" id="closeFolderModal" class="text-[#6b6a67] hover:text-[#0B6B3A]">✕</button>
              </div>
              <form method="POST" action="{{ route('admin.pub.folders.store') }}" class="p-4 space-y-4">
                @csrf
                <div>
                  <label class="block text-sm font-medium mb-1">Folder name</label>
                  <input type="text" name="name" required maxlength="160" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Approved Exam Formats" />
                  <p class="text-xs text-gray-500 mt-1">Max 160 characters. Will be automatically converted to a URL-friendly slug.</p>
                </div>
                <div class="flex justify-end gap-3">
                  <button type="button" id="cancelFolderModal" class="px-4 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-100">Cancel</button>
                  <button type="submit" class="px-4 py-2 rounded-lg text-white bg-primary-700 hover:bg-primary-800">Create Folder</button>
                </div>
              </form>
            </div>
          </div>

          <!-- Add Document Modal -->
          <div id="docModal" class="hidden fixed inset-0 z-50">
            <div class="absolute inset-0 bg-black/40" id="docBackdrop"></div>
            <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg shadow-md w-[95vw] max-w-md border border-gray-200">
              <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                <div class="font-semibold text-[#0B6B3A]">Add Document</div>
                <button type="button" id="closeDocModal" class="text-[#6b6a67] hover:text-[#0B6B3A]">✕</button>
              </div>
              <form method="POST" action="{{ route('admin.pub.store') }}" enctype="multipart/form-data" class="p-4 space-y-4">
                @csrf
                <div>
                  <label class="block text-sm font-medium mb-1">Folder</label>
                  @php($allFolders = DB::table('publication_folders')->orderBy('name')->get())
                  <select name="folder_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50">
                    <option value="">-- Select Folder --</option>
                    @foreach($allFolders as $af)
                      <option value="{{ $af->id }}">{{ $af->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Title</label>
                  <input type="text" name="title" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">File</label>
                  <input type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                  <p class="text-xs text-gray-500 mt-1">Supported formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT (Max 10MB)</p>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Published at (optional)</label>
                  <input type="date" name="published_at" class="border border-gray-300 rounded-lg px-3 py-2" />
                </div>
                <div class="flex justify-end gap-3">
                  <button type="button" id="cancelDocModal" class="px-4 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-100">Cancel</button>
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
      window.sidebarOpen = false;
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
          x && o && x.addEventListener('click', close);
          b && b.addEventListener('click', close);
          document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') close(); });
        }
        wire('folderModal','openFolderModal','closeFolderModal','cancelFolderModal','folderBackdrop');
        wire('docModal','openDocModal','closeDocModal','cancelDocModal','docBackdrop');

        // File upload validation
        const fileInput = document.querySelector('input[name="file"]');
        const form = document.querySelector('form[action*="admin.pub.store"]');
        
        if (fileInput && form) {
          fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
              // Check file size (10MB max)
              const maxSize = 10 * 1024 * 1024;
              if (file.size > maxSize) {
                alert('File size must be less than 10MB');
                e.target.value = '';
                return;
              }
              
              // Check file type
              const allowedTypes = ['.pdf', '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx', '.txt'];
              const fileName = file.name.toLowerCase();
              const isValidType = allowedTypes.some(type => fileName.endsWith(type));
              
              if (!isValidType) {
                alert('Invalid file type. Please upload PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, or TXT files.');
                e.target.value = '';
                return;
              }
            }
          });

          // Show upload progress
          form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="inline-flex items-center gap-2"><svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" opacity="0.3"/><path d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" fill="currentColor"/></svg>Uploading...</span>';
            
            // Reset after 10 seconds (in case of error)
            setTimeout(() => {
              submitBtn.disabled = false;
              submitBtn.textContent = originalText;
            }, 10000);
          });
        }
      });
    </script>
  </body>
</html>
