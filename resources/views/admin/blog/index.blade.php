<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="admin-name" content="{{ session('admin_name') }}" />
    <title>Blogs • RSMS</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <admin-header @toggle-sidebar="sidebarOpen = !sidebarOpen"></admin-header>
      <div class="flex flex-1 min-h-0">
        <admin-sidebar :open="sidebarOpen" active="blogs"></admin-sidebar>
        <main class="flex-1 min-w-0 p-6 md:p-8">
          <div class="max-w-screen-xl mx-auto space-y-6">
            <div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden p-4">
              <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                  <h1 class="text-2xl font-semibold">Blog Posts</h1>
                  <div class="relative">
                    <input id="search" type="text" placeholder="Search" class="pl-10 pr-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 w-64" />
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <a href="{{ route('admin.blog.categories.create') }}" class="px-3 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 text-sm">New Category</a>
                  <a href="{{ route('admin.blog.create') }}" class="px-4 py-2 rounded-lg text-white bg-primary-700 hover:bg-primary-800 text-sm">New Post</a>
                </div>
              </div>
            </div>

            <div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden">
              <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                  <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                      <th class="px-4 py-2">Title</th>
                      <th class="px-4 py-2">Category</th>
                      <th class="px-4 py-2">Published</th>
                      <th class="px-4 py-2">Preview</th>
                      <th class="px-4 py-2 text-right">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="postsBody">
                    @forelse($posts as $p)
                      <tr class="hover:bg-gray-100">
                        <td class="px-4 py-2 text-gray-900 font-medium">{{ $p->title }}</td>
                        <td class="px-4 py-2 text-sm">{{ optional(DB::table('blog_categories')->where('id',$p->category_id)->first())->name ?? '—' }}</td>
                        <td class="px-4 py-2 text-sm">{{ optional($p->published_at ?? $p->created_at)->format('M d, Y') }}</td>
                        <td class="px-4 py-2 text-sm">
                          <a href="/blogs/{{ $p->slug }}" target="_blank" class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-100">Open</a>
                        </td>
                        <td class="px-4 py-2 text-right">
                          <a href="{{ route('admin.blog.edit',$p->id) }}" class="text-sm px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 mr-2">Edit</a>
                          <form method="POST" action="{{ route('admin.blog.delete',$p->id) }}" class="inline">
                            @csrf
                            <button class="text-sm px-3 py-1.5 rounded-lg bg-red-600 text-white hover:bg-red-700" onclick="return confirm('Delete this post?')">Delete</button>
                          </form>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td class="px-4 py-6 text-sm text-gray-500" colspan="5">No blog posts yet.</td>
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
    <script>
      // Client-side search filter
      (function(){
        const q = document.getElementById('search');
        const body = document.getElementById('postsBody');
        if(!q || !body) return;
        q.addEventListener('input', function(){
          const term = (q.value||'').toLowerCase();
          Array.from(body.querySelectorAll('tr')).forEach(tr => {
            const text = tr.innerText.toLowerCase();
            tr.style.display = text.includes(term) ? '' : 'none';
          });
        });
      })();
    </script>
  </body>
</html>
