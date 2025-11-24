<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>RSMS • Results • {{ $examTitle }} • {{ $year }} • {{ $districtName }}</title>
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
          <span class="font-medium">{{ ucfirst(strtolower($districtName)) }}</span>
        </nav>
      </section>

      <main class="px-4 md:px-8 py-4 flex-1">
        <div class="max-w-screen-2xl mx-auto">
          <!-- Schools table -->
          <div class="bg-white rounded-lg border border-[#ecebea] shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full" id="schoolsTable">
                <thead class="bg-gradient-to-r from-[#0AA74A] to-[#0B6B3A] text-white">
                  <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">
                      <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                          <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                        </svg>
                        School Name
                      </div>
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider hidden md:table-cell">
                      <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                          <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                        District
                      </div>
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider hidden lg:table-cell">
                      <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                          <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                        </svg>
                        Region
                      </div>
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-medium uppercase tracking-wider">
                      Action
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-[#ecebea]">
                  @foreach($schools as $index => $s)
                    <tr class="hover:bg-gray-50 transition-colors duration-150 school-row">
                      <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                          <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-[#0AA74A]/10 to-[#0B6B3A]/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-[#0B6B3A]" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                            </svg>
                          </div>
                          <div>
                            <div class="text-sm font-medium text-gray-900 school-name">
                              {{ ucfirst(strtolower($s->name)) }}
                            </div>
                            <div class="text-xs text-gray-500 md:hidden">
                              {{ ucfirst(strtolower($districtName)) }} District
                            </div>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 text-sm text-gray-600 hidden md:table-cell">
                        {{ ucfirst(strtolower($districtName)) }}
                      </td>
                      <td class="px-6 py-4 text-sm text-gray-600 hidden lg:table-cell">
                        {{ ucfirst(strtolower($regionName)) }}
                      </td>
                      <td class="px-6 py-4 text-center">
                        <a href="/results/{{ strtolower($examTitle) }}/{{ $year }}/{{ urlencode(strtolower($regionName)) }}/{{ urlencode(strtolower($districtName)) }}/{{ urlencode(strtolower($s->name)) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#0AA74A] to-[#0B6B3A] text-white text-sm font-medium rounded-lg hover:shadow-lg transition-all duration-200 hover:scale-105">
                          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 5l7 7-7 7"/>
                          </svg>
                          View Results
                        </a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            
            @if($schools->isEmpty())
              <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                  <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No schools found</h3>
                <p class="text-gray-500">There are no schools available in this district.</p>
              </div>
            @endif
          </div>
        </div>
      </main>

      <footer-section></footer-section>
    </div>
  </body>
</html>
