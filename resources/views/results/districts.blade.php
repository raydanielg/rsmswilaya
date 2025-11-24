<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>RSMS • Results • {{ $examTitle }} • {{ $year }} • {{ $regionName }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full">
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
            <span>{{ ucfirst(strtolower($regionName)) }}</span>
          </nav>
        </div>
      </section>

      <main class="px-4 md:px-8 py-8 flex-1">
        <div class="max-w-screen-xl mx-auto">
          <h2 class="text-sm font-semibold text-[#6b6a67] mb-3">Select District</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($districts as $d)
              <a href="/results/{{ strtolower($examTitle) }}/{{ $year }}/{{ urlencode(strtolower($regionName)) }}/{{ urlencode(strtolower($d->name)) }}"
                 class="block bg-white border border-[#ecebea] rounded-lg px-4 py-4 hover:bg-[#f7f7f6] hover:border-[#0AA74A] transition-all duration-200 group">
                <div class="flex items-center gap-3">
                  <div class="h-10 w-10 rounded-full bg-gradient-to-br from-[#0AA74A] to-[#0B6B3A] flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                      <path d="M15 11V5l-3-3-3 3v2H3v14h18V11h-6zm-8 8H5v-2h2v2zm0-4H5v-2h2v2zm0-4H5V9h2v2zm6 8h-2v-2h2v2zm0-4h-2v-2h2v2zm0-4h-2V9h2v2zm6 8h-2v-2h2v2zm0-4h-2v-2h2v2z"/>
                    </svg>
                  </div>
                  <div class="font-medium text-[#1b1b18] group-hover:text-[#0B6B3A] transition-colors duration-200">
                    {{ ucfirst(strtolower($d->name)) }}
                  </div>
                </div>
              </a>
            @endforeach
          </div>
        </div>
      </main>

      <footer-section></footer-section>
    </div>
  </body>
</html>
