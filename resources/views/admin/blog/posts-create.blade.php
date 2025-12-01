<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="admin-name" content="{{ session('admin_name') }}" />
    <title>New Blog Post • RSMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full" style="font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <admin-header @toggle-sidebar="sidebarOpen = !sidebarOpen"></admin-header>
      <div class="flex flex-1 min-h-0">
        <admin-sidebar :open="sidebarOpen" active="blogs"></admin-sidebar>
        <main class="flex-1 min-w-0 p-4 md:p-8">
          <div class="max-w-screen-lg mx-auto space-y-5">
            <div class="bg-white rounded-2xl border border-[#e3e2df] shadow-sm px-5 md:px-6 pt-4 pb-3 flex items-center justify-between gap-3 flex-wrap">
              <div>
                <h1 class="text-xl md:text-2xl font-semibold">New Blog Post</h1>
                <p class="text-sm text-[#6b6a67] mt-1">Create a new article for the RSMS blog.</p>
              </div>
              <a href="{{ route('admin.blog.index') }}" class="text-sm text-[#0B6B3A] hover:underline">← Back to posts</a>
            </div>

            <div class="bg-white rounded-2xl border border-[#e3e2df] shadow-sm p-5 md:p-6">
            <form method="POST" action="{{ route('admin.blog.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
              <label class="block text-sm font-medium mb-1">Title</label>
              <input type="text" name="title" required class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" />
              @error('title')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <!-- Permalink -->
            <div>
              <label class="block text-sm font-medium mb-1">Permalink</label>
              <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">{{ rtrim(config('app.url'), '/') }}/blogs/</span>
                <input id="slugInput" type="text" name="slug" class="border border-[#e5e4e2] rounded px-3 py-2 text-sm w-64" placeholder="your-post-slug" />
              </div>
              <div class="text-xs text-gray-500 mt-1">Preview: <span id="permalinkPreview" class="font-mono">{{ rtrim(config('app.url'), '/') }}/blogs/</span></div>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Category</label>
              <select name="category_id" class="w-full border border-[#e5e4e2] rounded px-3 py-2">
                <option value="">-- None --</option>
                @foreach($categories as $c)
                  <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Excerpt</label>
              <textarea name="excerpt" rows="2" class="w-full border border-[#e5e4e2] rounded px-3 py-2" placeholder="Short summary..."></textarea>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Content</label>
              <textarea name="content" rows="12" class="w-full border border-[#e5e4e2] rounded px-3 py-2" placeholder="Write your post content here..."></textarea>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Cover image</label>
              <input type="file" name="image" accept="image/*" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Published at (optional)</label>
              <input type="date" name="published_at" class="border border-[#e5e4e2] rounded px-3 py-2" />
            </div>
            <div class="flex flex-wrap gap-3 justify-end">
              <a href="{{ route('admin.blog.index') }}" class="px-4 py-2 rounded border">Cancel</a>
              <button name="status" value="draft" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Save as Draft</button>
              <button name="status" value="published" class="px-4 py-2 rounded bg-[#0B6B3A] text-white hover:bg-[#095a31]">Publish</button>
            </div>
            </form>
          </div>
        </main>
      </div>
      <admin-footer></admin-footer>
    </div>
    <script>
      window.sidebarOpen = false;
      (function(){
        const base = '{{ rtrim(config('app.url'), '/') }}/blogs/';
        const slug = document.getElementById('slugInput');
        const prev = document.getElementById('permalinkPreview');
        if (slug && prev) {
          const upd = ()=> prev.textContent = base + (slug.value||'');
          slug.addEventListener('input', upd); upd();
          const titleInput = document.querySelector('input[name="title"]');
          if (titleInput) {
            titleInput.addEventListener('input', function(){
              if (!slug.value) {
                const raw = (titleInput.value || '').toLowerCase();
                const generated = raw
                  .trim()
                  .replace(/[^a-z0-9\s-]/g, '')
                  .replace(/\s+/g, '-')
                  .replace(/-+/g, '-');
                slug.value = generated;
                upd();
              }
            });
          }
        }
        const toggleBtn = document.getElementById('toggleSeo');
        const seoBody = document.getElementById('seoBody');
        toggleBtn && toggleBtn.addEventListener('click', ()=> seoBody && seoBody.classList.toggle('hidden'));
      })();
    </script>
  </body>
</html>
