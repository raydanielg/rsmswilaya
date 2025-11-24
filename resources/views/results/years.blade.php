<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>RSMS • Results • {{ $examTitle }}</title>
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
            <span>Results</span>
          </nav>
        </div>
      </section>

      <main class="px-4 md:px-8 py-8 flex-1">
        <div class="max-w-screen-xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          @foreach($years as $y)
            <a href="/results/{{ strtolower($examTitle) }}/{{ $y }}" class="block bg-white rounded-xl border border-[#ecebea] p-6 hover:shadow-xl transition-all duration-300 hover:border-[#0AA74A] hover:scale-105 group relative overflow-hidden">
              <!-- Background decoration -->
              <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-[#0AA74A]/10 to-[#0B6B3A]/10 rounded-full -mr-10 -mt-10"></div>
              
              <!-- Icon container -->
              <div class="relative h-24 w-24 rounded-2xl bg-gradient-to-br from-[#0AA74A] to-[#0B6B3A] flex items-center justify-center mb-6 mx-auto group-hover:rotate-6 transition-transform duration-300 shadow-lg">
                <svg class="w-12 h-12 text-white" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                  <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                </svg>
                <!-- Small decoration -->
                <div class="absolute -top-1 -right-1 w-3 h-3 bg-white rounded-full opacity-80"></div>
              </div>
              
              <!-- Year display -->
              <div class="text-center">
                <div class="text-3xl font-bold text-[#1b1b18] mb-3 group-hover:text-[#0B6B3A] transition-colors duration-300">
                  {{ $y }}
                </div>
                
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 bg-[#0AA74A]/10 text-[#0B6B3A] px-3 py-1.5 rounded-full text-sm font-medium mb-3">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                  </svg>
                  {{ $examTitle }} Results
                </div>
                
                <!-- Action text -->
                <div class="text-sm text-[#6b6a67] group-hover:text-[#0B6B3A] transition-colors duration-300 flex items-center justify-center gap-1">
                  Click to view
                  <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 18l6-6-6-6"/>
                  </svg>
                </div>
              </div>
              
              <!-- Hover gradient overlay -->
              <div class="absolute inset-0 bg-gradient-to-t from-[#0AA74A]/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </a>
          @endforeach
        </div>
      </main>

      <footer-section></footer-section>
    </div>
  </body>
</html>
