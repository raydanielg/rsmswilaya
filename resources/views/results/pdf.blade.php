<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>RSMS • Results • {{ $examTitle }} • {{ $year }} • {{ $schoolName }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-white text-[#1b1b18] min-h-screen w-full">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <app-header></app-header>

      <section class="bg-[#0AA74A] text-white">
        <div class="px-4 md:px-8 py-5 flex items-center justify-between">
          <h1 class="text-2xl font-semibold">Results - {{ $examTitle }}</h1>
          <nav class="text-sm opacity-95">
            <a href="/" class="hover:underline">Home</a>
            <span class="mx-2">/</span>
            <a href="/results/{{ strtolower($examTitle) }}" class="hover:underline">Results</a>
            <span class="mx-2">/</span>
            <a href="/results/{{ strtolower($examTitle) }}/{{ $year }}" class="hover:underline">{{ $year }}</a>
            <span class="mx-2">/</span>
            <span>{{ ucfirst(strtolower($schoolName)) }}</span>
          </nav>
        </div>
      </section>

      <main class="px-4 md:px-8 py-4 flex-1">
        <div class="max-w-screen-xl mx-auto">
          <div class="flex justify-end mb-2">
            <a href="{{ $fileUrl }}" download class="inline-flex items-center gap-2 px-4 py-2 rounded bg-[#0B6B3A] text-white hover:bg-[#095a31]">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 3v12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M8 11l4 4 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 21h16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
              Download
            </a>
          </div>
          <iframe src="{{ $fileUrl }}" class="w-full h-[80vh] border" title="Results PDF"></iframe>
        </div>
      </main>

      <footer-section></footer-section>
    </div>
  </body>
</html>
