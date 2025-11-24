<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $examTitle }} • {{ $year }} • {{ $regionName }} • {{ $districtName }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen">
    <div id="app">
      <app-header></app-header>
      <schools-list
        exam="{{ strtolower($examTitle) }}"
        year="{{ $year }}"
        region="{{ strtolower($regionName) }}"
        district="{{ strtolower($districtName) }}"
        region-label="{{ ucwords(strtolower($regionName)) }}"
        district-label="{{ ucwords(strtolower($districtName)) }}"
      />
      <footer-section></footer-section>
    </div>
  </body>
</html>
