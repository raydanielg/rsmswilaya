<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="maintenance-ends" content="{{ now()->addHours(2)->toIso8601String() }}" />
    <title>Maintenance • RSMS</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen">
    <div id="app">
      <maintenance-page></maintenance-page>
    </div>
  </body>
</html>
