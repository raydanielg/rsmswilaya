<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Blogs • RSMS</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-white text-[#1b1b18] min-h-screen w-full">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <app-header></app-header>

      <!-- Title bar -->
      <section class="bg-[#0AA74A] text-white">
        <div class="px-4 md:px-8 py-5 flex items-center justify-between">
          <h1 class="text-2xl font-semibold">Blogs</h1>
          <nav class="text-sm opacity-95">
            <a href="/" class="hover:underline">Home</a>
            <span class="mx-2">/</span>
            <span>Blogs</span>
          </nav>
        </div>
      </section>

      <main class="px-4 md:px-8 py-8 flex-1">
        <div class="max-w-screen-xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
          <!-- Main posts -->
          <section class="lg:col-span-8 space-y-6">
            @foreach($posts as $post)
              <article class="bg-white rounded-lg border border-[#ecebea] overflow-hidden shadow-sm hover:shadow transition">
                <a href="/blogs/{{ $post['slug'] }}">
                  <img src="{{ $post['image'] }}" alt="{{ $post['title'] }}" class="w-full h-56 object-cover" />
                </a>
                <div class="p-5">
                  <div class="text-xs text-[#6b6a67]">{{ \Carbon\Carbon::parse($post['date'])->diffForHumans() }}</div>
                  <h2 class="mt-1 text-xl font-semibold"><a href="/blogs/{{ $post['slug'] }}" class="hover:underline">{{ $post['title'] }}</a></h2>
                  <p class="mt-2 text-[#4f4e4b]">{{ $post['excerpt'] }}</p>
                  <div class="mt-4">
                    <a href="/blogs/{{ $post['slug'] }}" class="inline-flex items-center px-4 py-2 rounded-md bg-[#0B6B3A] text-white hover:bg-[#095a31]">Read more</a>
                  </div>
                </div>
              </article>
            @endforeach

            <!-- Pagination placeholder -->
            <div class="flex justify-between items-center text-sm text-[#6b6a67]">
              <span>Showing {{ count($posts) }} posts</span>
              <div class="flex items-center gap-2">
                <a class="px-3 py-1 border rounded hover:bg-[#f7f7f6]" href="#">Prev</a>
                <a class="px-3 py-1 border rounded hover:bg-[#f7f7f6]" href="#">Next</a>
              </div>
            </div>
          </section>

          <!-- Sidebar -->
          <aside class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-lg border border-[#ecebea] p-5">
              <h3 class="text-lg font-semibold mb-3">Categories</h3>
              <ul class="space-y-2">
                @foreach($categories as $cat)
                  <li>
                    <a href="/blogs?category={{ $cat->slug }}" class="inline-flex items-center gap-2 hover:underline">
                      <span class="h-2 w-2 rounded-full bg-[#0B6B3A]"></span>
                      <span>{{ $cat->name }}</span>
                      <span class="text-[#6b6a67]">({{ $cat->count }})</span>
                    </a>
                  </li>
                @endforeach
              </ul>
            </div>
            <div class="bg-white rounded-lg border border-[#ecebea] p-5">
              <h3 class="text-lg font-semibold mb-3">Latest Posts</h3>
              <ul class="space-y-3">
                @foreach($latest as $p)
                  <li class="flex items-center gap-3">
                    <img src="{{ $p['image'] }}" alt="{{ $p['title'] }}" class="h-12 w-16 object-cover rounded" />
                    <div>
                      <a href="/blogs/{{ $p['slug'] }}" class="font-medium hover:underline">{{ $p['title'] }}</a>
                      <div class="text-xs text-[#6b6a67]">{{ \Carbon\Carbon::parse($p['date'])->format('M d, Y') }}</div>
                    </div>
                  </li>
                @endforeach
              </ul>
            </div>
          </aside>
        </div>
      </main>

      <footer-section></footer-section>
    </div>
  </body>
</html>
