<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="admin-name" content="{{ session('admin_name') }}" />
    <title>Blogs • RSMS</title>
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
        <main class="flex-1 min-w-0 p-6 md:p-8">
          <div class="max-w-screen-xl mx-auto space-y-6">
            <div class="bg-white rounded-2xl border border-[#e3e2df] shadow-sm px-5 md:px-6 pt-4 pb-3">
              <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-col gap-1">
                  <h1 class="text-xl md:text-2xl font-semibold">Blog Posts</h1>
                  <p class="text-sm text-[#6b6a67]">Manage and publish updates for the RSMS blog.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                  <div class="relative">
                    <input id="search" type="text" placeholder="Search posts" class="pl-10 pr-3 py-2 text-sm border border-gray-300 rounded-full bg-gray-50 w-56 md:w-64 focus:ring-primary-500 focus:border-primary-500" />
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                  </div>
                  <a href="{{ route('admin.blog.categories.create') }}" class="px-3 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-100 text-xs md:text-sm">New Category</a>
                  <a href="{{ route('admin.blog.create') }}" class="px-4 py-2 rounded-full text-white bg-primary-700 hover:bg-primary-800 text-xs md:text-sm">New Post</a>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-2xl border border-[#e3e2df] shadow-sm p-0">
              <div class="border border-[#f0efed] rounded-2xl overflow-x-auto">
                <table class="w-full text-xs md:text-sm text-left text-gray-500 bg-white">
                  <thead>
                    <tr>
                      <th class="px-4 py-2 bg-[#f7f7f6] text-[11px] uppercase tracking-wide text-[#6b6a67] border-b border-[#ecebea]">Title</th>
                      <th class="px-4 py-2 bg-[#f7f7f6] text-[11px] uppercase tracking-wide text-[#6b6a67] border-b border-[#ecebea]">Category</th>
                      <th class="px-4 py-2 bg-[#f7f7f6] text-[11px] uppercase tracking-wide text-[#6b6a67] border-b border-[#ecebea]">Published</th>
                      <th class="px-4 py-2 bg-[#f7f7f6] text-[11px] uppercase tracking-wide text-[#6b6a67] border-b border-[#ecebea]">Preview</th>
                      <th class="px-4 py-2 bg-[#f7f7f6] text-[11px] uppercase tracking-wide text-[#6b6a67] border-b border-[#ecebea] text-right">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="postsBody">
                    @forelse($posts as $p)
                      <tr class="hover:bg-[#f9f8f6]">
                        <td class="px-4 py-2 text-gray-900 font-medium">{{ $p->title }}</td>
                        <td class="px-4 py-2 text-xs md:text-sm">{{ optional(DB::table('blog_categories')->where('id',$p->category_id)->first())->name ?? '—' }}</td>
                        <td class="px-4 py-2 text-xs md:text-sm">{{ optional($p->published_at ?? $p->created_at)->format('M d, Y') }}</td>
                        <td class="px-4 py-2 text-xs md:text-sm">
                          <a href="/blogs/{{ $p->slug }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full border border-gray-200 bg-white hover:bg-gray-100 text-xs md:text-sm">
                            <span>Open</span>
                          </a>
                        </td>
                        <td class="px-4 py-2 text-right">
                          <div class="inline-flex items-center gap-2">
                            <a href="{{ route('admin.blog.edit',$p->id) }}" class="inline-flex items-center gap-1 text-xs md:text-sm px-3 py-1.5 rounded-full border border-gray-200 bg-white hover:bg-gray-100">
                              <span>Edit</span>
                            </a>
                            <form method="POST" action="{{ route('admin.blog.delete',$p->id) }}" class="inline">
                              @csrf
                              <button class="inline-flex items-center gap-1 text-xs md:text-sm px-3 py-1.5 rounded-full bg-red-600 text-white hover:bg-red-700" onclick="return confirm('Delete this post?')">Delete</button>
                            </form>
                          </div>
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
