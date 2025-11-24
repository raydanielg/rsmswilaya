<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>RSMS • Results • {{ $examTitle }} • {{ $year }} • {{ $schoolName }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <app-header></app-header>

      <section class="px-4 md:px-8 pt-4">
        <nav class="text-xs md:text-sm text-gray-600 bg-white border border-[#ecebea] rounded-md px-3 py-2 inline-flex items-center gap-2">
          <a href="/results/{{ strtolower($examTitle) }}" class="text-[#0B6B3A] hover:underline">{{ strtoupper($examTitle) }}</a>
          <span>/</span>
          <a href="/results/{{ strtolower($examTitle) }}/{{ $year }}" class="text-[#0B6B3A] hover:underline">{{ $year }}</a>
          <span>/</span>
          <a href="/results/{{ strtolower($examTitle) }}/{{ $year }}/{{ urlencode(strtolower($regionName)) }}" class="text-[#0B6B3A] hover:underline">{{ ucfirst(strtolower($regionName)) }}</a>
          <span>/</span>
          <a href="/results/{{ strtolower($examTitle) }}/{{ $year }}/{{ urlencode(strtolower($regionName)) }}/{{ urlencode(strtolower($districtName)) }}" class="text-[#0B6B3A] hover:underline">{{ ucfirst(strtolower($districtName)) }}</a>
          <span>/</span>
          <span class="font-medium">{{ ucfirst(strtolower($schoolName)) }}</span>
        </nav>
      </section>

      <main class="px-4 md:px-8 py-6 flex-1">
        <div class="max-w-screen-xl mx-auto">
          <div class="bg-white border border-[#ecebea] rounded-lg p-5 shadow-sm mb-6">
            <div class="flex items-center justify-between gap-4 flex-wrap">
              <div>
                <div class="text-2xl font-semibold">{{ ucfirst(strtolower($schoolName)) }}</div>
                <div class="text-sm text-gray-600">{{ strtoupper($examTitle) }} • {{ $year }} • {{ ucfirst(strtolower($districtName)) }}, {{ ucfirst(strtolower($regionName)) }}</div>
              </div>
            </div>
          </div>

          <div class="space-y-4">
            @forelse($types as $type)
              <details class="group bg-white border border-[#ecebea] rounded-lg shadow-sm">
                <summary class="flex items-center justify-between px-4 py-3 cursor-pointer select-none">
                  <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-lg bg-gradient-to-br from-[#0AA74A]/15 to-[#0B6B3A]/15 flex items-center justify-center">
                      <svg class="w-5 h-5 text-[#0B6B3A]" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 4h18v2H3V4zm0 7h18v2H3v-2zm0 7h18v2H3v-2z"/>
                      </svg>
                    </div>
                    <div>
                      <div class="font-medium">{{ $type['name'] }} <span class="text-xs text-gray-500">({{ $type['code'] }})</span></div>
                      <div class="text-xs text-gray-500">{{ count($type['docs']) }} document(s)</div>
                    </div>
                  </div>
                  <svg class="h-4 w-4 text-gray-500 transition-transform group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                </summary>
                <div class="px-4 pb-4">
                  <ul class="divide-y divide-[#ecebea]">
                    @foreach($type['docs'] as $idx => $d)
                      <li class="py-3 flex items-center justify-between gap-3">
                        <div class="text-sm text-gray-700">Document {{ $idx + 1 }}</div>
                        <div class="flex items-center gap-2">
                          <a href="{{ $d['url'] }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-1.5 text-sm rounded bg-[#0B6B3A] text-white hover:bg-[#095a31]">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5l7 7-7 7"/></svg>
                            View
                          </a>
                          <a href="{{ $d['url'] }}" target="_blank" download class="inline-flex items-center gap-2 px-3 py-1.5 text-sm rounded border border-[#0B6B3A] text-[#0B6B3A] hover:bg-[#0B6B3A] hover:text-white">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 3v12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M8 11l4 4 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 21h16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                            Download
                          </a>
                        </div>
                      </li>
                    @endforeach
                  </ul>
                </div>
              </details>
            @empty
              <div class="text-sm text-gray-600">No documents available for this school.</div>
            @endforelse
          </div>
        </div>
      </main>

      <footer-section></footer-section>
    </div>
  </body>
</html>
