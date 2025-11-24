<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>RSMS • Certificate of Secondary Education Examination (CSEE)</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-white text-[#1b1b18] min-h-screen w-full">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <app-header></app-header>

      <section class="bg-[#0AA74A] text-white">
        <div class="px-4 md:px-8 py-5 flex items-center justify-between">
          <h1 class="text-2xl font-semibold">Certificate of Secondary Education Examination (CSEE)</h1>
          <nav class="text-sm opacity-95">
            <a href="/" class="hover:underline">Home</a>
            <span class="mx-2">/</span>
            <span>CSEE</span>
          </nav>
        </div>
      </section>

      <main class="px-4 md:px-8 py-8 flex-1">
        <div class="max-w-screen-xl mx-auto">
          <div class="bg-white rounded-md border border-[#ecebea] p-6">
            <h2 class="text-xl font-semibold">The Examination Calendar</h2>
            <p class="text-[#4f4e4b] mt-2">CSEE is administered in the first week of November every year.</p>
            <h2 class="mt-6 text-xl font-semibold">The objectives of CSEE</h2>
            <p class="text-[#4f4e4b] mt-2">Assess knowledge and skills achieved at secondary school and the capacity to apply them to real-world challenges and further education.</p>
            <h2 class="mt-6 text-xl font-semibold">Eligibility of Candidates</h2>
            <p class="text-[#4f4e4b] mt-2">Students who completed four years of secondary education and passed FTNA or Qualifying Test. Private candidates are also eligible.</p>
          </div>
        </div>
      </main>

      <footer-section></footer-section>
    </div>
  </body>
</html>
