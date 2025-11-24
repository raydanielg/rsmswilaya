<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>RSMS • Form Two National Assessment (FTNA)</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-white text-[#1b1b18] min-h-screen w-full">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <app-header></app-header>

      <section class="bg-[#0AA74A] text-white">
        <div class="px-4 md:px-8 py-5 flex items-center justify-between">
          <h1 class="text-2xl font-semibold">Form Two National Assessment</h1>
          <nav class="text-sm opacity-95">
            <a href="/" class="hover:underline">Home</a>
            <span class="mx-2">/</span>
            <span>FTNA</span>
          </nav>
        </div>
      </section>

      <main class="px-4 md:px-8 py-8 flex-1">
        <div class="max-w-screen-xl mx-auto">
          <div class="bg-white rounded-md border border-[#ecebea] p-6">
            <p class="text-[#4f4e4b]">FTNA monitors students' learning progress and provides actionable feedback for improvement at lower secondary level.</p>
            <h2 class="mt-6 text-xl font-semibold">Examination Formats</h2>
            <p class="text-[#4f4e4b] mt-2">Subject formats define paper structures and content coverage. View formats <a class="text-[#0B6B3A] underline" href="/publications/approved-exam-formats">here</a>.</p>
          </div>
        </div>
      </main>

      <footer-section></footer-section>
    </div>
  </body>
</html>
