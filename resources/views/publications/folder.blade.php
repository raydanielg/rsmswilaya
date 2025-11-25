<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Publications • {{ isset($folderTitle) ? $folderTitle : ($folder->name ?? 'Folder') }} • RSMS</title>
    @vite(['resources/css/app.css','resources/js/app.js','resources/js/pdf-download.js'])
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
            <a href="/publications" class="hover:underline">Publications</a>
            <span class="mx-2">/</span>
            <span>{{ isset($folderTitle) ? $folderTitle : ($folder->name ?? '') }}</span>
          </nav>
        </div>
      </section>

      <main class="px-4 md:px-8 py-8 flex-1">
        <div class="max-w-screen-xl mx-auto">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-xs font-semibold text-[#6b6a67]">PUBLICATIONS / {{ strtoupper(isset($folderTitle) ? $folderTitle : ($folder->name ?? '')) }}</h2>
            <a href="/publications" class="text-sm text-[#0B6B3A] hover:underline">← Back</a>
          </div>
          <ul class="divide-y divide-[#ecebea] bg-white rounded-md border border-[#ecebea]">
            @foreach($docs as $doc)
              <li>
                <div class="flex items-center gap-3 px-4 py-3">
                  <svg class="w-4 h-4 text-[#6b6a67]" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Z"/><path d="M14 2v6h6" fill="#ffffff22"/></svg>
                  <a href="{{ route('pdf.viewer', ['src' => $doc['url']]) }}" class="flex-1 hover:underline text-[#0B6B3A] font-medium" target="_blank">{{ $doc['name'] }}</a>
                  <div class="hidden md:block w-32 text-right text-[#6b6a67]">{{ \Carbon\Carbon::parse($doc['date'])->format('M d, Y') }}</div>
                  <div class="hidden md:block w-24 text-right text-[#6b6a67]">{{ $doc['size'] }}</div>
                  <a href="{{ route('pdf.viewer', ['src' => $doc['url']]) }}" target="_blank" class="text-[#0B6B3A] hover:text-[#095a31] mr-2" title="View">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  </a>
                  <a href="{{ $doc['download_url'] }}" download="{{ $doc['name'] }}" class="text-[#0B6B3A] hover:text-[#095a31]" title="Download">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 3v12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M8 11l4 4 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 21h16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                  </a>
                </div>
              </li>
            @endforeach
          </ul>
        </div>
      </main>

      <footer-section></footer-section>
    </div>
  </body>
</html>
