<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Exam • RSMS</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <site-header></site-header>
      <main class="flex-1 min-w-0 p-4 md:p-8">
        <div class="max-w-screen-lg mx-auto bg-white rounded-md border border-[#ecebea] p-6">
          <exam-page code="{{ $code }}"></exam-page>
        </div>
      </main>
      <site-footer></site-footer>
    </div>
  </body>
</html>
