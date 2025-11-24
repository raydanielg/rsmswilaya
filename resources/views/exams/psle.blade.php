<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>RSMS • Primary School Leaving Examination (PSLE)</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-white text-[#1b1b18] min-h-screen w-full">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <app-header></app-header>

      <section class="bg-[#0AA74A] text-white">
        <div class="px-4 md:px-8 py-5 flex items-center justify-between">
          <h1 class="text-2xl font-semibold">Primary School Leaving Examination (PSLE)</h1>
          <nav class="text-sm opacity-95">
            <a href="/" class="hover:underline">Home</a>
            <span class="mx-2">/</span>
            <span>PSLE</span>
          </nav>
        </div>
      </section>

      <main class="px-4 md:px-8 py-8 flex-1">
        <div class="max-w-screen-xl mx-auto">
          <div class="bg-white rounded-md border border-[#ecebea] p-6">
            <h2 class="text-xl font-semibold">The Examination Calendar</h2>
            <p class="text-[#4f4e4b] mt-2">PSLE is administered in the second week of September every year.</p>
            <h2 class="mt-6 text-xl font-semibold">The objectives of PSLE</h2>
            <p class="text-[#4f4e4b] mt-2">Assess learners’ knowledge and skills in primary subjects and their ability to apply basic skills in solving problems and making decisions.</p>
            <h2 class="mt-6 text-xl font-semibold">Eligibility for the Examination</h2>
            <p class="text-[#4f4e4b] mt-2">Any pupil who has completed standard seven in government or private schools is eligible to register and sit for this examination.</p>
          </div>
        </div>
      </main>

      <footer-section></footer-section>
    </div>
  </body>
</html>
