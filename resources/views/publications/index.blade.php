<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Publications • RSMS</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-white text-[#1b1b18] min-h-screen w-full">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <app-header></app-header>

      <!-- Title bar -->
      <section class="bg-[#0AA74A] text-white">
        <div class="px-4 md:px-8 py-5 flex items-center justify-between">
          <h1 class="text-2xl font-semibold">Publications</h1>
          <nav class="text-sm opacity-95">
            <a href="/" class="hover:underline">Home</a>
            <span class="mx-2">/</span>
            <span>Publications</span>
          </nav>
        </div>
      </section>

      <main class="px-4 md:px-8 py-8 flex-1">
        <div class="max-w-screen-xl mx-auto">
          <h2 class="text-xs font-semibold text-[#6b6a67] mb-3">PUBLICATIONS</h2>
          <ul class="divide-y divide-[#ecebea] bg-white rounded-md border border-[#ecebea]">
            @foreach($folders as $folder)
              <li>
                <a href="/publications/{{ $folder->slug }}" class="flex items-center gap-3 px-4 py-3 hover:bg-[#f7f7f6]">
                  <svg class="w-5 h-5 text-[#f4b400]" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M2 8a2 2 0 0 1 2-2h5l2 2h9a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8Z"/></svg>
                  <span class="font-medium">{{ $folder->name }}</span>
                </a>
              </li>
            @endforeach
          </ul>
        </div>
      </main>

      <footer-section></footer-section>
    </div>
  </body>
</html>
