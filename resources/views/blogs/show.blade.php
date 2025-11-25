<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>RSMS • {{ $post['title'] }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-white text-[#1b1b18] min-h-screen w-full">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <app-header></app-header>

      <section class="bg-[#0AA74A] text-white">
        <div class="px-4 md:px-8 py-5 flex items-center justify-between">
          <h1 class="text-2xl font-semibold">Blog</h1>
          <nav class="text-sm opacity-95">
            <a href="/" class="hover:underline">Home</a>
            <span class="mx-2">/</span>
            <a href="/blogs" class="hover:underline">Blogs</a>
            <span class="mx-2">/</span>
            <span>{{ $post['title'] }}</span>
          </nav>
        </div>
      </section>

      <main class="px-4 md:px-8 py-8 flex-1">
        <div class="max-w-screen-xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
          <article class="lg:col-span-8">
            @if(!empty($post['image']))
              <img src="{{ $post['image'] }}" alt="{{ $post['title'] }}" class="w-full h-72 object-cover rounded border border-[#ecebea]" />
            @else
              <div class="w-full h-72 rounded border border-[#ecebea] bg-[#f7f7f6] flex items-center justify-center text-[#6b6a67] text-sm">No image</div>
            @endif
            <div class="mt-4 text-xs text-[#6b6a67]">{{ \Carbon\Carbon::parse($post['date'])->format('M d, Y') }}</div>
            <h1 class="text-3xl font-bold mt-1">{{ $post['title'] }}</h1>
            <div class="prose max-w-none mt-4">
              <p>{{ $post['content'] }}</p>
            </div>
          </article>

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
